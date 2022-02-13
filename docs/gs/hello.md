# Hello Symfony!

In this post, we will create a simply Somfony project and running the application.



## Creating Symfony Project

You can create a  new Symfony project using Symfony CLI or Composer command line tools.



### Using Symfony CLI

To create a new Symfony project, you can use `symfony new` command. 

```bash
$ symfony new rest-sample

// install a webapp pack 
$ symfony new web-sample --webapp
```

By default, it will create a simple Symfony skeleton project only with core kernel configuration, which is good to start a lightweight Restful API application.

To get full options list of `symfony new` command, type the following command in your terminal.

```bash
$ symfony help new
Description:
  Create a new Symfony project

Usage:
  symfony.exe local:new [options] [--] [<directory>]

Arguments:
  directory  Directory of the project to create


Options:
  --dir=value      Project directory
  --version=value  The version of the Symfony skeleton (a version or one of "lts", "stable", "next", or "previous")
  --full           Use github.com/symfony/website-skeleton (deprecated, use --webapp instead)
  --demo           Use github.com/symfony/demo
  --webapp         Add the webapp pack to get a fully configured web project
  --book           Clone the Symfony: The Fast Track book project
  --docker         Enable Docker support
  --no-git         Do not initialize Git
  --cloud          Initialize Platform.sh
  --debug          Display commands output
  --php=value      PHP version to use
```

Alternatively, you can create it using Composer. 



### Using Composer 

Run the following command to create a Symfony  project using `composer`.

```bash
# composer create-project symfony/skeleton rest-sample

//start a classic website application
# composer create-project symfony/website-skeleton web-sample
```

The later is similar to the `symfony new projectname --full` to generate a full-featured web project skeleton.



### Running Symfony Application

Open your terminal, switch to the project root folder, and run the following command to start the application.

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

Open a browser and navigate to [http://127.0.0.1:8000](http://127.0.0.1:8000) , it will show the default home page.


## Hello Symfony!

Symfony follows the famous MVC pattern to handle request.  The controller  role is responsible for handling incoming request, updating models and  sending results to the HTTP response.



### Creating Controller

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



### Configuring Routes

According to the `Route` attribute defined in the `sayHello` method, if the incoming request path is matched with */hello* and the request method is `GET`,  it will be handled by `HelloController.sayHello` method. The `request` argument will be filled before invoking this method, it includes all request data in this request context. Every handling method return a `Reponse`, including the response status, view data, etc.

> Symfony provides several approaches to configure the routes, such as YAML,  annotations, XML, JSON, etc.  But the PHP official `Attribute` will be the trend in future. We do not cover other methods in this tutorial. 

To use the newest PHP 8 `Attribute` to configure the routing rules, apply the following changes in the project configuration.

* Open *config/packages/doctrine.yaml*,  remove  `doctrine/orm/mapping/App/type` node in the configuration tree or change its value to `attribute`.
* Open *composer.json*,  make sure  the PHP version set to `>=8.0.0`.

### Rendering JSON Response

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
