<?php

namespace SwExample;

use PDO;
use PDOPDOException;

class DbHelper
{
    //连接池
    private $_pools = [];

    //连接池大小
    const POOLSIZE = 5;

    // 数据库配置
    const USERNAME = "root";
    const PASSWORD = "root";
    const HOST = "127.0.0.1";
    const DB = "wordpress";

    public function __construct()
    {
        $db = self::DB;
        $username = self::USERNAME;
        $password = self::PASSWORD;
        $host = self::HOST;

        for ($i=0; $i < self::POOLSIZE; $i++) {
            $connection = new PDO("mysql:host=$host;dbname=$db;port=3306", $username, $password, array(
                PDO::ATTR_PERSISTENT => true // 持久化连接
            ));
            // sleep(3);
            array_push($this->_pools, $connection);
        }
    }

    // 从数据库连接池中获取一个数据库连接
    public function getConnection()
    {
        // 统计几个连接
        echo 'get' . count($this->_pools) . PHP_EOL;

        if (count($this->_pools) > 0) {
            $one = array_pop($this->_pools);
            // 获取一个以后
            echo 'getAfter' . count($this->_pools) . PHP_EOL;

            return $one;
        } else {
            throw new \Exception("数据库连接池中已无链接资源，请稍后重试!");
        }
    }

    // 归还连接至连接池
    public function release($conn)
    {
        echo 'release' . count($this->_pools) . PHP_EOL;

        if (count($this->_pools) >= self::POOLSIZE) {
            throw new \Exception("数据库连接池已满!");
        } else {
            array_push($this->_pools, $conn);
            // $conn = null;

            echo 'releaseAfter' . count($this->_pools) . PHP_EOL;
        }
    }

    // 查询数据
    public function query($sql)
    {
        try {
            $conn = $this->getConnection();
            $res = $conn->query($sql);
            $this->release($conn);
            return $res;
        } catch (PDOException $e) {
            print 'error:' . $e->getMessage();
        }
    }

    public function queryAll($sql)
    {
        try {
            $conn = $this->getConnection();
            $sth = $conn->prepare($sql);
            $sth->execute();
            $result = $sth->fetchAll();
            return $result;
        } catch (PDOException $e) {
            print 'error:' . $e->getMessage();
        }
    }
}
