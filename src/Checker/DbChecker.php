<?php

namespace Frankie\Auth\Checker;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMapping;
use Frankie\Factory\ResponseFactory;
use Frankie\Response\ResponseInterface;

class DbChecker implements Checker
{
    protected EntityManager $manager;
    protected string $key;
    protected string $table;
    protected string $column;
    protected ResponseInterface $error;

    public function __construct(
        ResponseFactory $factory, EntityManager $manager, string $table, string $column
    )
    {
        $this->manager = $manager;
        $this->key = '';
        $this->table = $table;
        $this->column = $column;
        $this->error = $factory->setBody('Invalid key.')
            ->get();
        $this->error->withStatus(401);
    }

    public function __clone()
    {
        $this->manager = clone $this->manager;
        $this->error = clone $this->error;
    }

    /**
     * @return bool
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function isCorrect(): bool
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('count', 'count');
        $query = $this->manager->createNativeQuery(
            "select count('{$this->column}') count from {$this->table} where '{$this->column}' = ?",
            $rsm
        )
            ->setParameter(0, $this->key);
        return (int)$query->getSingleScalarResult() === 1;
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

    public function setKey(string $key): Checker
    {
        $this->key = $key;
        return $this;
    }
}