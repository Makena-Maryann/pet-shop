<?php

namespace App\Services\Jwt\Interfaces;

interface TokenVerifier
{
    public function verifyToken(string $jwt): ?string;
}
