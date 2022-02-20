# Data Operations

Similar to JPA/Hibernate, Symfony Doctrine bundle provides basic CRUD operations  and flexible criteria builder to create complex queries.

## `ObjectManager` and `ObjectRepository` 

In Doctrine common package, there are two core components available for data persistence purpose.

* `ObjectManager` is the interface mainly provided for the end user that used  to find, persist, update and delete objects.
* `ObjectRepository` is used to retrieve instances by more flexible queries. 

Doctrine ORM provides derived class of `ObjectManager` - `EntityManagerInerface` and its implementation class. It also provides a subclass of `ObjectRepository` -  `EntityRepository`.

> Doctrine also includes a Doctrine ODM package for MongoDB, it uses the same interfaces from the Common package to perform data operations.

Symfony integrates Doctrine tightly  and makes these components work in its service container.

* There is a `EntityManagerInterface`  in the service container , you can inject it in any injectable components, such as `Controller`, `Command`, etc.
* Symfony adds an extra `EntityServiceRepository` which extends from `EntityRepository`.  All the generated repositories by *make bundle* are derived from the `EntityServiceRepository` class. It allows you to access the service container in your own `Repository`.
* In your own `Repository`, you can create complex queries by `QueryBuilder` API.

The `ObjectManager` and `ObjectRepository` provide interoperating methods. 

* There is a `ObjectManager.getRepository` method to locate the specific `Repository` that targets the `Entity` argument.
* And there is `getEntityManager` method available in your own `Repository`  to retrieve the instance of `EntityManagerInterface`.


## Creating Command

To demonstrate the usage of `EntityManagerInterfce` ,  we will generate a command firstly, then  inject `EntityManagerInterfce` via constructor to insert sample data into database. 

Run the following command to generate a `Command` from the built-in templates.

```bash
$ php bin/console make:command add-post
```

It will generate a `AddPostCommand` under *src/Command* folder. 

Change it as expected.

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

The Doctrine  `EntityManagerInterface` is managed by Symfony *Service Container*. The `persist` method is used to persist entities, and `flush` to commit the transaction.

To test if it work as expected, run the following command to execute the newly created `add-post` command.

```bash
# php bin/console app:add-post "test title" "test content"
 ! [NOTE] Title: test title                                               
 ! [NOTE] Content: test content                                                             
 [OK] Post is saved: Post: [ id =1ec3d3ec-895d-685a-b712-955865f6c134, title=test title, content=test content, createdAt=1636010040, blishedAt=] 
```
As you see, it is exectued successfully. It will add a new `Post` into database. 

