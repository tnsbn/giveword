# * Giveword *

## 1. Install packages
* cp .env.example .env
* composer install
* npm install
* php artisan key:generate
* php artisan migrate
* php artisan db:seed
* php artisan storage:link

## 2. RabbitMQ Configuration

Add connection to `config/queue.php`:

> This is the minimal config for the rabbitMQ connection/driver to work.

```php
'connections' => [
    // ...

    'rabbitmq' => [
    
       'driver' => 'rabbitmq',
       'hosts' => [
           [
               'host' => env('RABBITMQ_HOST', '127.0.0.1'),
               'port' => env('RABBITMQ_PORT', 5672),
               'user' => env('RABBITMQ_USER', 'guest'),
               'password' => env('RABBITMQ_PASSWORD', 'guest'),
               'vhost' => env('RABBITMQ_VHOST', '/'),
           ],
           // ...
       ],
       // ...
    ],

    // ...    
],
```


## 3. Elastic configuration

```
php artisan vendor:publish --provider="Elastic\Client\ServiceProvider"
```
```
php artisan vendor:publish --provider="Elastic\Migrations\ServiceProvider"
```

#### Migration:
```
php artisan elastic:migrate --force
```

#### Revert the last executed migrations:
```
php artisan elastic:migrate:rollback
```

#### Revert all previously migrated files:
```
php artisan elastic:migrate:reset
```

#### Drop all existing indices and rerun the migrations:
```
php artisan elastic:migrate:fresh
```

## 4. Run
```
php artisan serve
```
