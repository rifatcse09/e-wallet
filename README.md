## Geekpicker p2p wallet

## Description

The task is developed by Laravel 8. Laravel Repository Pattern is used for development. Laravel Passport is used for API authentication. Frontend is developed by Vue JS SB Admin Template. The API of http://fixer.io is used for currency conversion. In development, SOLID design principles are fully followed for coding. The task has PHPUnit test case.

### Installation

1. Clone this repo

```
git clone https://github.com/rifatcse09/e-wallet.git
```

2. Install composer packages

```
$ cd e-wallet
$ composer install
```

3. Create and setup .env file

```
$ copy .env.example .env
$ php artisan key:generate
put database credentials in .env file
```

5. Put currency conversion api key in .env file

```
CURRENCY_API_KEY=YOUR_FIXER_ACCESS_KEY
```

6. Put mailtrap credential

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
```

7. Migrate and insert records

```
$ php artisan migrate
$ php artisan db:seed
```

8. Install passport

```
$ php artisan passport:install
$ php artisan passport:keys
```

9. Install dependency with NPM
 
```
 npm install
```

10. Develop

 ```
 npm run dev
 ```
 
11. Run the server

```
$ php artisan serve
```

12. Run phpUnit Test

```
$ ./vendor/bin/phpunit
```
