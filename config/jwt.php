<?php

use Lcobucci\JWT\Signer\Rsa\Sha256;

return [
    //The URL of the JWT issuer
    'url' => env('JWT_URL', 'http://localhost'),

    //The path to the private key
    'private_key_path' => env('JWT_PRIVATE_KEY_PATH', storage_path('app/jwt/private.pem')),

    //The path to the public key
    'public_key_path' => env('JWT_PUBLIC_KEY_PATH', storage_path('app/jwt/public.pem')),

    //The algorithm used to sign the JWT
    'algorithm' => env('JWT_ALGORITHM', new Sha256()),

    //The unique ID of the JWT
    'unique_id' => env('JWT_UNIQUE_ID', bin2hex(random_bytes(8))),

    //The expiration time of the JWT
    'expires_at' => env('JWT_EXPIRES_AT', 120),

    //The time the JWT was issued
    'issued_at' => env('JWT_ISSUED_AT', 0),

    //The time the JWT can be used after it was issued
    'used_after' => env('JWT_USED_AFTER', 0),
];
