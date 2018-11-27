<?php

use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Config\Adapter\Ini as ConfigIni;
use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Url;
use Phalcon\Mvc\Application;

// 定义基础目录文件
define('APP_DIRS', realpath('..').'/');
require APP_DIRS.'library/test.php';
require APP_DIRS.'library/function.php';
// 读取基础配置文件--ConfigIni()
$config = new ConfigIni(APP_DIRS.'config/base.ini');
// 注册路径--Loader()
$loader = new Loader();
$loader->registerDirs([
    APP_DIRS.'app/controllers/',
    APP_DIRS.'app/models/'
])->register();
// 注册容器--FactoryDefault()
$di = new FactoryDefault();
// 注册视图路由
$di->set('view', function(){
    $view = new View();
    $view->setViewsDir(APP_DIRS.'app/views/');
    return $view;
});
// 注册url
$di->set('url', function(){
    $url = new Url();
    $url->setBaseUri('/');
    return $url;
});
// 注册数据库服务
$di->set("db", function() use ($config){
    return new DbAdapter([
        "host" => $config->database->host,
        "username" => $config->database->username,
        "password" => $config->database->password,
        "dbname" => $config->database->dbname
    ]);
});
// 处理MVC请求
$application = new Application($di);
try {
    $response = $application->handle();
    $response->send();
} catch(\Exception $e) {
    echo "Exception: ", $e->getMessage();
}
