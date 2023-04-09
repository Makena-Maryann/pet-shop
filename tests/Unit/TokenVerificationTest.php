<?php

namespace Tests\Unit;

use Mockery;
use DateTimeImmutable;
use Psr\Clock\ClockInterface;
use PHPUnit\Framework\TestCase;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use App\Services\Jwt\TokenGeneration;
use Lcobucci\JWT\Signer\Key\InMemory;
use App\Services\Jwt\TokenVerification;
use App\Services\Jwt\Interfaces\TokenVerifier;

class TokenVerificationTest extends TestCase
{
    private $url;
    private $publicKey;
    private $algorithm;
    private $clock;
    private $privateKey;
    private $uniqueId;
    private $expiresAt;
    private $issuedAt;
    private $canOnlyBeUsedAfter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->url = 'https://example.com';
        $this->algorithm = new Sha256();
        $this->clock = Mockery::mock(ClockInterface::class);
        $this->uniqueId = '12345';
        $this->expiresAt = new DateTimeImmutable('+1 hour');
        $this->issuedAt = new DateTimeImmutable();
        $this->canOnlyBeUsedAfter = new DateTimeImmutable();

        // Load public key from file
        $keyFile  = dirname(dirname(__DIR__)) . '/storage/app/jwt/public.pem';
        $keyContents = file_get_contents($keyFile);

        if (!$keyContents) {
            $this->fail("Failed to read key file: $keyFile");
        }

        $this->publicKey = InMemory::plainText($keyContents);

        // Load private key from file
        $keyFile  = dirname(dirname(__DIR__)) . '/storage/app/jwt/private.pem';
        $keyContents = file_get_contents($keyFile);

        if (!$keyContents) {
            $this->fail("Failed to read key file: $keyFile");
        }

        $this->privateKey = InMemory::plainText($keyContents);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        Mockery::close();
    }

    public function test_implements_token_verifier_interface()
    {
        $tokenVerifier = new TokenVerification($this->url, $this->publicKey, $this->algorithm, $this->clock);

        $this->assertInstanceOf(TokenVerifier::class, $tokenVerifier);
    }

    public function test_verify_token_returns_null_on_invalid_token()
    {
        $jwt = 'invalid.jwt.token';

        $tokenVerifier = new TokenVerification($this->url, $this->publicKey, $this->algorithm, $this->clock);

        $result = $tokenVerifier->verifyToken($jwt);

        $this->assertNull($result);
    }

    public function test_verify_token_returns_null_when_validation_fails()
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

        $jwt = $tokenGeneration->generateToken($userUuid);

        $tokenVerifier = new TokenVerification("https://me.com", $this->privateKey, $this->algorithm, $this->clock);

        $result = $tokenVerifier->verifyToken($jwt);

        $this->assertNull($result);
    }

    public function test_verify_token_returns_token_on_valid_token()
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

        $jwt = $tokenGeneration->generateToken($userUuid);

        $currentTime = new DateTimeImmutable();
        $this->clock->shouldReceive('now')->andReturn($currentTime);
        $tokenVerifier = new TokenVerification($this->url, $this->publicKey, $this->algorithm, $this->clock);
        $result = $tokenVerifier->verifyToken($jwt);

        $this->assertNotNull($result);
    }
}
