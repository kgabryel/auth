<?php

namespace Frankie\Auth\Checker;

use Frankie\Response\Response;
use Frankie\Response\ResponseInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class WebChecker implements Checker
{
    protected ResponseInterface $error;
    protected string $method;
    protected string $url;
    protected array $headers;
    protected Client $client;
    protected ResponseInterface $response;
    protected bool $defaultResult;
    /** @var int[] */
    protected array $correctStatuses;
    /** @var int[] */
    protected array $incorrectStatuses;

    /**
     * WebChecker constructor.
     *
     * @param Client $client
     * @param string $method
     * @param string $url
     * @param bool $defaultResult
     * @param array $headers
     *
     * @throws \JsonException
     */
    public function __construct(
        Client $client, string $method, string $url, bool $defaultResult, array $headers = []
    )
    {
        $this->client = $client;
        $this->method = strtoupper($method);
        $this->url = $url;
        $this->headers = $headers;
        $this->defaultResult = $defaultResult;
        $this->correctStatuses = [];
        $this->incorrectStatuses = [];
        $this->error = (new Response())->withStatus(401)
            ->withBody(json_encode('Invalid key.', JSON_THROW_ON_ERROR));
    }

    public function __clone()
    {
        $this->client = clone $this->client;
        $this->response = clone $this->response;
        $this->error = clone $this->error;
    }

    /**
     * @return bool
     * @throws GuzzleException
     */
    public function isCorrect(): bool
    {
        $this->response = $this->client->request(
            $this->method,
            $this->url,
            ['headers' => $this->headers]
        );
        $status = $this->response->getStatusCode();
        if (\in_array($status, $this->correctStatuses, true)) {
            return true;
        }
        if (\in_array($status, $this->incorrectStatuses, true)) {
            return false;
        }
        return $this->defaultResult;
    }

    public function setError($error): Checker
    {
        $this->error = $error;
        return $this;
    }

    public function getError(): ResponseInterface
    {
        return $this->error;
    }

    /**
     * @return ResponseInterface|null
     */
    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }

    public function setCorrectStatuses(array $statuses): self
    {
        $this->correctStatuses = $statuses;
        return $this;
    }

    public function setIncorrectStatuses(array $statuses): self
    {
        $this->incorrectStatuses = $statuses;
        return $this;
    }

    public function setKey(string $key): Checker
    {
        return $this;
    }
}