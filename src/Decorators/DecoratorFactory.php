<?php

namespace MysqlQueryAnalizer\Decorators;

class DecoratorFactory
{
    public static function create($decoratorName)
    {
        $decoratorName = strtolower($decoratorName);

        if ($decoratorName== 'table') {
            return new Engines\TableDecorator();
        } elseif ($decoratorName == 'csv') {
            return new Engines\CsvDecorator();
        }

        throw new \Exception(sprintf('Invalid decorator name "%s"', $decoratorName));
    }
}