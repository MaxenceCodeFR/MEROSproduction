<?php

namespace App\Tests\Entity;

use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Entity\Blog;
use Doctrine\ORM\EntityManager;

class BlogEntityTest extends KernelTestCase
{
    private EntityManager $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws NotSupported
     */
    public function testSearchByTitle()
    {
        // Création et persistance de l'entité Blog
        $blog = new Blog();
        $blog->setTitle('Bonjour');
        $blog->setContent('Maxence, du latin Maximus, qui signifie le plus grand');
        $blog->setCreatedAt(new \DateTimeImmutable());
        $blog->setIsArchived(false);

        $this->entityManager->persist($blog);
        $this->entityManager->flush();

        // Récupération du repository
        $blogRepository = $this->entityManager->getRepository(Blog::class);

        // Recherche des blogs par titre
        $results = $blogRepository->searchByTitle('Bonjour');

        // Assertions
        $this->assertCount(1, $results, 'Un seul blog doit être retourné');
        $this->assertEquals('Bonjour', $results[0]->getTitle(), 'Le titre du blog doit être Bonjour');

        // Cleanup

            $this->entityManager->remove($blog);
            $this->entityManager->flush();
            $this->entityManager->clear();

    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();


    }
}
