<?php

spl_autoload_register('myAutoLoad');

function myAutoLoad($className)
{
    echo "需要加载的文件：{$className}\r\n";
    include "./{$className}.php";
}

new AutoloadClass();
//new AutoloadClass2();
