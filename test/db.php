<?php

include '../vendor/autoload.php';

use SwExample\DbHelper;

$sql = 'select * from wp_users limit 1';

$db = new DbHelper;

for ($i=0; $i < 10; $i++) {
    echo '获取第'.$i.'次连接'.PHP_EOL;
    $res = $db->queryAll($sql);
//  var_dump($res) . PHP_EOL;
}