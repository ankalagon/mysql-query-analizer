<?php

namespace MysqlQueryAnalizer\Decorators\Engines;

use \MysqlQueryAnalizer\Decorators\DecoratorInterface;

class CsvDecorator extends GenericDecorator implements DecoratorInterface
{
    const delimiter = "\t";

    public function render()
    {
        $output = implode(self::delimiter, array_keys($this->_data[0])).PHP_EOL;

        foreach ($this->_data as $row) {
            $output .= implode(self::delimiter, array_values($row)).PHP_EOL;
        }

        return $output;
    }
}