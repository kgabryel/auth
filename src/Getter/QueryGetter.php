<?php

namespace Frankie\Auth\Getter;

use Frankie\Request\RequestInterface;

final class QueryGetter implements Getter
{
    private string $key;

    public function __construct(RequestInterface $request, string $key)
    {
        $this->key = '';
        if (
        $request->getQueries()
            ->hasQuery($key)
        ) {
            $this->key = $request->getQueries()
                ->getQuery($key);
        }
    }

    public function get(): string
    {
        return $this->key;
    }
}