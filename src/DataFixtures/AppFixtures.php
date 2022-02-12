<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\PostFactory;
use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $data = PostFactory::create("Building Restful APIs with Symfony and PHP 8", "test content");
        $data->addTag(Tag::of( "Symfony"))
            ->addTag( Tag::of("PHP 8"))
            ->addComment(Comment::of("test comment 1"))
            ->addComment(Comment::of("test comment 2"));

        $manager->persist($data);
        $manager->flush();
    }
}
