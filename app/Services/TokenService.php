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
    private $issuer;
    private $privateKey;
    private $publicKey;
    private $userUuid;
    private $uniqueId;

    public function __construct(string $issuer, InMemory $privateKey, InMemory $publicKey, string $uniqueId)
    {
        $this->issuer = $issuer;
        $this->privateKey = $privateKey;
        $this->publicKey = $publicKey;
        $this->uniqueId = $uniqueId;
    }

    public function generateToken(string $userUuid): string
    {
        $this->userUuid = $userUuid;
        $builder = (new Builder(new JoseEncoder(), ChainedFormatter::default()));
        $algorithm = new Sha256();
        $now   = new DateTimeImmutable();

        $token = $builder
            ->issuedBy($this->issuer)
            ->permittedFor($this->issuer)
            ->identifiedBy($this->uniqueId)
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now)
            ->expiresAt($now->modify('+2 hours'))
            ->withClaim('uuid', $userUuid)
            ->getToken($algorithm, $this->privateKey);

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

        if (!$this->validateToken($token)) {
            return null;
        }

        return $token->claims()->get('uuid');
    }

    private function validateToken(UnencryptedToken $token): bool
    {
        $validator = new Validator();

        $constraints = [
            new IssuedBy($this->issuer),
            new SignedWith(new Sha256(), $this->publicKey),
            new HasClaimWithValue('uuid', $this->userUuid),
            new IdentifiedBy($this->uniqueId),
            new StrictValidAt(new Clock, new DateInterval('PT1M')),
        ];

        return $validator->validate($token, ...$constraints);
    }
}
