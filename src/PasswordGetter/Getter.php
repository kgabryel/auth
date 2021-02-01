<?php

namespace Frankie\Auth\PasswordGetter;

interface Getter
{
    public function has(string $name): bool;

    public function getSecret(string $name): string;
}