<?php

namespace App\DataFixtures;

use App\Entity\PostFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $data = PostFactory::create("Building Restful APIs with Symfony and PHP 8", "test content");
        $manager->persist($data);
        $manager->flush();
    }
}
