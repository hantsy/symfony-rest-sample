# Creating Symfony Project

You can create a  new Symfony project using Symfony CLI or Composer command line tools.

## Using Symfony CLI

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

## Using Composer 

Run the following command to create a Symfony  project using `composer`.

```bash
# composer create-project symfony/skeleton rest-sample

//start a classic website application
# composer create-project symfony/website-skeleton web-sample
```

The later is similar to the `symfony new projectname --full` to generate a full-featured web project skeleton.

## Running Application

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