# Testing Controller

As described in the previous sections,  to test Controller/API, create a test class to extend `WebTestCase`, which provides a plenty of  facilities to handle request and assert response.

Run the following command to create a test skeleton.

```bash
# php bin/console make:test
```

Follow the interactive steps to create a test base on `WebTestCase`.

```php
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

}
```

If you try to run the test, it will fail. At the moment, there is no any data for testing.

### Preparing Data for Testing Purpose

The `doctrine/doctrine-fixtures-bundle` is use for populate sample data for testing purpose, and `dama/doctrine-test-bundle` ensures the data is restored before evey test is running.

Install `doctrine/doctrine-fixtures-bundle` and `dama/doctrine-test-bundle`.

```bash
composer require --dev doctrine/doctrine-fixtures-bundle dama/doctrine-test-bundle
```

Create a new `Fixture`. 

```bash 
# php bin/console make:fixtures
```

In the `load` fucntion, persist some data for tests.

```php
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
```

Run the command to load the sample data into database manually.

```bash
# php bin/console doctrine:fixtures:load 
```

Add the following extension configuration into the `phpunit.xml.dist`,  thus the data will be purged and recreated for every test running.

```xml
<extensions>
    <extension class="DAMA\DoctrineTestBundle\PHPUnit\PHPUnitExtension"/>
</extensions>
```

Run the following command to execute `PostControllerTest.php` .

```bash 
# php .\vendor\bin\phpunit .\tests\Controller\PostControllerTest.php
```

Create a test function to verify in the `PostControllerTest` file.

```php
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
```

Add a test to verify if the post is not found and get a 404 status code.

```php
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
```
