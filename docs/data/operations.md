# Data Operations

In Doctrine, there are core components designed for data persistence.

* `ObjectManager` is the interface mainly provided for the end user that used  to find, persist, update and delete instances.
* `ObjectRepository` is used to retrieve instances by flexible queries. 

Symfony integrates Doctrine tightly  and makes these components work in its service container.

* There is a `EntityManagerInterface`  *service* which subclasses `ObjectManager` , you can inject it in any injectable components, such as `Controller`, `Command`, etc.
* Symfony adds an extra `EntityServiceRepository` which inherits from `ObjectRepository`.  All the generated repositories by *make bundle* are derived from the `EntityServiceRepository` class. It allows you to access the service container in your own `Repository`.
* In your own `Repository`, you can create complex queries by `QueryBuilder` API.

The `ObjectManager` and `ObjectRepository` provide interoperating ability. 

* There is a `ObjectManager.forRepository` method to locate the related  `Repository` that targets the `Entity`.
* And there is `getEntityManager` method available in the `Repository` inheritance tree to retrieve the instance of `ObjectManager`.