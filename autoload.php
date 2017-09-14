<?php
//error_reporting(0);
error_reporting(E_ALL || ~E_NOTICE);
ini_set('display_errors', '1');
ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
session_start();
header("Content-Type: text/html; charset=UTF-8");

//定义根目录
define('ROOT_PATH',dirname(__FILE__));
define('DB_TYPE','redis');

if(DB_TYPE=='redis') :
	try {
		$redis = new Redis();
		$redis->connect("127.0.0.1", 6379);
		$redis->select(1);
	}
	catch(Exception $e) {
		echo 'Message: ' .$e->getMessage();
	}
endif;
