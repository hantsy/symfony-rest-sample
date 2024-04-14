<?php

namespace App\DataFixtures;

use App\Entity\ApiToken;
use App\Entity\Comment;
use App\Entity\PostFactory;
use App\Entity\Tag;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    public function __construct(
        private readonly UserPasswordHasherInterface $encoder
    ) {
    }
    public function load(ObjectManager $manager): void
    {
        $data = PostFactory::create("Building Restful APIs with Symfony and PHP 8", "test content");
        $data->addTag(Tag::of( "Symfony"))
            ->addTag( Tag::of("PHP 8"))
            ->addComment(Comment::of("test comment 1"))
            ->addComment(Comment::of("test comment 2"));

        $manager->persist($data);
        $manager->flush();

        $data2 = PostFactory::create("Building Restful APIs with Laravel and PHP 7", "test content");
        $data2->addTag(Tag::of( "Laravel"))
            ->addTag( Tag::of("PHP 7"))
            ->addComment(Comment::of("test comment 1"))
            ->addComment(Comment::of("test comment 2"));

        $manager->persist($data2);
        $manager->flush();

        $user1 = new User();
        $user1->setUsername('test')
            ->setPassword($this->encoder->hashPassword($user1, 'test'))
            ->setRoles(['ROLE_API']);
        $manager->persist($user1);
        $manager->flush();

        $apiToken = new ApiToken();
        $apiToken
            ->setUser($user1)
            ->setToken(
                hash('sha256', '42e18f5761d40076b1804412e81ffa3fcc15f1dd31aa00532d3ca32a1e0cc16c')
            );
        $manager->persist($apiToken);
        $manager->flush();
    }
}
