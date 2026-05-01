<?php
/**
 * 前台入口文件
 * 绑定 home 模块并启动框架
 */

use app\app;

/**
 * 绑定前台模块
 */
define('BIND_MODULE', 'home');

require __DIR__.'/../vendor/autoload.php';

app::run();
