<?php

namespace App\Services\Jwt;

use DateTimeImmutable;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use App\Services\Jwt\Interfaces\TokenGenerator;

class TokenGeneration implements TokenGenerator
{
    private $url;
    private $privateKey;
    private $algorithm;
    private $uniqueId;
    private $expiresAt;
    private $issuedAt;
    private $canOnlyBeUsedAfter;

    public function __construct(
        string $url,
        InMemory $privateKey,
        Sha256 $algorithm,
        string $uniqueId,
        DateTimeImmutable $expiresAt,
        DateTimeImmutable $issuedAt,
        DateTimeImmutable $canOnlyBeUsedAfter
    ) {
        $this->url = $url;
        $this->privateKey = $privateKey;
        $this->algorithm = $algorithm;
        $this->uniqueId = $uniqueId;
        $this->expiresAt = $expiresAt;
        $this->issuedAt = $issuedAt;
        $this->canOnlyBeUsedAfter = $canOnlyBeUsedAfter;
    }

    public function generateToken(string $userUuid): string
    {
        $builder = (new Builder(new JoseEncoder(), ChainedFormatter::default()));

        $token = $builder
            ->issuedBy($this->url)
            ->permittedFor($this->url)
            ->identifiedBy($this->uniqueId)
            ->issuedAt($this->issuedAt)
            ->canOnlyBeUsedAfter($this->canOnlyBeUsedAfter)
            ->expiresAt($this->expiresAt)
            ->withClaim('uuid', $userUuid)
            ->getToken($this->algorithm, $this->privateKey);

        return $token->toString();
    }
}
