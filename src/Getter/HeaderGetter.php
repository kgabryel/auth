<?php

namespace Frankie\Auth\Getter;

use Frankie\Request\RequestInterface;

final class HeaderGetter implements Getter
{
    private string $key;

    public function __construct(RequestInterface $request, string $key, string $prefix = '')
    {
        $this->key = '';
        if ($prefix !== '' && strpos($key, $prefix) === 0) {
            $key = substr($key, \strlen($prefix), \strlen($key));
        }
        if (
        $request->getHeaders()
            ->hasHeader($key)
        ) {
            $this->key = $request->getHeaders()
                ->getHeader($key);
        }
    }

    public function get(): string
    {
        return $this->key;
    }
}