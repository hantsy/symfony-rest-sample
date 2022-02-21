# Handling Subresources

In a real world blog application, a `Post` can be commented, it is a great path that the author and the readers communicate with each others.

When designing the APIs such as  adding a comment, there are a lot of debates, for example, *POST /comments* and *POST /posts/{id}/comments* which one is more reasonable. Many architects/developers only remember to perform the CRUD operations via HTTP protocol, but do not think through the relations between entities. 

## Adding Comment to Post

A comment has to be added under a post, so I would like to design it like this.

* Request matches Http verbs/HTTP Method: `POST`
* Request matches route endpoint: */posts/{id}/comments*
* If successful, return a `CREATED`(201) Http Status code, and set the response header *Location* value to the URI of the new created comments.

Add a function to the existing `PostController`.

```php
#[Route(path: "/{id}/comments", name: "addComments", methods: ["POST"])]
 public function addComment(Uuid $id, Request $request): Response
 {
     $data = $this->posts->findOneBy(["id" => $id]);
     if ($data) {
         $dto = $this->serializer->deserialize($request->getContent(), CreateCommentDto::class, 'json');
         $entity = Comment::of($dto->getContent());
 
         $this->objectManager->persist($entity->setPost($data));
         $this->objectManager->flush();
         return $this->json([], 201, ["Location" => "/comments/" . $entity->getId()]);
     } else {
         throw new PostNotFoundException($id);
         //return $this->json(["error" => "Post was not found b}y id:" . $id], 404);
     }
 }
```

## Retrieving Comments

Similar to the above adding comment, when retrieving comments of a specific post, we design the following API.

* Request matches Http verbs/HTTP Method: `GET`
* Request matches route endpoint: */posts/{id}/comments*
* Request header `Accept` matches `application/json`
* If successful, return a `OK`(200) Http Status code, and a collection of data in the response body.

```php
#[Route(path: "/{id}/comments", name: "commentByPostId", methods: ["GET"])]
public function getComments(Uuid $id): Response
{
    $data = $this->posts->findOneBy(["id" => $id]);
    if ($data) {
        return $this->json($data->getComments());
    } else {
        throw new PostNotFoundException($id);
        //return $this->json(["error" => "Post was not found b}y id:" . $id], 404);
    }
}
```



## Retrieving  Single Comment

To retrieve a single comment in some cases, such as for updating purpose, we move the *retrieving single comment* to the `/comments` endpoints.

```php
//src/Controller/CommentController.php
#[Route('comments', name: 'comments')]
class CommentController extends AbstractController
{

    public function __construct(private CommentRepository $commentRepository)
    {
    }

    #[Route('{id}', name: "getById")]
    public function byId(string $id): Response
    {
        $data = $this->commentRepository->findOneBy(["id" => $id]);
        if ($data) {
            $dto = CommentWithPostSummaryDto::of(
                $data->getId(),
                $data->getContent(),
                PostSummaryDto::of($data->getPost()?->getId(), $data->getPost()?->getTitle())
            );
            return $this->json($dto);
        } else {
            return $this->json(["error" => "Comment was not found" . $id], 404);
        }
    }
}
```

## API Design Consideration

How about the *retrieving comments of a user*, we could have some different design considerations,  for example:

* *GET /comments?user=hantsy*  Use a united endpoint for all cases,  and add a query parameter to filter the result 
* and *GET /me/comments* Mounted under  the current user endpoint, filter creator by the current user.

Which is better? I prefer the later. 

Sometimes it is difficult to make a decision this one is good and the other are bad, but when designing RESTful APIs, beside adopting the HTTP methods,  URI path itself is meaningful, it could indicate the root/leaves，whole/parts or parent/children relations. 

So in my mind, when considering the scenario *retrieving comments of a Post*,  *GET /posts/{id}/comments* is better than *GET /comments?post=id*.

