<?php

namespace Frankie\Auth\JWT;

final class Verifier
{
    private string $token;

    public function __construct()
    {
        $this->token = '';
    }

    public function setToken(string $token): self
    {
        $this->token = $token;
        return $this;
    }

    public function verifyFormat(): bool
    {
        $parts = explode('.', $this->token);
        if (\count($parts) !== 3) {
            return false;
        }
        return $this->verifyHeader($parts[0]) && $this->verifyPayload($parts[1]);
    }

    /**
     * @param string $header
     *
     * @return bool
     * @throws \JsonException
     */
    private function verifyHeader(string $header): bool
    {
        $header = json_decode(base64_decode($header), true, 512, JSON_THROW_ON_ERROR);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }
        $alg = $this->getPart($header, 'alg');
        return ($this->getPart($header, 'typ') === 'JWT' && $alg !== null && \is_string($alg));
    }

    /**
     * @param string $payload
     *
     * @return bool
     * @throws \JsonException
     */
    private function verifyPayload(string $payload): bool
    {
        json_decode(base64_decode($payload), true, 512, JSON_THROW_ON_ERROR);
        return json_last_error() === JSON_ERROR_NONE;
    }

    private function getPart(array $array, string $name): ?string
    {
        return $array[$name] ?? null;
    }
}