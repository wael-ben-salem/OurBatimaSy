<?php

namespace App\Tests\Controller;

use App\Entity\Terrain;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class TerrainControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $terrainRepository;
    private string $path = '/terrain/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->terrainRepository = $this->manager->getRepository(Terrain::class);

        foreach ($this->terrainRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Terrain index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'terrain[emplacement]' => 'Testing',
            'terrain[caracteristiques]' => 'Testing',
            'terrain[superficie]' => 'Testing',
            'terrain[detailsgeo]' => 'Testing',
            'terrain[idProjet]' => 'Testing',
            'terrain[idVisite]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->terrainRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Terrain();
        $fixture->setEmplacement('My Title');
        $fixture->setCaracteristiques('My Title');
        $fixture->setSuperficie('My Title');
        $fixture->setDetailsgeo('My Title');
        $fixture->setIdProjet('My Title');
        $fixture->setIdVisite('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Terrain');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Terrain();
        $fixture->setEmplacement('Value');
        $fixture->setCaracteristiques('Value');
        $fixture->setSuperficie('Value');
        $fixture->setDetailsgeo('Value');
        $fixture->setIdProjet('Value');
        $fixture->setIdVisite('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'terrain[emplacement]' => 'Something New',
            'terrain[caracteristiques]' => 'Something New',
            'terrain[superficie]' => 'Something New',
            'terrain[detailsgeo]' => 'Something New',
            'terrain[idProjet]' => 'Something New',
            'terrain[idVisite]' => 'Something New',
        ]);

        self::assertResponseRedirects('/terrain/');

        $fixture = $this->terrainRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getEmplacement());
        self::assertSame('Something New', $fixture[0]->getCaracteristiques());
        self::assertSame('Something New', $fixture[0]->getSuperficie());
        self::assertSame('Something New', $fixture[0]->getDetailsgeo());
        self::assertSame('Something New', $fixture[0]->getIdProjet());
        self::assertSame('Something New', $fixture[0]->getIdVisite());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Terrain();
        $fixture->setEmplacement('Value');
        $fixture->setCaracteristiques('Value');
        $fixture->setSuperficie('Value');
        $fixture->setDetailsgeo('Value');
        $fixture->setIdProjet('Value');
        $fixture->setIdVisite('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/terrain/');
        self::assertSame(0, $this->terrainRepository->count([]));
    }
}
