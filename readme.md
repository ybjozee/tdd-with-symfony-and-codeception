TDD with Codeception and Symfony
==================

Project
=================
This is a demo application built to demonstrate how to use Codeception in a Symfony project. 


Deploying
=================

## Technical Requirements

Before creating your first Symfony application you must:
* Install PHP 7.2.5 or higher and these PHP extensions (which are installed and enabled by default in most PHP 7 installations):
  [Ctype](https://www.php.net/book.ctype), [iconv](https://www.php.net/book.iconv), [JSON](https://www.php.net/book.json),[PCRE](https://www.php.net/book.pcre), [Session](https://www.php.net/book.session), [SimpleXML](https://www.php.net/book.simplexml), and [Tokenizer](https://www.php.net/book.tokenizer);

* Install [Composer](https://getcomposer.org/download/), which is used to install PHP packages.

* Optionally, you can also [install Symfony CLI](https://symfony.com/download). This creates a binary called symfony that provides all the tools you need to develop and
  run your Symfony application locally.

* The symfony binary also provides a tool to check if your computer meets all requirements. Open your console terminal and run this command:

        symfony check:requirements


Please visit [The Symfony Official Page](https://symfony.com/doc/current/setup.html) for more information on this project's technical
requirements.


## Installation
- Clone the project from github

        git clone https://github.com/ybjozee/tdd-with-symfony-and-codeception.git

- cd into the project

        cd tdd-with-symfony-and-codeception

- install the project dependencies

        composer install

- create your local .env file

        cp .env .env.local
  
- create your local .env.test file

        cp .env .env.test.local

***Remember to update your DATABASE_URL in .env.local***

    DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"

***Remember to update your DATABASE_URL in .env.local***

    DATABASE_URL="sqlite:///%kernel.project_dir%/var/data_test.db"

- Create your databases

        php bin/console doctrine:database:create
  
        php bin/console doctrine:database:create --env=test

- Update your schemas

        php bin/console doctrine:schema:update --force
  
        php bin/console doctrine:schema:update --force --env=test

- Run your tests 
        
        php vendor/bin/codecept/run 


