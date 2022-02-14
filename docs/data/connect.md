# Connecting to Database 

To enable Doctrine support in Symfony project,  install *symfony/orm-pack* firstly.


## Installing Doctrine ORM

Open a  terminal, switch to the project root folder and run:

```bash
$ composer require symfony/orm-pack
```

The **pack** is a virtual Symfony package, it  includes a bundle of  PHP packages and predefined configurations.

When it is installed successfully, it will install several packages, updated the package.lock file, add intial Doctrine configurations into the *config* folder, and update the *.env* file to add database related configuration, such as connection URL, etc..

By the way, install *symfony-maker-bundle*.

```bash 
$ composer require --dev symfony/maker-bundle
```

The *maker-bundle* provides a series of commands to generate skeleton codes from predefined template, including generating controllers, entities, repository, etc..

## Configuring Database

Open the `.env` file in the project root folder, edit the `DATABASE_URL` value, setup the database name, username, password.

```properties
DATABASE_URL="postgresql://user:password@127.0.0.1:5432/blogdb?serverVersion=13&charset=utf8"
```

Run the following command to generate a docker compose file template.

```bash
$ php bin/console make:docker:database
```

We change it to the following to start up a Postgres  database instance in development stage.

```yaml
version: "3.5" # specify docker-compose version, v3.5 is compatible with docker 17.12.0+

# Define the services/containers to be run
services:

  postgres:
    image: postgres:${POSTGRES_VERSION:-13}-alpine
    ports:
      - "5432:5432"
    environment:
      POSTGRES_DB: ${POSTGRES_DB:-blogdb}
      # You should definitely change the password in production
      POSTGRES_PASSWORD: ${POSTGRES_PASSWORD:-password}
      POSTGRES_USER: ${POSTGRES_USER:-user}
    volumes:
      - ./data/blogdb:/var/lib/postgresql/data:rw
      - ./pg-initdb.d:/docker-entrypoint-initdb.d
```

We will  use `UUID` as data type of the primary key, add a script to enable `uuid-ossp` extension in Postgres when it is starting up.

```sql
-- file: pg-initdb.d/ini.sql
SET search_path TO public;
DROP EXTENSION IF EXISTS "uuid-ossp";
CREATE EXTENSION "uuid-ossp" SCHEMA public;
```

Open *config/packages/test/doctrine.yaml*, comment out `dbname_suffix` line.  

We use Docker container to bootstrap a database to ensure the application behaviors are same between the development and production.

## Starting up Database

Before starting the application, make sure the database is running.  Run the following command to start up the Postgres in Docker.

```bash
$ docker compose up postgres
$ docker ps -a # to list all containers and make the postgres is running
```

Now startup the application and make sure there is no exception in the console, that means the database connection is successful.

```bash
$ symfony server:start
```

