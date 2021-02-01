<?php

namespace Frankie\Auth\Checker;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Frankie\Auth\DateChecker\DateChecker;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use Frankie\Factory\ResponseFactory;
use Frankie\Response\ResponseInterface;

class DbWithDateChecker implements Checker
{
    protected EntityManager $manager;
    protected string $key;
    protected string $table;
    protected string $column;
    protected ResponseInterface $error;
    protected DateChecker $dateChecker;

    public function __construct(
        ResponseFactory $factory, EntityManager $manager, string $table, string $column,
        DateChecker $dateChecker
    )
    {
        $this->manager = $manager;
        $this->key = '';
        $this->table = $table;
        $this->column = $column;
        $this->dateChecker = $dateChecker;
        $this->error = $factory->setBody('Invalid key.')
            ->get();
        $this->error->withStatus(401);
    }

    public function __clone()
    {
        $this->manager = clone $this->manager;
        $this->error = clone $this->error;
        $this->dateChecker = clone $this->dateChecker;
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
        $sqlQuery = "select count('{$this->column}') count from {$this->table} where '{$this->column}' = ?";
        $this->dateChecker->modifyQuery($sqlQuery);
        $query = $this->manager->createNativeQuery($sqlQuery, $rsm)
            ->setParameter(0, $this->key);
        $this->dateChecker->addParameter($query, 1);
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