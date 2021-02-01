<?php

namespace Frankie\Auth\JWT\Algs;

use Exception;
use Frankie\Auth\AuthException;

abstract class Alg
{
    protected function compare(string $signature, string $hash): bool
    {
        $hash = (string)str_replace(
            [
                '+',
                '/'
            ],
            [
                '-',
                '_'
            ],
            base64_encode($hash)
        );
        if (substr_compare($hash, '=', -1) === 0) {
            $hash = substr($hash, 0, -1);
        }
        return $signature === $hash;
    }

    /**
     * @param string $name
     *
     * @return Alg
     * @throws Exception
     */
    public static function get(string $name): Alg
    {
        $name = strtoupper($name);
        switch ($name) {
            case 'HS256':
                return new HS('SHA256');
            case 'HS384':
                return new HS('SHA384');
            case 'HS512':
                return new HS('SHA512');
            case 'RS256':
                return new RS('SHA256');
            case 'RS384':
                return new RS('SHA384');
            case 'RS512':
                return new RS('SHA512');
            default:
                throw new AuthException('Invalid Alg');
        }
    }

    abstract public function verify(string $token, string $secret): bool;
}