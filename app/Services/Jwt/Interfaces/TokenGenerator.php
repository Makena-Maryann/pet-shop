<?php

namespace App\Services\Jwt\Interfaces;

interface TokenGenerator
{
    public function generateToken(string $userUuid): string;
}
