<?php
/**
 * 后台入口文件
 * 绑定 admin 模块并启动框架
 */

use app\app;

/**
 * 绑定后台模块
 */
define('BIND_MODULE', 'admin');

require __DIR__.'/../vendor/autoload.php';

app::run();
