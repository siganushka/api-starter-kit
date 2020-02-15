# How to install ?

### Clone && Configuration

```php
$ git clone https://github.com/siganushka/api-starter-kit.git
$ cd ./api-starter-kit
$ cp .env .env.local
```

> configuration ``.env.local``

```php
DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name
```

### Install dependencies

```php
$ composer install
```

### Create database && Table && Fixtures

```php
$ php bin/console doctrine:database:create
$ php bin/console doctrine:schema:update --force
$ php bin/console doctrine:fixtures:load
```

### Install && Compress front-end dependencies

```php
$ yarn install
$ yarn encore production
```

### Unit test

```php
$ php bin/phpunit --debug
```

### Generate API doc

```php
$ ./node_modules/.bin/apidoc -i ./src/Controller/ -o ./public/apidoc
```
