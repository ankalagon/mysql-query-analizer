<?php

namespace MysqlQueryAnalizer\Decorators;

class FromArrayDecorator
{
    private $_engine = null;

    public function __construct(DecoratorInterface $engine)
    {
        $this->_engine = $engine;
    }

    public function toString($data)
    {
        $this->_engine->setData($data);
        return $this->_engine->render();
    }
}