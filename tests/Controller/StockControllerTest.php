<?php

namespace App\Tests\Controller;

use App\Entity\Stock;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class StockControllerTest extends WebTestCase{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $stockRepository;
    private string $path = '/stock/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->stockRepository = $this->manager->getRepository(Stock::class);

        foreach ($this->stockRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Stock index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'stock[nom]' => 'Testing',
            'stock[emplacement]' => 'Testing',
            'stock[datecreation]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->stockRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Stock();
        $fixture->setNom('My Title');
        $fixture->setEmplacement('My Title');
        $fixture->setDatecreation('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Stock');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Stock();
        $fixture->setNom('Value');
        $fixture->setEmplacement('Value');
        $fixture->setDatecreation('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'stock[nom]' => 'Something New',
            'stock[emplacement]' => 'Something New',
            'stock[datecreation]' => 'Something New',
        ]);

        self::assertResponseRedirects('/stock/');

        $fixture = $this->stockRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getNom());
        self::assertSame('Something New', $fixture[0]->getEmplacement());
        self::assertSame('Something New', $fixture[0]->getDatecreation());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Stock();
        $fixture->setNom('Value');
        $fixture->setEmplacement('Value');
        $fixture->setDatecreation('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/stock/');
        self::assertSame(0, $this->stockRepository->count([]));
    }
}
