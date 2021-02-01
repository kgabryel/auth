<?php

namespace Frankie\Auth\DateChecker;

use DateTime;
use Doctrine\ORM\NativeQuery;

final class DateGte implements DateChecker
{
    private string $columnName;

    public function __construct(string $dateColumn)
    {
        $this->columnName = $dateColumn;
    }

    public function modifyQuery(string &$query): void
    {
        $query .= " and {$this->columnName} >= ?";
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
    }
}