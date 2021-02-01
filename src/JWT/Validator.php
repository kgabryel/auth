<?php

namespace Frankie\Auth\JWT;

use Frankie\Auth\Checker\Checker;

final class Validator
{
    public const HEADER = 0;
    public const PAYLOAD = 1;
    private array $issuers;
    private array $subjects;
    private array $groups;
    private bool $expFlag;
    private bool $nbfFlag;
    private Checker $idChecker;
    private array $header;
    private array $payload;

    public function __construct()
    {
        $this->issuers = [];
        $this->subjects = [];
        $this->groups = [];
        $this->header = [];
        $this->payload = [];
        $this->expFlag = false;
        $this->nbfFlag = false;
    }

    public function __clone()
    {
        $this->idChecker = clone $this->idChecker;
    }

    public function setHeader(array $header): self
    {
        $this->header = $header;
        return $this;
    }

    public function setPayload(array $payload): self
    {
        $this->payload = $payload;
        return $this;
    }

    public function verifyIssuer(array $acceptable): self
    {
        $this->issuers = $acceptable;
        return $this;
    }

    public function verifySubjects(array $subjects): self
    {
        $this->subjects = $subjects;
        return $this;
    }

    public function verifyGroups(array $groups): self
    {
        $this->groups = $groups;
        return $this;
    }

    public function verifyExp(): self
    {
        $this->expFlag = true;
        return $this;
    }

    public function verifyNbf(): self
    {
        $this->nbfFlag = true;
        return $this;
    }

    public function verifyId(Checker $checker): self
    {
        $this->idChecker = $checker;
        return $this;
    }

    public function check(array $payload): bool
    {
        if ($this->expFlag && $this->checkDate('exp', $payload)) {
            return false;
        }
        if ($this->nbfFlag && $this->checkDate('nbf', $payload, true)) {
            return false;
        }
        if (($this->issuers !== []) && !$this->checkContent('iss', $payload, $this->issuers)) {
            return false;
        }
        if (($this->subjects !== []) && !$this->checkContent('sub', $payload, $this->subjects)) {
            return false;
        }
        if (($this->groups !== []) && !$this->checkContent('groups', $payload, $this->groups)) {
            return false;
        }
        if ($this->idChecker !== null && isset($payload['jti']) && \is_string($payload['jti'])) {
            $this->idChecker->setKey($payload['jti']);
            return $this->idChecker->isCorrect();
        }
        return true;
    }

    private function checkContent(string $key, array $content, array $values): bool
    {
        if (!isset($content[$key])) {
            return false;
        }
        if (\is_array($content[$key])) {
            foreach ($content[$key] as $value) {
                if (\in_array($value, $values, true)) {
                    return true;
                }
            }
            return false;
        }
        return \in_array($content[$key], $values, true);
    }

    private function checkDate(string $key, array $content, bool $greater = false): bool
    {
        if (!isset($content[$key])) {
            return false;
        }
        if ($greater) {
            return (int)$content[$key] >= time();
        }
        return (int)$content[$key] <= time();
    }
}