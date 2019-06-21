<?php

function db()
{
    return mysqli_connect("localhost", "root", "root");
}
for ($i=0; $i < 10000; $i++) {
    $db_con  = "db{$i}";
    $$db_con = db();
}