<?php
namespace app;

use app\library\Route;
use app\library\Loader;
use app\library\Constant;

/**
 * 应用启动类
 * 框架入口，负责初始化和启动整个应用
 */
final class app
{
    /**
     * 构造函数
     */
    function __construct(){}

    /**
     * 框架启动入口
     * 依次执行：初始化 → 常量定义 → 加载器 → 路由
     */
    public static function run()
    {
        self::_init();
        self::constant();
        self::loader();
        self::route();
    }

    /**
     * 初始化设置
     * 启动 Session、设置时区、编码
     */
    public static function _init()
    {
        session_start();
        date_default_timezone_set('PRC');
        header("Content-type: text/html; charset=utf-8");
    }

    /**
     * 定义常量
     */
    public static function constant()
    {
        new Constant();
    }

    /**
     * 路由分发
     */
    public static function route()
    {
        new Route();
    }

    /**
     * 加载器初始化
     * 加载配置和助手函数
     */
    public static function loader()
    {
        Loader::_init();
        Loader::load(__ROOT__.'/help', 'function');
    }
}
