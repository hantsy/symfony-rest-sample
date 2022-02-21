# API Design Consideration

The term `REST` firstly occurred in the [CHAPTER 5: Representational State Transfer (REST)](https://www.ics.uci.edu/~fielding/pubs/dissertation/rest_arch_style.htm) of [Roy Fielding](https://twobithistory.org/2020/06/28/rest.html)'s dissertation: [Architectural Styles and the Design of Network-based Software](https://www.ics.uci.edu/~fielding/pubs/dissertation/top.htm). This dissertation had been translated into several languages, including Chinese. But it does not include certain executable guidelines and considerations when designing RESTful APIs, till [Richardson Maturity Model](https://martinfowler.com/articles/richardsonMaturityModel.html) (RMM) was born. 

## Richardson Maturity Model

Richardson Maturity Model defines a series of  **Levels** used to evaluate the quality of the given RESTful APIs. 

* Level 0 - only uses HTTP as a transport protocol, but without using any of the  mechanisms of the web. For example,  in the legacy RMI or RPC protocol, it use a single endpoint to handle all requests, the interaction between the client and server is based on your own implementation.
* Level 1 uses an unique URI endpoint to identify resources, and break a large service endpoint down into multiple smaller resources. For example, in an ecommerce  system, defining various resources, such as */customers*, */orders*, */products*, etc.
* Level 2 introduces a standard set of verbs so that we handle similar situations in the same way, removing unnecessary variation. For the same resource endpoint, such as */orders*, verb `POST` to place a new order, `GET` to retrieve all orders.
* Level 3 introduces discoverability, providing a way of making a protocol more self-documenting. HATEOAS (Hypertext As The Engine Of Application State) is a popular protocol archives *self-documenting* purpose, for example, in a *collection* result, using links make it possible to navigate all subresources with extra documentation. 

According to these clear rules, when exploring the existing popular APIs in the internet, you will find a lot of public APIs only archive Level 0 or Level 1, in fact they are not *REST* ready,  although the author names  it *REST API* in their documentation. 

When building RESTful APIs, make sure the design solution archives RMM Level 2, else we may can not call it  *REST*.  If you can take RMM Level 3 into consideration, it is a great addition.

## Design RESTful API

If you are new to REST, consider reading [Roy's article](https://www.ics.uci.edu/~fielding/pubs/dissertation/rest_arch_style.htm) firstly, and do not miss [Martin's explanation of RMM](https://martinfowler.com/articles/richardsonMaturityModel.html).  To learn these resource in a central place, I suggest the [REST API Tutorial ](https://restfulapi.net/) website which explains REST and RMM with detailed examples. 

[Github API](https://docs.github.com/en/rest) is an excellent REST design example, you can consider Github API as the *Best Practice*,  when designing REST API, use it as a reference.

Follow the REST convention and the requirements of RMM Level 2, we summarize the RESTful APIs of a blog system into a table list. This list covers all APIs we have designed and implemented in the previous sections.

| URI      | Http Method | Request                                         | Response                    | Description       |
| ----------- | ----------- | ----------------------------------------------- | --------------------------- | ----------------- |
| /posts      | GET         |                                                 | 200, [{'id':1, 'title'},{}] | Get all posts     |
| /posts      | POST        | {'title':'test title','content':'test content'} | 201                         | Create a new post |
| /posts/{id} | GET         |                                                 | 200, {'id':1, 'title'}      | Get a post by id  |
| /posts/{id} | PUT         | {'title':'test title','content':'test content'} | 204                         | Update a post     |
| /posts/{id} | DELETE      |                                                 | 204                         | Delete a post     |
| /posts/{id}/comments | GET  |                                                 | 200, [{'content':''},{}] | Get comments of a post|
| /posts/{id}/comments | POST  | {'content':'test content'} | 201                   | Add comment to a post    |

## Considering Entity Relations

When we design RESTful API, besides following the HTTP protocol and REST convention, most of time it is maybe heavily dependent on our past experience and practice.

How about the *retrieving comments of a user*, we could have some different design considerations,  for example:

* *GET /comments?user=hantsy*  Use a united endpoint for all cases,  and add a query parameter to filter the result 
* and *GET /me/comments* Mounted under  the current user endpoint(*/me*), filter comments by the current user.

Which is better? I prefer the later. 

Sometimes it is difficult to make a decision this one is good and the other are bad.  Besides identifying the resources, URI path itself is meaningful, it could indicate the root/leaves, whole/parts or parent/children relations. 

So in my mind, when considering the scenario *retrieving comments of a Post*,  *GET /posts/{id}/comments* is better than *GET /comments?post=id*.

