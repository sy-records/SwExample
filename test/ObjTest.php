<?php

class ObjTest
{
    protected $num;
    
    public function __construct($num)
    {
        $this->num = $num;
    }

    public function setNum($num)
    {
        $this->num = $num;
    }

    public function getNum()
    {
        return $this->num;
    }
}

function microtimeFloat()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

$startTime = microtimeFloat();

for ($i = 0; $i < 10000; $i++) {
    $test = new ObjTest($i);
    $test->getNum();
}
echo "对象不复用耗时" . (microtimeFloat() - $startTime) . '秒' . PHP_EOL;

$startTime = microtimeFloat();

$test = new ObjTest(0);

for ($i = 0; $i < 10000; $i++) {
    $test->setNum($i);
    $test->getNum();
}
echo "对象复用耗时" . (microtimeFloat() - $startTime) . '秒' . PHP_EOL;


