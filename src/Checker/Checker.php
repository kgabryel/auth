<?php

namespace Frankie\Auth\Checker;

interface Checker
{
    public function isCorrect(): bool;

    public function setError($error): self;

    public function getError();

    public function setKey(string $key): self;
}