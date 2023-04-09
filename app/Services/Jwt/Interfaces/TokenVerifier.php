<?php

namespace App\Services\Jwt\Interfaces;

use Lcobucci\JWT\UnencryptedToken;

interface TokenVerifier
{
    public function verifyToken(string $jwt): ?UnencryptedToken;
}
