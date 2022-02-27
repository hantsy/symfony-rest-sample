## Using Enum in Symfony

PHP 8.1 introduces the official *Enum* support.   [Doctrine brought Enum type support in its ORM framework](https://www.doctrine-project.org/2022/01/11/orm-2.11.html),  and [Symfony added serialization and deserialization support of a Enum type](https://symfony.com/blog/new-in-symfony-5-4-php-enumerations-support).  

It is time to migrate your projects to use PHP Enum if you are using 3rd-party enumeration solutions.

To use PHP *Enum*, you have to upgrade to PHP 8.1, and set the PHP version to `8.1`  in the project composer file.

```json
{
    //...
    "require": {
        "php": ">=8.1",
        //...
    }
}
```



## Creating Enum Class

For example, we will add a `Status` to the `Post` entity, and defined several fixed values of the post status.

```php
<?php

namespace App\Entity;

enum Status: string
{
    case Draft = "DRAFT";
    case PendingModerated = "PENDING_MODERATED";
    case Published = "PUBLISHED";
}
```

Here we use a *string* backed enum, add a field in the `Post` class.

```php
#[Column(type: "string", enumType: Status::class)]
private Status $status;
```

Note, set the *enumType* as the `Status` class.  It will store the status value as a string in the database tables.

In the `Post` constructor, assign a default value to the status.

```php
public function __construct()
{
    $this->status = Status::Draft;
    //...
}
```

Now everything is ok. 

## Creating HttpMethod

 When we setup the `Route` attribute on the Controller class,  we use a literal value to set up the HTTP method.

```php
#[Route(path: "/{id}", name: "byId", methods: ["GET"])]
```

For the *methods* value, there are only several options available to choose. Obviously, if introducing *Enum*, it will provide a *type-safe* way to  setup the values and decrease the typo errors.

Create an Enum named `HttpMethod`.

```php
<?php

namespace App\Annotation;

enum HttpMethod
{
    case GET;
    case POST;
    case HEAD;
    case OPTIONS;
    case PATCH;
    case PUT;
    case DELETE;
}
```

Then refactor the `Route` attribute and create a series of attributes(`Get`, `Post`, `Put`, `Delete`, etc.) that are mapped to different HTTP methods.

```php
//file : src/Annotation/Get.php
#[Attribute]
class Get extends Route
{
    public function getMethods()
    {
        return [HttpMethod::GET->name];
    }

}

//file : src/Annotation/Head.php
#[Attribute]
class Head extends Route
{
    public function getMethods()
    {
        return [HttpMethod::HEAD->name];
    }

}

//file : src/Annotation/Options.php
#[Attribute]
class Options extends Route
{
    public function getMethods()
    {
        return [HttpMethod::OPTIONS->name];
    }

}

//file : src/Annotation/Patch.php
#[Attribute]
class Patch extends Route
{
    public function getMethods()
    {
        return [HttpMethod::PATCH->name];
    }
}

//file : src/Annotation/Post.php
#[Attribute]
class Post extends Route
{
    public function getMethods()
    {
        return [HttpMethod::POST->name];
    }
}

//file : src/Annotation/Put.php
#[Attribute]
class Put extends Route
{
    public function getMethods()
    {
        return [HttpMethod::PUT->name];
    }
}

//file : src/Annotation/Delete.php
#[Attribute]
class Delete extends Route
{
    public function getMethods()
    {
        return [HttpMethod::DELETE->name];
    }
}
```

Now you can polish the `PostController`, use these attributes instead. As you see, the naming of the new attributes literally look more clear.

```php
#[Route(path: "/posts", name: "posts_")]
class PostController extends AbstractController
{

    // constructor...

    // #[Route(path: "", name: "all", methods: ["GET"])]
    #[Get(path: "", name: "all")]
    public function all(#[QueryParam] string $keyword,
                        #[QueryParam] int $offset = 0,
                        #[QueryParam] int $limit = 20): Response
    {
        //...
    }

    // #[Route(path: "/{id}", name: "byId", methods: ["GET"])]
    #[Get(path: "/{id}", name: "byId")]
    public function getById(Uuid $id): Response
    {
       //...
    }

    //#[Route(path: "", name: "create", methods: ["POST"])]
    #[Post(path: "", name: "create")]
    public function create(#[Body] CreatePostDto $data): Response
    {
        //...
    }

    //#[Route(path: "/{id}", name: "update", methods: ["PUT"])]
    #[Put(path: "/{id}", name: "update")]
    public function update(Uuid $id, #[Body] UpdatePostDto $data): Response
    {
        //...
    }

    // #[Route(path: "/{id}/status", name: "update_status", methods: ["PUT"])]
    #[Put(path: "/{id}/status", name: "update_status")]
    public function updateStatus(Uuid $id, #[Body] UpdatePostStatusDto $data): Response
    {
       //...
    }

    //#[Route(path: "/{id}", name: "delete", methods: ["DELETE"])]
    #[Delete(path: "/{id}", name: "delete")]
    public function deleteById(Uuid $id): Response
    {
        //...
    }

    // comments sub resources.
    //#[Route(path: "/{id}/comments", name: "commentByPostId", methods: ["GET"])]
    #[GET(path: "/{id}/comments", name: "commentByPostId")]
    public function getComments(Uuid $id): Response
    {
      //...
    }

    //#[Route(path: "/{id}/comments", name: "addComments", methods: ["POST"])]
    #[Post(path: "/{id}/comments", name: "addComments")]
    public function addComment(Uuid $id, Request $request): Response
    {
		//...
    }

}

```

Run the application again to make sure it works.

