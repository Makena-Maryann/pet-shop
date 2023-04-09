<?php

namespace App\Services\Jwt;

use DateInterval;
use Psr\Clock\ClockInterface;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Validation\Validator;
use Lcobucci\JWT\Token\InvalidTokenStructure;
use App\Services\Jwt\Interfaces\TokenVerifier;
use Lcobucci\JWT\Encoding\CannotDecodeContent;
use Lcobucci\JWT\Token\UnsupportedHeaderFound;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\StrictValidAt;

class TokenVerification implements TokenVerifier
{
    private $url;
    private $publicKey;
    private $algorithm;
    private $clock;

    public function __construct(string $url, InMemory $publicKey, Sha256 $algorithm, ClockInterface $clock)
    {
        $this->url = $url;
        $this->publicKey = $publicKey;
        $this->algorithm = $algorithm;
        $this->setClock($clock);
    }

    public function setClock(ClockInterface $clock)
    {
        $this->clock = $clock;
    }

    public function verifyToken(string $jwt): ?UnencryptedToken
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

        return $token;
    }

    private function validateToken(UnencryptedToken $token): bool
    {
        $validator = new Validator();

        $constraints = [
            new IssuedBy($this->url),
            new SignedWith($this->algorithm, $this->publicKey),
            new StrictValidAt($this->clock, new DateInterval('PT1M')),
        ];

        return $validator->validate($token, ...$constraints);
    }
}
