# Data Modeling

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

