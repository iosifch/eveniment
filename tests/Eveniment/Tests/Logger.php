<?php

namespace Eveniment\Tests;

class Logger
{
    protected $logs = [];

    public function offsetSet($index, $value)
    {
        $this->logs[$index] = $value;
    }

    public function offsetGet($index)
    {
        return $this->logs[$index];
    }
}
