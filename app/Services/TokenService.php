<?php

namespace App\Services;

use DateInterval;
use DateTimeImmutable;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Validation\Validator;
use Psr\Clock\ClockInterface as Clock;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Token\InvalidTokenStructure;
use Lcobucci\JWT\Encoding\CannotDecodeContent;
use Lcobucci\JWT\Token\UnsupportedHeaderFound;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\IdentifiedBy;
use Lcobucci\JWT\Validation\Constraint\StrictValidAt;
use Lcobucci\JWT\Validation\Constraint\HasClaimWithValue;

class TokenService
{
    private $url;
    private $privateKey;
    private $publicKey;
    private $uniqueId;
    private $userUuid;
    private $algorithm;
    private $expiresAt;
    private $issuedAt;
    private $canOnlyBeUsedAfter;

    public function __construct(string $url, InMemory $privateKey, InMemory $publicKey, Sha256 $algorithm, DateTimeImmutable $expiresAt, DateTimeImmutable $issuedAt, DateTimeImmutable $canOnlyBeUsedAfter)
    {
        $this->url = $url;
        $this->privateKey = $privateKey;
        $this->publicKey = $publicKey;
        $this->algorithm = $algorithm;
        $this->expiresAt = $expiresAt;
        $this->issuedAt = $issuedAt;
        $this->canOnlyBeUsedAfter = $canOnlyBeUsedAfter;
    }

    public function generateToken(string $userUuid, string $uniqueId): string
    {
        $this->userUuid = $userUuid;
        $this->uniqueId = $uniqueId;
        $builder = (new Builder(new JoseEncoder(), ChainedFormatter::default()));

        $token = $builder
            ->issuedBy($this->url)
            ->permittedFor($this->url)
            ->identifiedBy($uniqueId)
            ->issuedAt($this->issuedAt)
            ->canOnlyBeUsedAfter($this->canOnlyBeUsedAfter)
            ->expiresAt($this->expiresAt)
            ->withClaim('uuid', $userUuid)
            ->getToken($this->algorithm, $this->privateKey);

        return $token->toString();
    }

    public function verifyToken(string $jwt): ?string
    {
        $parser = new Parser(new JoseEncoder());

        try {
            $token = $parser->parse($jwt);
        } catch (CannotDecodeContent | InvalidTokenStructure | UnsupportedHeaderFound $e) {
            return null;
        }

        assert($token instanceof UnencryptedToken);

        if (!$this->validateToken($token, $this->uniqueId)) {
            return null;
        }

        return $token->claims()->get('uuid');
    }

    private function validateToken(UnencryptedToken $token, string $uniqueId): bool
    {
        $validator = new Validator();

        $constraints = [
            new IssuedBy($this->url),
            new SignedWith(new Sha256(), $this->publicKey),
            new HasClaimWithValue('uuid', $this->userUuid),
            new IdentifiedBy($uniqueId),
            new StrictValidAt(new Clock, new DateInterval('PT1M')),
        ];

        return $validator->validate($token, ...$constraints);
    }
}
