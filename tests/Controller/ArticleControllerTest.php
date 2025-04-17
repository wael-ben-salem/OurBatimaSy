<?php

namespace App\Tests\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ArticleControllerTest extends WebTestCase{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $articleRepository;
    private string $path = '/article/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->articleRepository = $this->manager->getRepository(Article::class);

        foreach ($this->articleRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Article index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'article[nom]' => 'Testing',
            'article[description]' => 'Testing',
            'article[prixUnitaire]' => 'Testing',
            'article[photo]' => 'Testing',
            'article[etapeprojet]' => 'Testing',
            'article[stock]' => 'Testing',
            'article[fournisseur]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->articleRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Article();
        $fixture->setNom('My Title');
        $fixture->setDescription('My Title');
        $fixture->setPrixUnitaire('My Title');
        $fixture->setPhoto('My Title');
        $fixture->setEtapeprojet('My Title');
        $fixture->setStock('My Title');
        $fixture->setFournisseur('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Article');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Article();
        $fixture->setNom('Value');
        $fixture->setDescription('Value');
        $fixture->setPrixUnitaire('Value');
        $fixture->setPhoto('Value');
        $fixture->setEtapeprojet('Value');
        $fixture->setStock('Value');
        $fixture->setFournisseur('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'article[nom]' => 'Something New',
            'article[description]' => 'Something New',
            'article[prixUnitaire]' => 'Something New',
            'article[photo]' => 'Something New',
            'article[etapeprojet]' => 'Something New',
            'article[stock]' => 'Something New',
            'article[fournisseur]' => 'Something New',
        ]);

        self::assertResponseRedirects('/article/');

        $fixture = $this->articleRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getNom());
        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('Something New', $fixture[0]->getPrixUnitaire());
        self::assertSame('Something New', $fixture[0]->getPhoto());
        self::assertSame('Something New', $fixture[0]->getEtapeprojet());
        self::assertSame('Something New', $fixture[0]->getStock());
        self::assertSame('Something New', $fixture[0]->getFournisseur());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Article();
        $fixture->setNom('Value');
        $fixture->setDescription('Value');
        $fixture->setPrixUnitaire('Value');
        $fixture->setPhoto('Value');
        $fixture->setEtapeprojet('Value');
        $fixture->setStock('Value');
        $fixture->setFournisseur('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/article/');
        self::assertSame(0, $this->articleRepository->count([]));
    }
}
