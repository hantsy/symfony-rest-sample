<?php

namespace App\Tests\Controller;

use App\Dto\CreatePostDto;
use App\Entity\Post;
use App\Entity\Status;
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
        //var_dump("url:::".$url);
        $this->assertNotNull($url);
        $this->assertStringStartsWith("/posts", $url);


        // 2 get the newly created post.
        $client->jsonRequest(
            'GET',
            $url,
        );

        $getByIdResponse = $client->getResponse();
        echo("json response:::" . $getByIdResponse->getContent());
        $getData = $this->getContainer()->get('serializer')->deserialize($getByIdResponse->getContent(), Post::class, "json");
        $this->assertEquals("test title", $getData->getTitle());
        $this->assertEquals("test content", $getData->getContent());
        $this->assertEquals(Status::Draft, $getData->getStatus());
        $this->assertNotNull($getData->getCreatedAt());
    }
}
