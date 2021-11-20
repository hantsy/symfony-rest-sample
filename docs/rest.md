<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->
**Table of Contents**  *generated with [DocToc](https://github.com/thlorenz/doctoc)*

- [Building Restful APIs with Symfony 5 and PHP 8](#building-restful-apis-with-symfony-5-and-php-8)
  - [Get your feet wet](#get-your-feet-wet)
  - [Hello , Symfony](#hello--symfony)
  - [Connecting to Database](#connecting-to-database)
  - [Building Data Models](#building-data-models)
    - [Adding Sample Data](#adding-sample-data)
    - [Testing Repository](#testing-repository)
  - [Exposing RESTful APIs](#exposing-restful-apis)
    - [Testing Controller](#testing-controller)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->

# Building Restful APIs with Symfony 5 and PHP 8

Symfony is a full-featured modularized PHP framework which is used for building all kinds of applications, from traditional web applications to the small Microservice components.



## Get your feet wet

Install PHP 8 and PHP Composer tools.

```bash
# choco php composer
```

Install [Symfony CLI](symfony check:requirements), check the system requirements. 

```bash
# symfony check:requirements

Symfony Requirements Checker
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

> PHP is using the following php.ini file:
C:\tools\php80\php.ini

> Checking Symfony requirements:

....................WWW.........

                                              
 [OK]                                         
 Your system is ready to run Symfony projects 
                                              

Optional recommendations to improve your setup
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

 * intl extension should be available
   > Install and enable the intl extension (used for validators).

 * a PHP accelerator should be installed
   > Install and/or enable a PHP accelerator (highly recommended).

 * realpath_cache_size should be at least 5M in php.ini
   > Setting "realpath_cache_size" to e.g. "5242880" or "5M" in
   > php.ini* may improve performance on Windows significantly in some
   > cases.


Note  The command console can use a different php.ini file
~~~~  than the one used by your web server.
      Please check that both the console and the web server
      are using the same PHP version and configuration.

```

According to the *recommendations* info, adjust your PHP configuration in the *php.ini*.  And we will use Postgres as database in the sample application, make sure `pdo_pgsql` and `pgsql` modules are enabled.

Finally, you can confirm the enabled modules by the following command.

```bash
# php -m
```

Create a new Symfony project. 

```bash
# symfony new rest-sample

// a classic website application
# symfony new web-sample --full
```

By default, it will create a simple Symfony skeleton project only with core kernel configuration, which is good to start a lightweight Restful API application.

Alternatively, you can create it using Composer. 

```bash
# composer create-project symfony/skeleton rest-sample

//start a classic website application
# composer create-project symfony/website-skeleton web-sample
```

Enter the generated project root folder, start the application.

```bash
# symfony server:start

 [WARNING] run "symfony.exe server:ca:install" first if you want to run the web server with TLS support, or use "--no-  
 tls" to avoid this warning                                                                                             
                                                                                                                       
Tailing PHP-CGI log file (C:\Users\hantsy\.symfony\log\499d60b14521d4842ba7ebfce0861130efe66158\79ca75f9e90b4126a5955a33ea6a41ec5e854698.log)
Tailing Web Server log file (C:\Users\hantsy\.symfony\log\499d60b14521d4842ba7ebfce0861130efe66158.log)
                                                                                                                        
 [OK] Web server listening                                                                                              
      The Web server is using PHP CGI 8.0.10                                                                            
      http://127.0.0.1:8000                                                                                             
                                                                                                                        

[Web Server ] Oct  4 13:33:01 |DEBUG  | PHP    Reloading PHP versions
[Web Server ] Oct  4 13:33:01 |DEBUG  | PHP    Using PHP version 8.0.10 (from default version in $PATH)
[Web Server ] Oct  4 13:33:01 |INFO   | PHP    listening path="C:\\tools\\php80\\php-cgi.exe" php="8.0.10" port=61738

```



## Hello , Symfony

Create a simple class to a resource entity in the HTTP response.

```php
class Post
{
    private ?string $id = null;

    private string $title;

    private string $content;
    
    //getters and setters.
}
```

And use a factory to create a new Post instance.

```php
class PostFactory
{
    public static function create(string $title, string $content): Post
    {
        $post = new Post();
        $post->setTitle($title);
        $post->setContent($content);
        return $post;
    }
}
```

Let's create a simple Controller class.  

To use the newest PHP 8 attributes to configure the routing rules, apply the following changes in the project configurations.

* Open *config/packages/doctrine.yaml*,  remove  `doctrine/orm/mapping/App/type` or change its value to `attribute`
* Open *composer.json*, change PHP version to `>=8.0.0`.

To render the response body into a JSON string,  use a `JsonReponse` to wrap the response. 

```bash 
#[Route(path: "/posts", name: "posts_")]
class PostController
{

    #[Route(path: "", name: "all", methods: ["GET"])]
    function all(): Response
    {
        $post1 = PostFactory::create("test title", "test content");
        $post1->setId("1");

        $post2 = PostFactory::create("test title", "test content");
        $post2->setId("2");
        $data = [$post1->asArray(), $post2->asArray()];
        return new JsonResponse($data, 200, ["Content-Type" => "application/json"]);
        //return $this->json($data, 200, ["Content-Type" => "application/json"]);
    }
}    
```

 The first parameter of `JsonReponse` accepts an array as data, so add a function in the `Post` class to archive this purpose.

```php
class Post{
    //...
    public function asArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content
        ];
    }
}
```

Run the application, use `curl` to test the `/posts` endpoint.

```bash
# curl http://localhost:8000/posts
```

Symfony provides a simple `AbstractController` which includes several functions to simplfy the response and adopt the container and dependency injection management. 

In the above controller, extends from `AbstractController`, simply call `$this->json` to render the response in JSON format, no need to transform the data to an array before rendering response.

```php

class PostController extends AbstractController
{

    function all(): Response
    {
        //...
        return $this->json($data, 200, ["Content-Type" => "application/json"]);
    }
}  
```



## Connecting to Database 

Doctrine is a popular ORM framework ,  it is highly inspired by the existing Java ORM tooling, such as JPA spec and Hibernate framework. There are two core components in Doctrine,  `doctrine/dbal` and `doctrine/orm`, the former is a low level APIs for database operations, if you know Java development, consider it as the *Jdbc* layer. The later is the advanced ORM framework, the public APIs are similar to JPA/Hibernate. 

Install Doctrine into the project.

```bash
# composer require symfony/orm-pack
# composer require --dev symfony/maker-bundle
```

The **pack** is a virtual Symfony package, it will install a series of packages and basic configurations.

Open the `.env` file in the project root folder, edit the `DATABASE_URL` value, setup the database name, username, password to connect.

```properties
DATABASE_URL="postgresql://user:password@127.0.0.1:5432/blogdb?serverVersion=13&charset=utf8"
```

Use the following command to generate a docker compose file template.

```bash
# php bin/console make:docker:database
```

We change it to the following to start up a Postgres  database in development.

```yaml
version: "3.5" # specify docker-compose version, v3.5 is compatible with docker 17.12.0+

# Define the services/containers to be run
services:

  postgres:
    image: postgres:${POSTGRES_VERSION:-13}-alpine
    ports:
      - "5432:5432"
    environment:
      POSTGRES_DB: ${POSTGRES_DB:-blogdb}
      # You should definitely change the password in production
      POSTGRES_PASSWORD: ${POSTGRES_PASSWORD:-password}
      POSTGRES_USER: ${POSTGRES_USER:-user}
    volumes:
      - ./data/blogdb:/var/lib/postgresql/data:rw
      - ./pg-initdb.d:/docker-entrypoint-initdb.d
```

We will  use `UUID` as data type of the primary key, add a script to enable `uuid-ossp` extension in Postgres when it is starting up.

```sql
-- file: pg-initdb.d/ini.sql
SET search_path TO public;
DROP EXTENSION IF EXISTS "uuid-ossp";
CREATE EXTENSION "uuid-ossp" SCHEMA public;
```

Open *config/packages/test/doctrine.yaml*, comment out `dbname_suffix` line.  We use Docker container to bootstrap a database to ensure the application behaviors are same between the development and production.

Now startup the application and make sure there is no exception in the console, that means the database connection is successful.

```bash
symfony server:start
```

Before starting the application, make sure the database is running.  Run the following command to start up the Postgres in Docker.

```bash
# docker compose up postgres
# docker ps -a # to list all containers and make the postgres is running
```



## Building Data Models

Now we will build the Entities that will be used in the next sections.  We are modeling a simple blog system, it includes the following concepts.

* A `Post` presents an article post in the blog system.
* A `Comment` presents the comments under a specific post.
* The common `Tag` can be applied on different posts,  which categorizes posts by topic, categories , etc.

You can draft your model relations in mind or through some graphic data modeling tools.  

* Post and  comments is a `one-to-many` relation
* Post and tag is a `many-to-many` relation

It is easy to convert the idea to real codes via Doctrine `Entity`.   Run the following command to create `Post`, `Comment` and `Tag` entities.

In the Doctrine ORM 2.10.x and Dbal 3.x, the UUID type ID generator is deprecated.  We will switch to the Uuid form `symfony\uid`.

Install `symfony\uid` firstly.

```bash
# composer require symfony/uid
```
Simply, you can use the following command to create entities quickly.

```bash
# php bin/console make:entity  # following the interactive steps to create them one by one.
```

Finally we got three entities in the *src/Entity* folder. Modify them as you expected.

```php
// src/Entity/Post.php
#[Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[Id]
    //#[GeneratedValue(strategy: "UUID")
    //#[Column(type: "string", unique: true)]
    #[Column(type: "uuid", unique: true)]
    #[GeneratedValue(strategy: "CUSTOM")]
    #[CustomIdGenerator(class: UuidGenerator::class)]
    private ?Uuid $id = null;

    #[Column(type: "string", length: 255)]
    private string $title;

    #[Column(type: "string", length: 255)]
    private string $content;

    #[Column(name: "created_at", type: "datetime", nullable: true)]
    private DateTime|null $createdAt = null;

    #[Column(name: "published_at", type: "datetime", nullable: true)]
    private DateTime|null $publishedAt = null;

    #[OneToMany(mappedBy: "post", targetEntity: Comment::class, cascade: ['persist', 'merge', "remove"], fetch: 'LAZY', orphanRemoval: true)]
    private Collection $comments;

    #[ManyToMany(targetEntity: Tag::class, mappedBy: "posts", cascade: ['persist', 'merge'], fetch: 'EAGER')]
    private Collection $tags;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->comments = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }
    //other getters and setters
}

// src/Entity/Comment.php
#[Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[Id]
    //#[GeneratedValue(strategy: "UUID")]
    #[Column(type: "uuid", unique: true)]
    #[GeneratedValue(strategy: "CUSTOM")]
    #[CustomIdGenerator(class: UuidGenerator::class)]
    private ?Uuid $id = null;

    #[Column(type: "string", length: 255)]
    private string $content;

    #[Column(name: "created_at", type: "datetime", nullable: true)]
    private DateTime|null $createdAt = null;

    #[ManyToOne(targetEntity: "Post", inversedBy: "comments")]
    #[JoinColumn(name: "post_id", referencedColumnName: "id")]
    private Post $post;

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }
    //other getters and setters
}

//src/Entity/Tag.php
#[Entity(repositoryClass: TagRepository::class)]
class Tag
{
    #[Id]
    //#[GeneratedValue(strategy: "UUID")
    //#[Column(type: "string", unique: true)]
    #[Column(type: "uuid", unique: true)]
    #[GeneratedValue(strategy: "CUSTOM")]
    #[CustomIdGenerator(class: UuidGenerator::class)]
    private ?Uuid $id = null;

    #[Column(type: "string", length: 255)]
    private ?string $name;

    #[ManyToMany(targetEntity: Post::class, inversedBy: "tags")]
    private Collection $posts;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }
}
```

At the same time, it generated three `Repository` classes for these entities.

```php
// src/Repository/PostRepsoitory.php
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }
}

// src/Repository/CommentRepsoitory.php
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }
}

//src/Repository/TagRepository.php
class TagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }
}
```

You can use Doctrine migration to generate a *Migration* file to maintain database schema in a production environment.

Run the following command to generate a *Migration* file.

```bash
# php bin/console make:migration
```

After it is executed, a Migration file is generated in the *migrations* folder, its naming is like `Version20211104031420`.  It is a simple class extended `AbstractMigration`, the `up` function is use for upgrade to this version and `down` function is use for downgrade to the previous version. 

To apply Migrations on database automaticially.

```bash
# php bin/console doctrine:migrations:migrate

# return to prev version
# php bin/console doctrine:migrations:migrate prev

# migrate to next
# php bin/console doctrine:migrations:migrate next

# These alias are defined : first, latest, prev, current and next

# certain version fully qualified class name
# php bin/console doctrine:migrations:migrate FQCN
```

Doctrine bundle also includes some command to maintain database and schema. eg. 

```bash
# php bin/console doctrine:database:create
# php bin/console doctrine:database:drop

// schema create, drop, update and validate
# php bin/console doctrine:schema:create
# php bin/console doctrine:schema:drop
# php bin/console doctrine:schema:update
# php bin/console doctrine:schema:validate
```



### Adding Sample Data

Create a custom command to load some sample data.

```bash
# php bin/console make:command add-post
```

It will generate a `AddPostCommand` under *src/Command* folder.

```php
#[AsCommand(
    name: 'app:add-post',
    description: 'Add a short description for your command',
)]
class AddPostCommand extends Command
{


    public function __construct(private EntityManagerInterface $manager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('title', InputArgument::REQUIRED, 'Title of a post')
            ->addArgument('content', InputArgument::REQUIRED, 'Content of a post')
            //->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $title = $input->getArgument('title');

        if ($title) {
            $io->note(sprintf('Title: %s', $title));
        }

        $content = $input->getArgument('content');

        if ($content) {
            $io->note(sprintf('Content: %s', $content));
        }

        $entity = PostFactory::create($title, $content);
        $this ->manager->persist($entity);
        $this ->manager->flush();

//        if ($input->getOption('option1')) {
//            // ...
//        }

        $io->success('Post is saved: '.$entity);

        return Command::SUCCESS;
    }
}
```

The Doctrine  `EntityManagerInterface` is managed by Symfony *Service Container*,  and use for data persistence operations. 

Run the following command to add a post into the database.

```bash
# php bin/console app:add-post "test title" "test content"
 ! [NOTE] Title: test title                                               
 ! [NOTE] Content: test content                                                             
 [OK] Post is saved: Post: [ id =1ec3d3ec-895d-685a-b712-955865f6c134, title=test title, content=test content, createdAt=1636010040, blishedAt=] 
```

### Testing Repository

[PHPUnit](https://phpunit.de) is the most popular testing framework in PHP world, Symfony integrates PHPUnit tightly. 

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

## Creating PostController:  Exposing your first Rest API

Similar to other MVC framework, we can expose RESTful APIs via Symfony `Controller` component.  Follow  the REST convention, we are planning to create the following APIs to a blog system.

* `GET  /posts`    Get all posts.
* `GET /posts/{id}` Get a single post by ID, if not found, return status 404 
* `POST /posts` Create a new post from  request body, add the new post URI to response header `Location`, and return status 201
* `DELETE /posts/{id}` Delete a single post by ID, return status 204. If the post was  not found, return status 404 instead.
* ...

Run the following command to create a Controller skeleton. Follow the interactive guide to create a controller named `PostController`.

```bash
# php bin/console make:constroller
```

Open *src/Controller/PostController.php* in IDE.

Add `Route` attribute on class level and two functions: one for fetching all posts and another for getting single post by ID.

```bash
#[Route(path: "/posts", name: "posts_")]
class PostController extends AbstractController
{
    public function __construct(private PostRepository      $posts)
    {
    }

    #[Route(path: "", name: "all", methods: ["GET"])]
    function all(): Response
    {
        $data = $this->posts->findAll();
        return $this->json($data);
    }
    
}
```

Start up the application, and try to access the *http://localhost:8000/posts*, it will throw a circular dependencies exception when rendering the models in JSON view directly. There are some solutions to avoid this, the simplest is break the bi-direction relations before rendering the JSON view.  Add a `Ignore` attribute on `Comment.post` and `Tag.posts`.

```php
//src/Entity/Comment.php
class Comment
{
    #[Ignore]
    private Post $post;
}

//src/Entity/Tag.php
class Tag
{
    #[Ignore]
    private Collection $posts;
}
```



### Testing Controller

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

### Paginating Result

There are a lot of web applications which provide a input field for typing keyword and paginating the search results. Assume there is a *keyword* provided by request to match Post *title* or *content* fields, a  *offset* to set the offset position of the pagination, and a *limit* to set the limited size of the elements per page. Create  a function in the `PostRepository`, accepts a *keyword*, *offset* and *limit* as arguments.

```php
public function findByKeyword(string $q, int $offset = 0, int $limit = 20): Page
{
    $query = $this->createQueryBuilder("p")
        ->andWhere("p.title like :q or p.content like :q")
        ->setParameter('q', "%" . $q . "%")
        ->orderBy('p.createdAt', 'DESC')
        ->setMaxResults($limit)
        ->setFirstResult($offset)
        ->getQuery();

    $paginator = new Paginator($query, $fetchJoinCollection = false);
    $c = count($paginator);
    $content = new ArrayCollection();
    foreach ($paginator as $post) {
        $content->add(PostSummaryDto::of($post->getId(), $post->getTitle()));
    }
    return Page::of ($content, $c, $offset, $limit);
}
```

Firstly, create a dynamic query using `createQueryBuilder` ,  then create a Doctrine `Paginator` instance to execute the query. The `Paginator` implements `Countable` interface,  use `count` to get the count of total elements. Finally, we use a custom `Page` object to wrap the result.

```php
class Page
{
    private Collection $content;
    private int $totalElements;
    private int $offset;
    private int $limit;

    #[Pure] public function __construct()
    {
        $this->content = new ArrayCollection();
    }


    public static function of(Collection $content, int $totalElements, int $offset = 0, int $limit = 20): Page
    {
        $page = new Page();
        $page->setContent($content)
            ->setTotalElements($totalElements)
            ->setOffset($offset)
            ->setLimit($limit);

        return $page;
    }
    
    //
    //getters

}    
```

### Customzing ArgumentResolver

In the `PostController` , create a new function to serve the route `/posts`, but it accepts query parameters like */posts?q=Symfony&offset=0&limit=10*, and make the parameters are optional.

```php
    #[Route(path: "", name: "all", methods: ["GET"])]
    function all(Request $req): Response
    {
        $keyword = $req->query->get('q');
        $offset = $req->query->get('offset');
        $limit = $req->query->get('limit');
        
        $data = $this->posts->findByKeyword($keyword || '', $offset, $limit);
        return $this->json($data);
    }
```

It works but the query parameters can not be handled as the route path parameters.  We can create a custom `ArgumentResolver` to resolve the bound the arguments.

Create an attribute to identify a query parameter that need to be resolved by a `ArgumentResolver`.

```php
#[Attribute(Attribute::TARGET_PARAMETER)]
final class QueryParam
{
    private null|string $name;
    private bool $required;

    /**
     * @param string|null $name
     * @param bool $required
     */
    public function __construct(?string $name = null, bool $required = false)
    {
        $this->name = $name;
        $this->required = $required;
    }
    
    //getters and setters
    
}    
```

Create a custom `ArgumentResolver` implements the built-in `ArgugmentResolverInterface`.

```php
class QueryParamValueResolver implements ArgumentValueResolverInterface, LoggerAwareInterface
{
    public function __construct()
    {
    }

    private LoggerInterface $logger;

    /**
     * @inheritDoc
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $argumentName = $argument->getName();
        $this->logger->info("Found [QueryParam] annotation/attribute on argument '" . $argumentName . "', applying [QueryParamValueResolver]");
        $type = $argument->getType();
        $nullable = $argument->isNullable();
        $this->logger->debug("The method argument type: '" . $type . "' and nullable: '" . $nullable . "'");

        //read name property from QueryParam
        $attr = $argument->getAttributes(QueryParam::class)[0];// `QueryParam` is not repeatable
        $this->logger->debug("QueryParam:" . $attr);
        //if name property is not set in `QueryParam`, use the argument name instead.
        $name = $attr->getName() ?? $argumentName;
        $required = $attr->isRequired() ?? false;
        $this->logger->debug("Polished QueryParam values: name='" . $name . "', required='" . $required . "'");

        //fetch query name from request
        $value = $request->query->get($name);
        $this->logger->debug("The request query parameter value: '" . $value . "'");

        //if default value is set and query param value is not set, use default value instead.
        if (!$value && $argument->hasDefaultValue()) {
            $value = $argument->getDefaultValue();
            $this->logger->debug("After set default value: '" . $value . "'");
        }

        if ($required && !$value) {
            throw new \InvalidArgumentException("Request query parameter '" . $name . "' is required, but not set.");
        }

        $this->logger->debug("final resolved value: '" . $value . "'");
        
        //must return  a `yield` clause
        yield match ($type) {
            'int' => $value ? (int)$value : 0,
            'float' => $value ? (float)$value : .0,
            'bool' => (bool)$value,
            'string' => $value ? (string)$value : ($nullable ? null : ''),
            null => null
        };
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        $attrs = $argument->getAttributes(QueryParam::class);
        return count($attrs) > 0;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
```

 At runtime, it calls the `supports` function to check it the current request satisfy the requirement, if it is ok, then invoke the `resovle` funtion.

In the `supports` function, we check if the argument is annotated with a `QueryParam`, if it is existed, then resolved the argument from request query.

##  Get Post by ID 

Follow the design in the previous section, add another function to `PostController` to map route `/posts/{id}` . 

```bash
class PostController extends AbstractController
{
	//other functions...

    #[Route(path: "/{id}", name: "byId", methods: ["GET"])]
    function getById(Uuid $id): Response
    {
        $data = $this->posts->findOneBy(["id" => $id]);
        if ($data) {
            return $this->json($data);
        } else {
            return $this->json(["error" => "Post was not found by id:" . $id], 404);
        }
    }
}
```

  Run the application, and try to access *http://localhost:8000/posts/{id}*, it will throw an exception like this.

```bash
App\Controller\PostController::getById(): Argument #1 ($id) must be of type Symfony\Component\Uid\Uuid, string given, cal
led in D:\hantsylabs\symfony5-sample\rest-sample\vendor\symfony\http-kernel\HttpKernel.php on line 156

```

The `id` in the URI is a string,  can not be  used  as `Uuid`  directly.

Symfony provides `ParamConverter` to convert the request attributes to the target type. We can create a custom `ParamConverter` to archive the purpose.

### Customizing ParamConverter 

Create a  new class `UuidParamCovnerter` under *src/Request/* folder.

```php
class UuidParamConverter implements ParamConverterInterface
{
    public function __construct(private LoggerInterface $logger)
    {
    }


    /**
     * @inheritDoc
     */
    public function apply(Request $request, ParamConverter $configuration): bool
    {

        $param = $configuration->getName();

        if (!$request->attributes->has($param)) {
            return false;
        }

        $value = $request->attributes->get($param);
        $this->logger->info("parameter value:" . $value);
        if (!$value && $configuration->isOptional()) {
            $request->attributes->set($param, null);

            return true;
        }

        $data = Uuid::fromString($value);
        $request->attributes->set($param, $data);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function supports(ParamConverter $configuration): bool
    {
        $className = $configuration->getClass();
        $this->logger->info("converting to UUID :{c}", ["c" => $className]);
        return $className && $className == Uuid::class;
    }
}
```



In the above codes, 

* The `supports` function to check the execution environment if matching the requirements
* The `apply` function to perform the conversion. if `supports` returns false, this conversion step will be skipped.
* 

