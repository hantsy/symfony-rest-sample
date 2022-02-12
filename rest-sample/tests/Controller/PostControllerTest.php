<?php

namespace App\Tests\Controller;

use App\Dto\CreateCommentDto;
use App\Dto\CreatePostDto;
use App\Dto\UpdatePostDto;
use App\Dto\UpdatePostStatusDto;
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

        // 1. create a new post
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
        $this->assertResponseStatusCodeSame(201);
        $response = $client->getResponse();
        $url = $response->headers->get('Location');
        //var_dump("url:::".$url);
        $this->assertNotNull($url);
        $this->assertStringStartsWith("/posts", $url);


        // 2. get the newly created post.
        $client->jsonRequest('GET', $url,);

        $getByIdResponse = $client->getResponse();
        echo("json response:::" . $getByIdResponse->getContent());
        $getData = $this->getContainer()->get('serializer')->deserialize($getByIdResponse->getContent(), Post::class, "json");
        $this->assertEquals("test title", $getData->getTitle());
        $this->assertEquals("test content", $getData->getContent());
        $this->assertEquals(Status::Draft, $getData->getStatus());
        $this->assertNotNull($getData->getCreatedAt());

        // 3. update the existing post.
        $updateData = UpdatePostDto::of("title update", "content update");
        $client->request(
            'PUT',
            $url,
            [],
            [],
            ["CONTENT_TYPE" => "application/json"],
            $this->getContainer()->get('serializer')->serialize($updateData, 'json')
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(204);

        // 4. verify the updated post.
        $client->jsonRequest('GET', $url,);

        $updatedResponse = $client->getResponse();
        echo("json response of updated post:::" . $updatedResponse->getContent());
        $updatedData = $this->getContainer()->get('serializer')->deserialize($updatedResponse->getContent(), Post::class, "json");
        $this->assertEquals("title update", $updatedData->getTitle());
        $this->assertEquals("content update", $updatedData->getContent());

        // 5. update the post status
        $updateStatusData = UpdatePostStatusDto::of(Status::PendingModerated);
        $client->request(
            'PUT',
            $url . "/status",
            [],
            [],
            ["CONTENT_TYPE" => "application/json"],
            $this->getContainer()->get('serializer')->serialize($updateStatusData, 'json')
        );
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(204);

        // 5. verify the updated post status.
        $client->jsonRequest('GET', $url,);
        $updatedStatusResponse = $client->getResponse();
        echo("json response of updated post status:::" . $updatedStatusResponse->getContent());
        $updatedStatusData = $this->getContainer()->get('serializer')->deserialize($updatedStatusResponse->getContent(), Post::class, "json");
        $this->assertEquals("title update", $updatedStatusData->getTitle());
        $this->assertEquals("content update", $updatedStatusData->getContent());
        $this->assertEquals(Status::PendingModerated, $updatedStatusData->getStatus());

        // 6. add comment
        $commentData = CreateCommentDto::of("test comment");
        $crawler = $client->request(
            'POST',
            $url . "/comments",
            [],
            [],
            ["CONTENT_TYPE" => "application/json"],
            $this->getContainer()->get('serializer')->serialize($commentData, 'json')
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(201);

        // 7. get comments
        $client->jsonRequest('GET', $url . "/comments");
        $commentsResponse = $client->getResponse();
        echo("json response of comments:::" . $commentsResponse->getContent());
        $this->assertResponseStatusCodeSame(200);
        $this->assertStringContainsString("test comment", $commentsResponse->getContent());

        // 8. delete the post
        $client->jsonRequest("DELETE", $url);
        $client->getResponse();
        $this->assertResponseStatusCodeSame(204);

        // 9. verify the post is deleted.
        $client->jsonRequest('GET', $url,);
        $this->assertResponseStatusCodeSame(404);

    }
}
