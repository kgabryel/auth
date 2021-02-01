<?php

namespace Frankie\Auth\JWT;

use Frankie\Auth\AuthException;

class Parser
{
    protected array $header;
    protected array $payload;
    protected string $signature;

    /**
     * Parser constructor.
     *
     * @param string $token
     *
     * @throws AuthException
     * @throws \JsonException
     */
    public function __construct(string $token)
    {
        $parts = explode('.', $token);
        if (\count($parts) !== 3) {
            throw new AuthException('Invalid token format.');
        }
        $this->header = json_decode(base64_decode($parts[0]), true, 512, JSON_THROW_ON_ERROR);
        $this->payload = json_decode(base64_decode($parts[1]), true, 512, JSON_THROW_ON_ERROR);
        $this->signature = $parts[2];
    }

    public function getHeader(): array
    {
        return $this->header;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }
}