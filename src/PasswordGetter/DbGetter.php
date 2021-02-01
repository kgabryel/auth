<?php

namespace Frankie\Auth\PasswordGetter;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMapping;

final class DbGetter implements Getter
{
    private EntityManager $manager;
    private string $table;
    private string $passwordColumn;
    private string $nameColumn;

    public function __construct(
        EntityManager $manager, string $table, string $passwordColumn, string $nameColumn
    )
    {
        $this->manager = $manager;
        $this->table = $table;
        $this->passwordColumn = $passwordColumn;
        $this->nameColumn = $nameColumn;
    }

    public function __clone()
    {
        $this->manager = clone $this->manager;
    }

    /**
     * @param string $name
     *
     * @return bool
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function has(string $name): bool
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('count', 'count');
        $query = $this->manager->createNativeQuery(
            "select count({$this->passwordColumn}) count 
from {$this->table} where {$this->nameColumn} = ?",
            $rsm
        )
            ->setParameter(0, $name);
        return (int)$query->getSingleScalarResult() === 1;
    }

    public function getSecret(string $name): string
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult($this->passwordColumn, $name);
        $query = $this->manager->createNativeQuery(
            "select {$this->passwordColumn} from {$this->table} where {$this->nameColumn} = ?",
            $rsm
        )
            ->setParameter(0, $name);
        return $query->getResult()[0][$name];
    }
}