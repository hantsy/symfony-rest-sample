# Prerequisites 

Before creating a Symfony application, make sure you have installed the following software:

* PHP 8.0+
* PHP Composer
* Symfony CLI
* An text editor or IDE

## Installing PHP and Composer

For most Linux users, PHP are available in the official repository, install it directly via the system built-in package management tools.

For Windows users, to manage your softwares as Linux users, [install Chocolatey](https://chocolatey.org/) firstly.

To install PHP 8 and PHP Composer, run the following command.

```bash
$ choco php composer
```

To verify the installation, run the following command in your terminal.

```bash
$  php -v
PHP 8.1.2 (cli) (built: Jan 19 2022 10:13:52) (NTS Visual C++ 2019 x64)
Copyright (c) The PHP Group
Zend Engine v4.1.2, Copyright (c) Zend Technologies

$ composer -V
Composer version 2.1.12 2021-11-09 16:02:04
```



## Installing Symfony CLI

For Linux users, follow the installation guide in [the Symfony Download page](https://symfony.com/download) to install it into your system.

For Windows users, Symfony CLI is not available in the Chocolatey repository,  if you are using `scoop` or `gofish`, follow the instruction in the [Symfony Download page](https://symfony.com/download) to install it directly. Else download a copy  to your system, add its localtion to the System environment variable `PATH`.

Open a terminal, run the following command to test if it is available in the `PATH`.

```bash
$ symfony -V

 INFO  A new Symfony CLI version is available (5.3.4, currently running 5.3.3).

       If you installed the Symfony CLI via a package manager, updates are going to be automatic.
       If not, upgrade by downloading the new version at https://github.com/symfony-cli/symfony-cli/releases
       And replace the current binary (symfony.exe) by the new one.

Symfony CLI version 5.3.3 (c) 2017-2022 Symfony SAS (2022-02-04T15:07:05Z - stable)
```





## Check System Requirements 

Run the following 

```bash
# symfony check:requirements

Symfony Requirements Checker
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

> PHP is using the following php.ini file:
C:\tools\php80\php.ini

> Checking Symfony requirements:

....................WWW.........

                                              
 [OK]                                         
 Your system is ready to run Symfony projects 
                                              

Optional recommendations to improve your setup
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

 * intl extension should be available
   > Install and enable the intl extension (used for validators).

 * a PHP accelerator should be installed
   > Install and/or enable a PHP accelerator (highly recommended).

 * realpath_cache_size should be at least 5M in php.ini
   > Setting "realpath_cache_size" to e.g. "5242880" or "5M" in
   > php.ini* may improve performance on Windows significantly in some
   > cases.


Note  The command console can use a different php.ini file
~~~~  than the one used by your web server.
      Please check that both the console and the web server
      are using the same PHP version and configuration.

```

Open the *php.ini* file, adjust the configuration according to the above *recommendations* info.  

> If you are not familiar with PHP configuration, backup *php.ini* before modifying it.

And we will use Postgres as database in the sample application, make sure `pdo_pgsql` and `pgsql` modules are enabled.

Finally, you can confirm the enabled modules by the following command.

```bash
$ php -m

[PHP Modules]
bcmath
calendar
Core
ctype
curl
date
dom
filter
hash
iconv
json
libxml
mbstring
mysqlnd
openssl
pcre
PDO
pdo_pgsql
pgsql
Phar
readline
Reflection
session
SimpleXML
SPL
standard
tokenizer
xml
xmlreader
xmlwriter
zip
zlib

[Zend Modules]
```

It lists all enabled PHP  modules.

Everything is OK now, in the next section, we will create a Symfony project.
