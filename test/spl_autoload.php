<?php

spl_autoload_register('myAutoLoad', true, true);

function myAutoLoad($className)
{
    echo "需要加载的文件：{$className}\r\n";
    include "./{$className}.php";
}

$objDemo = new AutoloadClass();
