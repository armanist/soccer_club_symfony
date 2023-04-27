# Soccer Club

The application written with Symfony PHP framework which allows to create football teams and add players to them.
It allows to sell and buy players between football teams.


## Requirements

Windows or Ubuntu,
PHP 8.1+,
Composer,
Node,
NPM,
MySQL

## Installation

Clone the code from Github, 
Run `composer install` to pull down the project dependencies (php packages).
Run `nmp install` to pull down the project dependencies (js packages).

Install the Symphony CLI (depends on platform)

`wget https://get.symfony.com/cli/installer -O - | bash`

Migrate the database tables and fixtures

`php bin/console doctrine:migrations:migrate`

The Fixtures uses Faker package to generate `Teams` and `Players`

Run server (will run local server on available port) 

`symfony server:start`

Open browser and navigate to `{base_url}/teams`
