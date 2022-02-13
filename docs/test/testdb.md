### Testing Repository



Run the following command to install PHPUnit and Symfony **test-pack**.  The **test-pack** will install all essential packages for testing Symfony components and  add PHPUnit configuration, such as *phpunit.xml.dist*.

```bash
# composer require --dev phpunit/phpunit symfony/test-pack
```

An simple test example written in pure PHPUnit.

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

The following is an example of  testing  a `Repository` - `PostRepository`. The  `KernelTestCase` contains facilities to bootstrap application kernel and provides service container.

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

In the above codes, in the `setUp` function, boot up the application kernel, after it is booted, a test scoped *Service Container* is available.    Then get `EntityManagerInterface` and `PostRepository` from service container.

In the  `testCreatePost`  function, persists a `Post` entity, and find this post by id and verify the *title* and *content* fields.

> Currently, PHPUnit does not include PHP 8 Attribute support, the testing codes are similar to the legacy JUnit 4 code style.