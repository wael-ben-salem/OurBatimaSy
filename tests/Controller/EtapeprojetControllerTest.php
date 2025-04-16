<?php

namespace App\Tests\Controller;

use App\Entity\Etapeprojet;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class EtapeprojetControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $etapeprojetRepository;
    private string $path = '/etapeprojet/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->etapeprojetRepository = $this->manager->getRepository(Etapeprojet::class);

        foreach ($this->etapeprojetRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Etapeprojet index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'etapeprojet[nometape]' => 'Testing',
            'etapeprojet[description]' => 'Testing',
            'etapeprojet[datedebut]' => 'Testing',
            'etapeprojet[datefin]' => 'Testing',
            'etapeprojet[statut]' => 'Testing',
            'etapeprojet[montant]' => 'Testing',
            'etapeprojet[idProjet]' => 'Testing',
            'etapeprojet[idRapport]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->etapeprojetRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Etapeprojet();
        $fixture->setNometape('My Title');
        $fixture->setDescription('My Title');
        $fixture->setDatedebut('My Title');
        $fixture->setDatefin('My Title');
        $fixture->setStatut('My Title');
        $fixture->setMontant('My Title');
        $fixture->setIdProjet('My Title');
        $fixture->setIdRapport('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Etapeprojet');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Etapeprojet();
        $fixture->setNometape('Value');
        $fixture->setDescription('Value');
        $fixture->setDatedebut('Value');
        $fixture->setDatefin('Value');
        $fixture->setStatut('Value');
        $fixture->setMontant('Value');
        $fixture->setIdProjet('Value');
        $fixture->setIdRapport('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'etapeprojet[nometape]' => 'Something New',
            'etapeprojet[description]' => 'Something New',
            'etapeprojet[datedebut]' => 'Something New',
            'etapeprojet[datefin]' => 'Something New',
            'etapeprojet[statut]' => 'Something New',
            'etapeprojet[montant]' => 'Something New',
            'etapeprojet[idProjet]' => 'Something New',
            'etapeprojet[idRapport]' => 'Something New',
        ]);

        self::assertResponseRedirects('/etapeprojet/');

        $fixture = $this->etapeprojetRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getNometape());
        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('Something New', $fixture[0]->getDatedebut());
        self::assertSame('Something New', $fixture[0]->getDatefin());
        self::assertSame('Something New', $fixture[0]->getStatut());
        self::assertSame('Something New', $fixture[0]->getMontant());
        self::assertSame('Something New', $fixture[0]->getIdProjet());
        self::assertSame('Something New', $fixture[0]->getIdRapport());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Etapeprojet();
        $fixture->setNometape('Value');
        $fixture->setDescription('Value');
        $fixture->setDatedebut('Value');
        $fixture->setDatefin('Value');
        $fixture->setStatut('Value');
        $fixture->setMontant('Value');
        $fixture->setIdProjet('Value');
        $fixture->setIdRapport('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/etapeprojet/');
        self::assertSame(0, $this->etapeprojetRepository->count([]));
    }
}
