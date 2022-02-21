# Handling Exception

Symfony kernel provides a event machoism to raise an `Exception` in `Controller` class and handle them  in your custom `EventListener` or `EventSubscriber` .

## Creating PostNotFoundException

For example, create a `PostNotFoundException`.

```php
class PostNotFoundException extends \RuntimeException
{

    public function __construct(Uuid $uuid)
    {
        parent::__construct("Post #" . $uuid . " was not found");
    }

}
```


## Creating EventListener

Create a `EventListener` to catch this exception, and handle the exception as expected.

```php
class ExceptionListener implements LoggerAwareInterface
{
    private LoggerInterface $logger;

    public function __construct()
    {
    }

    public function onKernelException(ExceptionEvent $event)
    {
        // You get the exception object from the received event
        $exception = $event->getThrowable();
        $data = ["error" => $exception->getMessage()];

        // Customize your response object to display the exception details
        $response = new JsonResponse($data);

        // HttpExceptionInterface is a special type of exception that
        // holds status code and header details

        if ($exception instanceof PostNotFoundException) {
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
        } else if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
        } else {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // sends the modified response object to the event
        $event->setResponse($response);
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
```

Next, register this `ExceptionListener` in *config/service.yml* file. Then it will be applied in the HTTP request lifecycle.

```yml
  App\EventListener\ExceptionListener:
    tags:
      - { name: kernel.event_listener, event: kernel.exception, priority: 50 }
```

It binds the `event.exception`  event to this `ExceptionListener`, and set `priority` value to setup the execution order at runtime.

Run the following command to show all registered `EventListener`/`EventSubscriber`s on event *kernel.exception*.

```base
php bin/console debug:event-subscriber kernel.exception
```


## Throwing Exception in Controller

Open the `PostController`, change the  `getById` function to the following.

```php
#[Route(path: "/{id}", name: "byId", methods: ["GET"])]
function getById(Uuid $id): Response
{
    $data = $this->posts->findOneBy(["id" => $id]);
    if ($data) {
   		return $this->json($data);
    } else {
    	throw new PostNotFoundException($id);
    }
}
```

Run the application again, and try to access a single Post through a non-existing id.

```bash
curl http://localhost:8000/posts/1ec3e1e0-17b3-6ed2-a01c-edecc112b438 -H "Accept: application/json" -v
> GET /posts/1ec3e1e0-17b3-6ed2-a01c-edecc112b438 HTTP/1.1
> Host: localhost:8000
> User-Agent: curl/7.55.1
> Accept: application/json
>
< HTTP/1.1 404 Not Found
< Cache-Control: no-cache, private
< Content-Type: application/json
< Date: Mon, 22 Nov 2021 03:57:51 GMT
< X-Powered-By: PHP/8.0.10
< X-Robots-Tag: noindex
< Content-Length: 69
<
{"error":"Post #1ec3e1e0-17b3-6ed2-a01c-edecc112b438 was not found."}
```

