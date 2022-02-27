# Testing Repository

Symfony provides a *test-pack* to simplify the configuration for running tests. 

## Installing Testing Tools

Run the following command to install PHPUnit and Symfony **test-pack**.  
The **test-pack** will install all essential packages for testing Symfony components and add PHPUnit configuration, such as *phpunit.xml.dist*.

An simple test example written in PHPUnit.

```bash
class PostTest extends TestCase
{

    public function testPost()
    {
        $p = PostFactory::create("tests title", "tests content");

        $this->assertEquals("tests title", $p->getTitle());
        $this->assertEquals("tests content", $p->getContent());
        $this->assertNotNull( $p->getCreatedAt());
    }
}
```

Symfony provides some specific base classes(`KernelTestCase`, `WebTestCase`, etc.) to simplfy the testing work in a Symfony project.

## Testing  PostRepository

Create a  test class  `PostRepositoryTest`, make it extends from `KernelTestCase`. 

```php
class PostRepositoryTest extends KernelTestCase
{

    private EntityManagerInterface $entityManager;

    private PostRepository $postRepository;

    protected function setUp(): void
    {
        //(1) boot the Symfony kernel
        $kernel = self::bootKernel();
        $this->assertSame('test', $kernel->getEnvironment());
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        //(2) use static::getContainer() to access the service container
        $container = static::getContainer();

        //(3) get PostRepository from container.
        $this->postRepository = $container->get(PostRepository::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
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
```

The `KernelTestCase` contains all facilities to bootstrap the application kernel and provides service container in tests.

In the above codes, there is a `setUp` function which is used to prepare the testing environment before running the tests.

In this function, it boots up the application kernel. After it is booted, a test scoped *Service Container* is available, you can fetch the managed service in the container.

Here we retrieve `EntityManagerInterface` and `PostRepository` from service container.

In the `testCreatePost` function, it persists a `Post` entity firstly, then find this post by id and verify the *title* and *content* fields.

> Currently, PHPUnit does not include PHP 8 Attribute support, the testing codes looks like the legacy JUnit 4 code style.