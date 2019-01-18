<?php

namespace Wilson\Async\Task;

class Lamb extends AbstractTask
{
    private $name;
    public function __construct($name)
    {
        $this->name = $name;
    }

    public function sing()
    {
        $result = $this->db->query('select now()');
        var_dump($result->fetch());
        echo 'i have a little lamb.'.time()." it's name is {$this->name} \n";
    }

    public function handler()
    {
        // TODO: Implement handler() method.
        $this->sing();
    }
}
