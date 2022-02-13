# Data Accessing with Doctrine

Doctrine is a popular ORM framework, it is highly inspired by the existing Java ORM tooling, such as JPA spec and Hibernate framework. 
There are two core components in Doctrine, `doctrine/dbal` and `doctrine/orm`, the former is a low level APIs for database operations, if you know Java development, consider it as the *Jdbc* layer. 
The later is the advanced ORM framework, the public APIs are similar to JPA/Hibernate. 
