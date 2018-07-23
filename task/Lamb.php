<?php

namespace Task;

class Lamb implements TaskInterface
{
    private $name;
    public function __construct($name)
    {
        $this->name = $name;
    }

    public function sing()
    {
        echo 'i have a little lamb.'.time()." it's name is {$this->name} \n";
    }

    public function handler()
    {
        // TODO: Implement handler() method.
        $this->sing();
    }
}
