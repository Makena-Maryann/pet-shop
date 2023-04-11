# Pet Shop (eCommerce)

## Description

This is an API that provides the necessary endpoints and HTTP request methods to satisfy the needs of Buckhill's FE team.

## Setup/Installation Requirements

Before you start, make sure you have the following installed:

-   [PHP](https://www.php.net/downloads.php) >= 8.2
-   [Composer](https://getcomposer.org/download/)
-   [MySQL](https://dev.mysql.com/downloads/mysql/)
-   [Git](https://git-scm.com/downloads)

**TL;DR command list**

    git clone https://github.com/Makena-Maryann/pet-shop.git
    cd pet-shop
    composer install
    cp .env.example .env
    php artisan key:generate
    php artisan migrate --seed
    php artisan serve

Clone the repository & switch to the repo folder

    git clone https://github.com/Makena-Maryann/pet-shop.git
    cd pet-shop

Install dependencies & copy the .env.example file to .env

    composer install
    cp .env.example .env

Generate application key & run database migrations & seeders (**Set the database connection in .env before migrating**)

    php artisan key:generate
    php artisan migrate --seed

Start the local development server

    php artisan serve

You can now access the server at `http://localhost:8000`

## JWT Setup

Create an asymmetric key pair to sign and verify JWT tokens and store them in the storage/app/jwt folder using the following commands:

    openssl genrsa -out storage/app/jwt/private.pem 2048

    openssl rsa -in storage/app/jwt/private.pem -pubout -out storage/app/jwt/public.pem

You can update the JWT configuration in config/jwt.php . The default configuration uses `RS256 algorithm` to sign the JWT token and sets the token expiration time to `2 hours`. You can also change the `token issuer`, `expiry time`, and other configurations.

## API Documentation

To test the API endpoints, run the command below to generate the documentation.

    php artisan l5-swagger:generate

You can now access the documentation at `http://localhost:8000/api/documentation`

## Admin Credentials

To login as an admin, use the following credentials:

    email: admin@buckhill.co.uk
    password: admin

## Testing

To run the tests, run the following command:

    php artisan test

## Level 3 Challenge

The repo can be found [here](https://github.com/Makena-Maryann/currency-exchange-rate).

## User Stories Implemented

1. Admin credentials will not change.
2. User emails will change, but the password will remain the same.
3. The API routing prefix must follow the following convention: /api/v1/{route_name}.
4. Bearer Token authentication.
5. Middleware protection.
6. Admin endpoint.
7. User Login/logout.
8. The application needs to seed at least 50 orders for 10 different users with a random amount of products, these orders will get randomly assigned an order status, if the order status is paid or shipped it must have an assigned payment method.
9. All listing endpoints most include a paginated response and include these basic filters: Page, limit, sort by, desc.
10. Delivery fee: If the order total amount is higher than 500 there is a free delivery otherwise, there will be a charge of 15.

## License

The Pet Shop Project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

Copyright (c) 2023 **Maryann Makena**
