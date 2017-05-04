<?php

namespace MysqlQueryAnalizer\Decorators\Engines;

use CLIFramework\Component\Table\Table;
use \MysqlQueryAnalizer\Decorators\DecoratorInterface;


class TableDecorator extends GenericDecorator implements DecoratorInterface
{
    public function render()
    {
        $table = new Table();
        $table->setHeaders(array_keys($this->_data[0]));

        foreach ($this->_data as $row) {
            $table->addRow(array_values($row));
        }

        return $table->render();
    }
}