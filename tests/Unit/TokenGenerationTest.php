<?php

namespace Tests\Unit;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use App\Services\Jwt\TokenGeneration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Encoding\ChainedFormatter;

class TokenGenerationTest extends TestCase
{
    private $url;
    private $privateKey;
    private $algorithm;
    private $uniqueId;
    private $expiresAt;
    private $issuedAt;
    private $canOnlyBeUsedAfter;

    protected function setUp(): void
    {
        parent::setUp();

        // Set up test data
        $this->url = 'https://example.com';
        $this->uniqueId = '12345';
        $this->algorithm = new Sha256();
        $this->expiresAt = new DateTimeImmutable('+1 hour');
        $this->issuedAt = new DateTimeImmutable();
        $this->canOnlyBeUsedAfter = new DateTimeImmutable();

        // Load private key from file
        $keyFile  = dirname(dirname(__DIR__)) . '/storage/app/jwt/private.pem';
        $keyContents = file_get_contents($keyFile);

        if (!$keyContents) {
            $this->fail("Failed to read key file: $keyFile");
        }

        $this->privateKey = InMemory::plainText($keyContents);
    }

    public function test_generate_token_returns_valid_jwt_token(): void
    {
        $userUuid = 'abcd-1234';

        $tokenGeneration = new TokenGeneration(
            $this->url,
            $this->privateKey,
            $this->algorithm,
            $this->uniqueId,
            $this->expiresAt,
            $this->issuedAt,
            $this->canOnlyBeUsedAfter
        );

        $token = $tokenGeneration->generateToken($userUuid);

        $this->assertIsString($token);
    }
}
