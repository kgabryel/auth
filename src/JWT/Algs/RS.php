<?php

namespace Frankie\Auth\JWT\Algs;

use Frankie\Auth\AuthException;

class RS extends Alg
{
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
        $hash = '';
        openssl_sign($parts[0] . '.' . $parts[1], $hash, $secret, $this->type);
        return $this->compare($parts[2], $hash);
    }
}