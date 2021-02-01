<?php

namespace Frankie\Auth\Getter;

final class NullGetter implements Getter
{

    public function get(): string
    {
        return '';
    }
}