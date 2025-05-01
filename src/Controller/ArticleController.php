<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/article')]
final class ArticleController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private HttpClientInterface $httpClient;

    public function __construct(EntityManagerInterface $entityManager, HttpClientInterface $httpClient)
    {
        $this->entityManager = $entityManager;
        $this->httpClient = $httpClient;
    }

    private function logArticleHistory(string $action, Article $article): void
    {
        $filesystem = new Filesystem();
        $historyFile = $this->getParameter('kernel.project_dir') . '/var/article_history.json';

        $history = [];
        if ($filesystem->exists($historyFile)) {
            $history = json_decode(file_get_contents($historyFile), true) ?? [];
        }

        $history[] = [
            'action' => $action,
            'article_id' => $article->getId(),
            'article_name' => $article->getNom(),
            'timestamp' => (new \DateTime())->format('Y-m-d H:i:s'),
        ];

        file_put_contents($historyFile, json_encode($history, JSON_PRETTY_PRINT));
    }

    #[Route(name: 'app_article_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, Request $request, PaginatorInterface $paginator): Response
    {
        $locale = $request->query->get('locale', 'fr');
        $request->setLocale($locale);

        $queryBuilder = $entityManager->getRepository(Article::class)
            ->createQueryBuilder('a')
            ->orderBy('a.id', 'ASC');

        $articles = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            3
        );

        $repository = $entityManager->getRepository(Article::class);
        $totalArticles = $repository->count([]);

        $distributionByStock = $repository->createQueryBuilder('a')
            ->select('s.nom AS stockName, COUNT(a.id) AS count')
            ->leftJoin('a.stock', 's')
            ->groupBy('s.id')
            ->getQuery()
            ->getResult();

        $distributionByEtapeProjet = $repository->createQueryBuilder('a')
            ->select('e.nometape AS etapeName, COUNT(a.id) AS count')
            ->leftJoin('a.etapeprojet', 'e')
            ->groupBy('a.etapeprojet')
            ->getQuery()
            ->getResult();

        $distributionByFournisseur = $repository->createQueryBuilder('a')
            ->select('f.nom AS fournisseurName, COUNT(a.id) AS count')
            ->leftJoin('a.fournisseur', 'f')
            ->groupBy('a.fournisseur')
            ->getQuery()
            ->getResult();

        $historyFile = $this->getParameter('kernel.project_dir') . '/var/article_history.json';
        $history = json_decode(file_get_contents($historyFile), true) ?? [];

        // Fetch exchange rate from TND to EUR
        $response = $this->httpClient->request('GET', 'https://api.exchangerate-api.com/v4/latest/TND');
        $exchangeRate = $response->toArray()['rates']['EUR'] ?? 0;

        // Add converted prices to articles
        foreach ($articles as $article) {
            $article->priceInEuro = $article->getPrixUnitaire() * $exchangeRate;
        }

        return $this->render('article/index.html.twig', [
            'articles' => $articles,
            'totalArticles' => $totalArticles,
            'distributionByStock' => $distributionByStock,
            'distributionByEtapeProjet' => $distributionByEtapeProjet,
            'distributionByFournisseur' => $distributionByFournisseur,
            'articleHistory' => $history,
            'exchangeRate' => $exchangeRate,
        ]);
    }

    #[Route('/new', name: 'app_article_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photoFile = $form->get('photoFile')->getData();

            if ($photoFile) {
                $uploadsDir = $this->getParameter('uploads_directory');
                $fileName = uniqid() . '.' . $photoFile->guessExtension();
                $photoFile->move($uploadsDir, $fileName);
                $article->setPhoto('/uploads/' . $fileName);
            }

            $this->entityManager->persist($article);
            $this->entityManager->flush();

            $this->logArticleHistory('created', $article);

            return $this->redirectToRoute('app_article_index');
        }

        return $this->render('article/new.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_article_show', methods: ['GET'])]
    public function show($id, EntityManagerInterface $entityManager): Response
    {
        $this->addFlash('info', "Attempting to find Article with ID: $id");
        $id = (int) $id;

        $article = $entityManager->getRepository(Article::class)->find($id);

        if (!$article) {
            $this->addFlash('error', 'Article not found.');
            return $this->redirectToRoute('app_article_index');
        }

        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_article_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photoFile = $form->get('photoFile')->getData();

            if ($photoFile) {
                $uploadsDir = $this->getParameter('uploads_directory');
                $fileName = uniqid() . '.' . $photoFile->guessExtension();
                $photoFile->move($uploadsDir, $fileName);
                $article->setPhoto('/uploads/' . $fileName);
            }

            $this->entityManager->flush();

            $this->logArticleHistory('updated', $article);

            return $this->redirectToRoute('app_article_index');
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_article_delete', methods: ['POST'])]
    public function delete(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $article->getId(), $request->getPayload()->getString('_token'))) {
            $this->logArticleHistory('deleted', $article);
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/toggle-bot', name: 'app_toggle_bot', methods: ['POST'])]
    public function toggleBot(Request $request): JsonResponse
    {
        $activeFile = $this->getParameter('kernel.project_dir') . '/templates/article/active.txt';
        $currentState = trim(file_get_contents($activeFile));
        $newState = ($currentState === 'true') ? 'false' : 'true';
        file_put_contents($activeFile, $newState);

        return new JsonResponse(['status' => $newState]);
    }
}