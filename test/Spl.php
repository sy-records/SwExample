<?php
/**
 * User: lufei
 * Date: 2019/9/2
 * Email: lufei@swoole.com
 */

// spl_class 返回所有可用的SPL类
print_r(spl_classes());

// spl_​autoload_​register  注册给定的函数作为 __autoload 的实现
function myAutoLoader($class) {
	include './' . $class . '.php';
}
spl_autoload_register('myAutoLoader');

$obj = new AutoloadClass();

// spl_​autoload_​functions  返回所有已注册的__autoload()函数
$functions = spl_autoload_functions();
var_dump($functions);

// spl_​autoload_​unregister 注销已注册的__autoload()函数
$status = spl_autoload_unregister("myAutoLoader");
var_dump($status);

// spl_​autoload_​extensions 注册并返回spl_autoload函数使用的默认文件扩展名
var_dump(spl_autoload_extensions());
var_dump(spl_autoload_extensions(".swphp"));
var_dump(spl_autoload_extensions());

// SplStack
$stack = new SplStack();
$stack->push("1111\n");
$stack->push("2222\n");

echo $stack->pop();
echo $stack->pop();

// SplQueue
$queue = new SplQueue();
$queue->enqueue("1111\n");
$queue->enqueue("2222\n");

echo $queue->dequeue();
echo $queue->dequeue();

// SplMinHeap
$heap = new SplMinHeap();
$heap->insert("1111\n");
$heap->insert("2222\n");

echo $heap->extract();
echo $heap->extract();

// SplFixedArray
$array = new SplFixedArray(10);
$array[0] = '1';
$array[9] = '10';
var_dump($array);