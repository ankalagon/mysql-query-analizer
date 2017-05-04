<?php

namespace MysqlQueryAnalizer\Decorators\Engines;

class GenericDecorator
{
    /**
     * data to decorate
     * @var array
     */
    protected $_data = [];

    public function setData(array $data)
    {
        $this->_data = $data;
    }
}