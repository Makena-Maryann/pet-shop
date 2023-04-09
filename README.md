Setting up the project

-   Basic setup

    -   Database setup
    -   JWT setup
    -   Docs generation
    -   Php Insights

-   Testing

    -   Unit Testing
    -   Feature Testing

-   User Stories Implemented

-   Level 3 Challenge
    -   Repo and installation instructions
    -   How it has been implemented in the repo

Create an asymmetric key pair to sign and verify JWT tokens. You can use OpenSSL to generate a new key pair. The private key is used to sign the JWT token and the public key is used to verify the signature.

openssl genrsa -out storage/app/jwt/private.pem 2048
openssl rsa -in storage/app/jwt/private.pem -pubout -out storage/app/jwt/public.pem

You can update the JWT configuration in config/jwt.php . The default configuration uses RS256 algorithm to sign the JWT token. Sets the token expiration time to 2 hours. You can also change the token issuer, expiry time, and other configurations.
