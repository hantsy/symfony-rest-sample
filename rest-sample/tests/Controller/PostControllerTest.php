<?php

namespace App\Tests\Controller;

use App\Controller\Dto\CreatePostDto;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PostControllerTest extends WebTestCase
{
    public function testGetAllPosts(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/posts');

        $this->assertResponseIsSuccessful();

        //
        $response = $client->getResponse();
        $data = $response->getContent();
        //dump($data);
        $this->assertStringContainsString("Symfony and PHP", $data);
    }

    public function testCreatePost(): void
    {
        $client = static::createClient();
        $data = CreatePostDto::of("test title", "test content");
        $crawler = $client->request(
            'POST',
            '/posts',
            [],
            [],
            [],
            $this->getContainer()->get('serializer')->serialize($data, 'json')
        );

        $this->assertResponseIsSuccessful();

        $response = $client->getResponse();
        $url = $response->headers->get('Location');
        //dump($data);
        $this->assertNotNull($url);
        $this->assertStringStartsWith("/posts/", $url);
    }
}
