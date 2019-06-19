<?php

require '../vendor/autoload.php';

use Swoole\MysqlPool;

$config = [
    'host' => '127.0.0.1',   //数据库ip
    'port' => 3306,          //数据库端口
    'user' => 'root',        //数据库用户名
    'password' => 'root', //数据库密码
    'database' => 'wordpress',   //默认数据库名
    'timeout' => 0.5,       //数据库连接超时时间
    'charset' => 'utf8mb4', //默认字符集
    'strict_type' => true,  //ture，会自动表数字转为int类型
    'pool_size' => '3',     //连接池大小
    'pool_get_timeout' => 0.5, //当在此时间内未获得到一个连接，会立即返回。（表示所有的连接都已在使用中）
];

//创建http server
$http = new Swoole\Http\Server("0.0.0.0", 9501);
$http->set([
		//"daemonize" => true,
		"worker_num" => 1,
		"log_level" => SWOOLE_LOG_ERROR,
    ]);

$http->on('WorkerStart', function ($serv, $worker_id) use ($config) {
        //worker启动时,每个进程都初始化连接池，在onRequest中可以直接使用
        try {
            MysqlPool::getInstance($config);
        } catch (\Exception $e) {
            //初始化异常，关闭服务
            echo $e->getMessage() . PHP_EOL;
            $serv->shutdown();
        } catch (\Throwable $throwable) {
            //初始化异常，关闭服务
            echo $throwable->getMessage() . PHP_EOL;
            $serv->shutdown();
        }
    });

$http->on('request', function ($request, $response) {

        //浏览器会自动发起这个请求，这也是很多人碰到的一个问题：
        //为什么我浏览器打开网站，收到了两个请求?
        if ($request->server['path_info'] == '/favicon.ico') {
            $response->end('');
            return;
        }

        //获取数据库
        if ($request->server['path_info'] == '/list') {
            go(function () use ($request, $response) {
                    //从池子中获取一个实例
                    try {
                        $pool = MysqlPool::getInstance();
                        $mysql = $pool->get();
                        defer(function () use ($mysql) {
                                //利用defer特性，可以达到协程执行完成，归还$mysql到连接池
                                //好处是 可能因为业务代码很长，导致乱用或者忘记把资源归还
                                MysqlPool::getInstance()->put($mysql);
                                echo "当前可用连接数：" . MysqlPool::getInstance()->getLength() . PHP_EOL;
                            });
                        $result = $mysql->query("select * from wp_users");
                        $response->end(json_encode($result));
                    } catch (\Exception $e) {
                    	$response->end($e->getMessage());
                    }
            });
            return;
        }

        echo "get request:".date('Y-m-d H:i:s').PHP_EOL;
        if ($request->server['path_info'] == '/timeout') {
            go(function () use ($request, $response) {
                    //从池子中获取一个实例
                    try {
						$pool = MysqlPool::getInstance();
						echo "当前可用连接数：" . $pool->getLength() . PHP_EOL;
						$mysql = $pool->get();
						echo "当前可用连接数：" . $pool->getLength() . PHP_EOL;
                        defer(function () use ($mysql) {
                                //协程执行完成，归还$mysql到连接池
                                MysqlPool::getInstance()->put($mysql);
                                echo "当前可用连接数：" . MysqlPool::getInstance()->getLength() . PHP_EOL;
                        });
						$result = $mysql->query("select * from wp_users");
						\Swoole\Coroutine::sleep(10); //sleep 10秒,模拟耗时操作
						$response->end(date('Y-m-d H:i:s').PHP_EOL.json_encode($result));
                    } catch (\Exception $e) {
                    	$response->end($e->getMessage());
                    }
            });
            return;
        }

    });

$http->start();