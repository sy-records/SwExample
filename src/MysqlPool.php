<?php

namespace Swoole;

use \Swoole\Coroutine\MySQL;
use \Swoole\Coroutine\Channel as chan;

class MysqlPool
{
    private static $instance;
    private $pool;  //连接池容器，一个channel
    private $config;

    /**
     * @param  null $config
     * @return MysqlPool
     * @desc   获取连接池实例
     */
    public static function getInstance($config = null)
    {
        if (empty(self::$instance)) {
            if (empty($config)) {
                throw new \RuntimeException("mysql config empty");
            }
            self::$instance = new static($config);
        }

        return self::$instance;
    }

	/**
	 * MysqlPool constructor.
	 *
	 * @param $config
	 * @desc  初始化，自动创建实例,需要放在workerstart中执行
	 */
	public function __construct($config)
	{
		if (empty($this->pool)) {
			$this->config = $config;
			$this->pool = new chan($config['pool_size']);
			for ($i = 0; $i < $config['pool_size']; $i++) {
				$mysql = new MySQL();
				$res = $mysql->connect($config);
				if ($res == false) {
					//连接失败，抛弃常
					throw new \RuntimeException("failed to connect mysql server.");
				} else {
					//mysql连接存入channel
					$this->put($mysql);
				}
			}
		}
	}

	/**
     * @param $mysql
     * @desc  放入一个mysql连接入池
     */
    public function put($mysql)
    {
        $this->pool->push($mysql);
    }

    /**
     * @return mixed
     * @desc   获取一个连接，当超时，返回一个异常
     */
    public function get()
    {
        $mysql = $this->pool->pop($this->config['pool_get_timeout']);
        if (false === $mysql) {
            throw new \RuntimeException("get mysql timeout, all mysql connection is used");
        }
        return $mysql;
    }

    /**
     * @return mixed
     * @desc   获取当时连接池可用对象
     */
    public function getLength()
    {
        return $this->pool->length();
    }

}