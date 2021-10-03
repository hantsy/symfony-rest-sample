<?php

namespace App\Tests\Repository;

use App\Controller\Dto\CreatePostDto;
use App\Entity\PostFactory;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PostRepositoryTest extends KernelTestCase
{

    private EntityManager $entityManager;

    private PostRepository $postRepository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->assertSame('test', $kernel->getEnvironment());
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->postRepository = $this->getContainer()
            ->get(PostRepository::class);
    }

    public function testCreatePost(): void
    {
        $entity = PostFactory::create("test post", "test content");
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
        $this->assertNotNull($entity->getId());

        $byId = $this->postRepository->findOneBy(["id" => $entity->getId()]);
        $this->assertEquals("test post", $byId->getTitle());
        $this->assertEquals("test content", $byId->getContent());
    }

}
