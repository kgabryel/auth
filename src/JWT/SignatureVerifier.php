<?php

namespace Frankie\Auth\JWT;

use Frankie\Auth\JWT\Algs\Alg;

class SignatureVerifier
{
    /**
     * @param string $token
     * @param string $alg
     * @param string $secret
     *
     * @return bool
     * @throws \Exception
     */
    public function verify(string $token, string $alg, string $secret): bool
    {
        $verifier = Alg::get($alg);
        return $verifier->verify($token, $secret);
    }
}