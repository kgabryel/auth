<?php

namespace Frankie\Auth;

use Frankie\Auth\JWT\Parser;
use Frankie\Auth\JWT\SignatureVerifier;
use Frankie\Auth\JWT\Validator;
use Frankie\Auth\JWT\Verifier;
use Frankie\Auth\PasswordGetter\Getter;

final class JWTAuthenticator
{
    private string $token;
    private array $header;
    private array $payload;
    private Verifier $verifier;
    private Getter $passwordGetter;
    private SignatureVerifier $signVerifier;
    private Validator $validator;

    public function __construct(
        string $token, Verifier $JWTVerifier, Getter $getter, SignatureVerifier $signatureVerifier,
        Validator $validator
    )
    {
        $this->token = $token;
        $this->verifier = $JWTVerifier;
        $this->passwordGetter = $getter;
        $this->signVerifier = $signatureVerifier;
        $this->header = [];
        $this->payload = [];
        $this->validator = $validator;
    }

    public function __clone()
    {
        $this->verifier = clone $this->verifier;
        $this->passwordGetter = clone $this->passwordGetter;
        $this->signVerifier = clone $this->signVerifier;
        $this->validator = clone $this->validator;
    }

    /**
     * @return bool
     * @throws AuthException
     * @throws \Exception
     */
    public function isCorrect(): bool
    {
        $this->verifier->setToken($this->token);
        if (!$this->verifier->verifyFormat()) {
            return false;
        }
        $parser = new Parser($this->token);
        $this->header = $parser->getHeader();
        $this->payload = $parser->getPayload();
        if (!$this->passwordGetter->has($this->header['alg'])) {
            return false;
        }
        if (
        !$this->signVerifier->verify(
            $this->token,
            $this->header['alg'],
            $this->passwordGetter->getSecret($this->header['alg'])
        )
        ) {
            return false;
        }
        $this->validator->setHeader($this->header);
        $this->validator->setPayload($this->payload);
        return $this->validator->check($this->payload);
    }
}