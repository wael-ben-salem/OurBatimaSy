<?php

namespace App\Tests\Controller;

use App\Entity\Projet;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ProjetControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $projetRepository;
    private string $path = '/projet/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->projetRepository = $this->manager->getRepository(Projet::class);

        foreach ($this->projetRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Projet index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'projet[type]' => 'Testing',
            'projet[stylearch]' => 'Testing',
            'projet[budget]' => 'Testing',
            'projet[etat]' => 'Testing',
            'projet[datecreation]' => 'Testing',
            'projet[nomprojet]' => 'Testing',
            'projet[idTerrain]' => 'Testing',
            'projet[idEquipe]' => 'Testing',
            'projet[idClient]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->projetRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Projet();
        $fixture->setType('My Title');
        $fixture->setStylearch('My Title');
        $fixture->setBudget('My Title');
        $fixture->setEtat('My Title');
        $fixture->setDatecreation('My Title');
        $fixture->setNomprojet('My Title');
        $fixture->setIdTerrain('My Title');
        $fixture->setIdEquipe('My Title');
        $fixture->setIdClient('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Projet');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Projet();
        $fixture->setType('Value');
        $fixture->setStylearch('Value');
        $fixture->setBudget('Value');
        $fixture->setEtat('Value');
        $fixture->setDatecreation('Value');
        $fixture->setNomprojet('Value');
        $fixture->setIdTerrain('Value');
        $fixture->setIdEquipe('Value');
        $fixture->setIdClient('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'projet[type]' => 'Something New',
            'projet[stylearch]' => 'Something New',
            'projet[budget]' => 'Something New',
            'projet[etat]' => 'Something New',
            'projet[datecreation]' => 'Something New',
            'projet[nomprojet]' => 'Something New',
            'projet[idTerrain]' => 'Something New',
            'projet[idEquipe]' => 'Something New',
            'projet[idClient]' => 'Something New',
        ]);

        self::assertResponseRedirects('/projet/');

        $fixture = $this->projetRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getType());
        self::assertSame('Something New', $fixture[0]->getStylearch());
        self::assertSame('Something New', $fixture[0]->getBudget());
        self::assertSame('Something New', $fixture[0]->getEtat());
        self::assertSame('Something New', $fixture[0]->getDatecreation());
        self::assertSame('Something New', $fixture[0]->getNomprojet());
        self::assertSame('Something New', $fixture[0]->getIdTerrain());
        self::assertSame('Something New', $fixture[0]->getIdEquipe());
        self::assertSame('Something New', $fixture[0]->getIdClient());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Projet();
        $fixture->setType('Value');
        $fixture->setStylearch('Value');
        $fixture->setBudget('Value');
        $fixture->setEtat('Value');
        $fixture->setDatecreation('Value');
        $fixture->setNomprojet('Value');
        $fixture->setIdTerrain('Value');
        $fixture->setIdEquipe('Value');
        $fixture->setIdClient('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/projet/');
        self::assertSame(0, $this->projetRepository->count([]));
    }
}
