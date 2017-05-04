<?php

namespace MysqlQueryAnalizer\Decorators;

abstract class AvailableDecorators
{
    const table = 'table';
    const csv = 'csv';

    public static function getDecorators()
    {
        return [self::table, self::csv];
    }
}