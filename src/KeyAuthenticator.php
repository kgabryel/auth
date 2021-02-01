<?php

namespace Frankie\Auth;

use Frankie\Auth\Checker\Checker;
use Frankie\Auth\Getter\Getter;
use Frankie\Factory\ResponseFactory;
use Frankie\Response\ResponseInterface;

final class KeyAuthenticator
{
    private Getter $getter;
    private ResponseInterface $nullableError;
    private ResponseInterface $error;
    private Checker $checker;

    /**
     * KeyAuthenticator constructor.
     *
     * @param Getter $getter
     * @param Checker $checker
     * @param ResponseFactory $factory
     */
    public function __construct(
        Getter $getter, Checker $checker, ResponseFactory $factory
    )
    {
        $this->getter = $getter;
        $this->checker = $checker;
        $this->checker->setKey($this->getter->get());
        $this->error = $factory->setBody('No key sent')
            ->get();
        $this->error->withStatus(401);
        $this->nullableError = $this->error;
    }

    public function __clone()
    {
        $this->getter = clone $this->getter;
        $this->nullableError = clone $this->nullableError;
        $this->error = clone $this->error;
        $this->checker = clone $this->checker;
    }

    public function isCorrect(): bool
    {
        if ($this->getter->get() === '') {
            $this->error = $this->nullableError;
            return false;
        }
        if (!$this->checker->isCorrect()) {
            if ($this->checker->getError() !== null) {
                $this->error = $this->checker->getError();
            }
            return false;
        }
        return true;
    }

    public function getError(): ResponseInterface
    {
        return $this->error ?? $this->nullableError;
    }

    public function setError($error): self
    {
        $this->error = $error;
        return $this;
    }
}