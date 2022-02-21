# Hello Symfony!

In this post, we will create a simply Somfony project and running the application.

Symfony follows the famous MVC pattern to handle request.  The controller  role is responsible for handling incoming request, updating models and  sending results to the HTTP response.

## Creating Controller

Here we will create a simple `Controller` to experience Symfony request handling.

```php 
class HelloController
{
    /**
     * @param Request $request
     * @return Response
     */
    #[Route('/hello', name: 'hello', methods:['GET'])]
    public function sayHello(Request $request): Response
    {
        $name = $request->get("name") ?? "Symfony";
        $data = ['message' => 'Hello ' . $name];

        return new JsonResponse($data, 200, [], true);
    }
}  
```

Attribute is a new feature introduced in PHP 8.0, here we use `Route` attribute to define the routing rule for the `HelloController`.  

## Configuring Routes

According to the `Route` attribute defined in the `sayHello` method, if the incoming request path is matched with */hello* and the request method is `GET`,  it will be handled by `HelloController.sayHello` method. The `request` argument will be filled before invoking this method, it includes all request data in this request context. Every handling method return a `Reponse`, including the response status, view data, etc.

> Symfony provides several approaches to configure the routes, such as YAML,  annotations, XML, JSON, etc.  But the PHP official `Attribute` will be the trend in future. We do not cover other methods in this tutorial. 

To use the newest PHP 8 `Attribute` to configure the routing rules, apply the following changes in the project configuration.

* Open *config/packages/doctrine.yaml*,  remove  `doctrine/orm/mapping/App/type` node in the configuration tree or change its value to `attribute`.
* Open *composer.json*,  make sure  the PHP version set to `>=8.0.0`.

## Rendering JSON Response

To render the response body into a JSON string,  use a `JsonReponse` to wrap the response.  The first parameter of `JsonReponse` accepts an array as data. In PHP, it is a little tedious to convert an object to an array.

Symfony provides a simple `AbstractController` which includes several functions to simplfy the response building and adopt the container and dependency injection management. 

Change the above controller, make it to extend from `AbstractController`.  

```php
class HelloController extends AbstractController
{
    /**
     * @param Request $request
     * @return Response
     */
    #[Get('/hello', name: 'hello')]
    public function sayHello(Request $request): Response
    {
        $name = $request->get("name") ?? "Symfony";
        $data = Greeting::of('Hello ' . $name);
        return $this->json($data);
    }
}
```
It simply invoke `$this->json` to accept an object and render the response in JSON format, no need to transform the data to an array before rendering response.

The `Greeting` is a plain PHP class.

```php
class Greeting
{
    private string $message;
    
    static function of(string $message): Greeting
    {
        $data = new Greeting();
        return $data->setMessage($message);
    }
    
    // use IDE to generate setters and getters
}    
```

Run the application, use `curl` to test the `/hello` endpoint.

```bash
$ curl http://localhost:8000/hello
{"message":"Hello Symfony"}
$ curl http://localhost:8000/hello?name=Hantsy
{"message":"Hello Hantsy"}
```
