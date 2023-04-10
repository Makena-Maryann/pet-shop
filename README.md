-   Setting up the project

    -   Basic setup
    -   Database setup
    -   JWT setup
        Create an asymmetric key pair to sign and verify JWT tokens. You can use OpenSSL to generate a new key pair. The private key is used to sign the JWT token and the public key is used to verify the signature.

        openssl genrsa -out storage/app/jwt/private.pem 2048
        openssl rsa -in storage/app/jwt/private.pem -pubout -out storage/app/jwt/public.pem

        You can update the JWT configuration in config/jwt.php . The default configuration uses RS256 algorithm to sign the JWT token. Sets the token expiration time to 2 hours. You can also change the token issuer, expiry time, and other configurations.

    -   Docs generation

        -   php artisan l5-swagger:generate

    -   Php Insights

-   Testing

    -   Unit Testing
    -   Feature Testing

-   User Stories Implemented

    -   Admin credentials will not change (email / password) admin@buckhill.co.uk / admin
    -   User emails will change, but the password will remain the same, you can get the new user email from the admin/user-listing endpoint. password: userpassword
    -   The API routing prefix must follow the following convention: /api/v1/{route_name}
    -   Bearer Token authentication
    -   Middleware protection
    -   Admin endpoint
    -   User Login/logout
    -   The application needs to seed at least 50 orders for 10 different users with a random amount of products, these orders will get randomly assigned an order status, if the order status is paid or shipped it must have an assigned payment method.
    -   All listing endpoints most include a paginated response and include these basic filters: Page, limit, sort by, desc
    -   Delivery fee: If the order total amount is higher than 500 there is a free delivery otherwise, there will be a charge of 15

-   Level 3 Challenge
    -   Repo and installation instructions
    -   How it has been implemented in the repo
