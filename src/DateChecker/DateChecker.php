<?php

namespace Frankie\Auth\DateChecker;

use Doctrine\ORM\NativeQuery;

interface DateChecker
{
    public function modifyQuery(string &$query): void;

    public function addParameter(NativeQuery $query, int $start): void;
}