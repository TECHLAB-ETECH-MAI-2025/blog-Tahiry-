<?php

namespace App\Tests\Controller;

use App\Entity\Commentaire;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class CommentaireControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $commentaireRepository;
    private string $path = '/commentaire/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->commentaireRepository = $this->manager->getRepository(Commentaire::class);

        foreach ($this->commentaireRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Commentaire index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'commentaire[author]' => 'Testing',
            'commentaire[content]' => 'Testing',
            'commentaire[createdAt]' => 'Testing',
            'commentaire[article]' => 'Testing',
            'commentaire[Article]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->commentaireRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Commentaire();
        $fixture->setAuthor('My Title');
        $fixture->setContent('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setArticle('My Title');
        $fixture->setArticle('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Commentaire');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Commentaire();
        $fixture->setAuthor('Value');
        $fixture->setContent('Value');
        $fixture->setCreatedAt('Value');
        $fixture->setArticle('Value');
        $fixture->setArticle('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'commentaire[author]' => 'Something New',
            'commentaire[content]' => 'Something New',
            'commentaire[createdAt]' => 'Something New',
            'commentaire[article]' => 'Something New',
            'commentaire[Article]' => 'Something New',
        ]);

        self::assertResponseRedirects('/commentaire/');

        $fixture = $this->commentaireRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getAuthor());
        self::assertSame('Something New', $fixture[0]->getContent());
        self::assertSame('Something New', $fixture[0]->getCreatedAt());
        self::assertSame('Something New', $fixture[0]->getArticle());
        self::assertSame('Something New', $fixture[0]->getArticle());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Commentaire();
        $fixture->setAuthor('Value');
        $fixture->setContent('Value');
        $fixture->setCreatedAt('Value');
        $fixture->setArticle('Value');
        $fixture->setArticle('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/commentaire/');
        self::assertSame(0, $this->commentaireRepository->count([]));
    }
}
