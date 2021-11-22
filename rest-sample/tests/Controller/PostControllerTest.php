<?php

namespace App\Tests\Controller;

use App\Dto\CreatePostDto;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Uid\Uuid;

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

    public function testGetANoneExistingPost(): void
    {
        $client = static::createClient();
        $id = Uuid::v4();
        $crawler = $client->request('GET', '/posts/' . $id);

        //
        $response = $client->getResponse();
        $this->assertResponseStatusCodeSame(404);
        $data = $response->getContent();
        $this->assertStringContainsString("Post #" . $id . " was not found", $data);
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
            ["CONTENT_TYPE" => "application/json"],
            $this->getContainer()->get('serializer')->serialize($data, 'json')
        );

        $this->assertResponseIsSuccessful();

        $response = $client->getResponse();
        $url = $response->headers->get('Location');
        //dump($data);
        $this->assertNotNull($url);
        $this->assertStringStartsWith("/posts", $url);
    }
}
