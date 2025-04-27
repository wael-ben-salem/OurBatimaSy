<?php

namespace App\Controller;

use App\Entity\Etapeprojet;
use App\Form\EtapeprojetType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;
use App\Helper\CalendarHelper; 


#[Route('/etapeprojet')]
final class EtapeprojetController extends AbstractController
{
    #[Route(name: 'app_etapeprojet_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, PaginatorInterface $paginator, Request $request): Response
    {
        $query = $entityManager
            ->getRepository(Etapeprojet::class)
            ->createQueryBuilder('e')
            ->leftJoin('e.idProjet', 'p') 
            ->addSelect('p') 
            ->getQuery();
    
        $etapeprojets = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            9
        );
    
        return $this->render('etapeprojet/index.html.twig', [
            'etapeprojets' => $etapeprojets,
        ]);
    }

    #[Route('/new', name: 'app_etapeprojet_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $etapeprojet = new Etapeprojet();
        $form = $this->createForm(EtapeprojetType::class, $etapeprojet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($etapeprojet);
            $entityManager->flush();

            return $this->redirectToRoute('app_etapeprojet_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('etapeprojet/new.html.twig', [
            'etapeprojet' => $etapeprojet,
            'form' => $form,
        ]);
    }

    #[Route('/calendar/{year}/{month}', name: 'app_etapeprojet_calendar', methods: ['GET'])]
    public function calendar(EntityManagerInterface $entityManager, ?int $year = null, ?int $month = null): Response
    {
        // Set current month/year if not provided
        $currentYear = $year ?? (int)date('Y');
        $currentMonth = $month ?? (int)date('m');
        
        // Validate month range
        $currentMonth = max(1, min(12, $currentMonth));
        
        // Calculate previous and next month for navigation
        $prevMonth = $currentMonth - 1;
        $prevYear = $currentYear;
        if ($prevMonth < 1) {
            $prevMonth = 12;
            $prevYear--;
        }
        
        $nextMonth = $currentMonth + 1;
        $nextYear = $currentYear;
        if ($nextMonth > 12) {
            $nextMonth = 1;
            $nextYear++;
        }
        
        // Get all etapes
        $etapes = $entityManager
            ->getRepository(Etapeprojet::class)
            ->findAll();
        
        // Prepare events for the calendar
        $events = [];
        foreach ($etapes as $etape) {
            if ($etape->getDatedebut()) {
                $events[] = [
                    'id' => $etape->getIdEtapeprojet(),
                    'title' => $etape->getNometape(),
                    'date' => $etape->getDatedebut(),
                    'endDate' => $etape->getDatefin(),
                    'status' => $etape->getStatut(),
                    'url' => $this->generateUrl('app_etapeprojet_show', ['idEtapeprojet' => $etape->getIdEtapeprojet()])
                ];
            }
        }
        
        // Generate calendar structure
        $calendarHelper = new CalendarHelper();
        $calendar = $calendarHelper->generateCalendar($currentYear, $currentMonth, $events);
        
        return $this->render('etapeprojet/calendar_php.html.twig', [
            'calendar' => $calendar,
            'month' => $currentMonth,
            'year' => $currentYear,
            'monthName' => strftime('%B', mktime(0, 0, 0, $currentMonth, 1, $currentYear)),
            'prevMonth' => $prevMonth,
            'prevYear' => $prevYear,
            'nextMonth' => $nextMonth,
            'nextYear' => $nextYear,
            'events' => $events
        ]);
    }

    #[Route('/{idEtapeprojet}', name: 'app_etapeprojet_show', methods: ['GET'])]
    public function show(?Etapeprojet $etapeprojet = null): Response
    {
        if (!$etapeprojet) {
            throw $this->createNotFoundException('Étape non trouvée');
        }
        
        return $this->render('etapeprojet/show.html.twig', [
            'etapeprojet' => $etapeprojet,
        ]);
    }

    #[Route('/{idEtapeprojet}/edit', name: 'app_etapeprojet_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Etapeprojet $etapeprojet, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EtapeprojetType::class, $etapeprojet);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $entityManager->flush();
                $this->addFlash('success', 'L\'étape du projet a été mise à jour avec succès.');
                return $this->redirectToRoute('app_etapeprojet_show', ['idEtapeprojet' => $etapeprojet->getIdEtapeprojet()]);
            } else {
                $this->addFlash('error', 'Veuillez vérifier à nouveau les champs.');
            }
        }

        return $this->render('etapeprojet/edit.html.twig', [
            'etapeprojet' => $etapeprojet,
            'form' => $form->createView(), 
        ]);
    }


    #[Route('/{idEtapeprojet}', name: 'app_etapeprojet_delete', methods: ['POST'])]
    public function delete(Request $request, Etapeprojet $etapeprojet, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$etapeprojet->getIdEtapeprojet(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($etapeprojet);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_etapeprojet_index', [], Response::HTTP_SEE_OTHER);
    }

}