# Data Modeling

We are modeling a simple blog system, it includes the following concepts.

* A `Post` presents an article post in the blog system.
* A `Comment` presents the comments under a specific post.
* The common `Tag` can be applied on different posts,  which categorizes posts by topic, categories, etc.

You can draft your model relations in your mind or draw it in graph data modeling tools, such as draw.io, etc.  

* Post and comments is a `one-to-many` relation
* Post and tag is a `many-to-many` relation

It is easy to convert the idea to real codes via Doctrine `Entity`.  

## An Entity Example

A Doctrine entity is very similar to the JPA `Entity` class.

```php
#[Entity()]
class Post
{
    #[Id]
    #[GeneratedValue(strategy: "AUTO")
	#[Column(type: "integer")]
    private ?int $id;

    #[Column(type: "string", length: 255)]
    private string $title;

    #[Column(type: "string", length: 255)]
    private string $content;
}
```

An entity is annotated with `Entity` attribute, optionally you can setup another `Table` attribute to configure backed table metadata.

An entity should contain an identifier field, annotated wtih a `Id` attribute. Together with `Id`, the `GeneratedValue` is used to identifier generation before inserting into database tables.

A `Column` attribute is used to configure the column metadata in the backed table.


## Identifier Generation Strategy

The `GeneratedValue` attribute contains an optional parameter  `strategy ` which is used to set the name of identifier generation strategy. Valid values are `AUTO`, `SEQUENCE`, `IDENTITY`, `UUID`   (deprecated), `CUSTOM` and `NONE`.   If not specified, the default value is `AUTO`.

Since Doctrine ORM 2.10.0 and Dbal 3.0,  the deprecated `UUID` strategy does not work.  We will switch to the `CUSTOM` strategy and the `UuidGenerator` form `symfony\uid`.

Install `symfony\uid` firstly.

```bash
$ composer require symfony/uid
```
Use the following instead of the legacy UUID strategy.

```php
#[GeneratedValue(strategy: "CUSTOM")]
#[CustomIdGenerator(class: UuidGenerator::class)]
#[Column(type: "uuid", unique: true)]
private ?Uuid $id = null;
```

Next, let's create `Post`, `Comment` and `Tag` entities.

## Creating Entities

Run the following command, and follow the interactive steps to create `Post`, `Comment` and `Tag` one by one.

```bash
$ php bin/console make:entity
```

Finally we got three entities in the *src/Entity* folder. 

Change them as you expected.

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

Note, the `one-to-many` and `many-to-many` relations we use a  `Collection` and initialize a `ArrayCollection` in the constructor to maintain the relations. The `mappedBy`, `targetEntity`, `cascade`, `orphanRemoval`, and `fetch` are easy if you have used Hibernate/JPA before. More details, please read the  [OneToMany](https://www.doctrine-project.org/projects/doctrine-orm/en/2.11/reference/attributes-reference.html#attrref_onetomany) and [ManyToMany](https://www.doctrine-project.org/projects/doctrine-orm/en/2.11/reference/attributes-reference.html#manytomany) section in the Doctrine attribute reference.

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

You can use Doctrine migration to maintain database schema in development and production environments.

## Doctrine Migration

Run the following command to generate a *Migration* file.

```bash
$ php bin/console make:migration
```

Finally a Migration file is generated in the */migrations* folder, the naming is like `Version20211104031420`.  It is a simple class extended `AbstractMigration`, there is a `up` function is used to upgrade to this version and the `down` function is used to downgrade to the previous version. 

There are some commands to apply these *migrations* scripts on database automatically.

```bash
# migrate to the latest
$ php bin/console doctrine:migrations:migrate

# return to prev version
$ php bin/console doctrine:migrations:migrate prev

# migrate to next
$ php bin/console doctrine:migrations:migrate next

# These alias are defined : first, latest, prev, current and next

# certain version fully qualified class name
$ php bin/console doctrine:migrations:migrate FQCN
```

Doctrine bundle also includes some command to maintain database and schema. eg. 

```bash
# create and drop database
$ php bin/console doctrine:database:create
$ php bin/console doctrine:database:drop

# schema create, drop, update and validate
$ php bin/console doctrine:schema:create
$ php bin/console doctrine:schema:drop
$ php bin/console doctrine:schema:update
$ php bin/console doctrine:schema:validate
```
As you see, these commands are used to execute small tasks, esp,  performing some administration tasks. You can create your own command.

