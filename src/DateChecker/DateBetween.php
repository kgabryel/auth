<?php

namespace Frankie\Auth\DateChecker;

use DateTime;
use Doctrine\ORM\NativeQuery;

final class DateBetween implements DateChecker
{
    private string $columnFrom;
    private string $columnTo;
    private string $fromOperator;
    private string $toOperator;

    public function __construct(
        string $columnFrom, string $columnTo, bool $fromEq = false, $toEq = false
    )
    {
        $this->columnFrom = $columnFrom;
        $this->columnTo = $columnTo;
        $this->fromOperator = '<';
        if ($fromEq) {
            $this->fromOperator = '<=';
        }
        $this->toOperator = '>';
        if ($toEq) {
            $this->toOperator = '>=';
        }
    }

    public function modifyQuery(string &$query): void
    {
        $query .= " and {$this->columnFrom} {$this->fromOperator} ? 
        and {$this->columnTo} {$this->toOperator} ?";
    }

    /**
     * @param NativeQuery $query
     * @param int $start
     *
     * @throws \Exception
     */
    public function addParameter(NativeQuery $query, int $start): void
    {
        $query->setParameter($start, (new DateTime())->format('Y-m-d H:i:s'));
        $query->setParameter(++$start, (new DateTime())->format('Y-m-d H:i:s'));
    }
}