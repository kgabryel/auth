<?php

namespace Frankie\Auth\JWT\Algs;

use Frankie\Auth\AuthException;

class HS extends Alg
{
    /** @var string */
    protected string $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    /**
     * @param string $token
     * @param string $secret
     *
     * @return bool
     * @throws AuthException
     */
    public function verify(string $token, string $secret): bool
    {
        $parts = explode('.', $token);
        if (\count($parts) !== 3) {
            throw new AuthException('Invalid token format.');
        }
        $hash = hash_hmac($this->type, $parts[0] . '.' . $parts[1], $secret, true);
        return $this->compare($parts[2], $hash);
    }
}