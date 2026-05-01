<?php
namespace app\library;

use app\library\Loader;

/**
 * 常量定义类
 * 定义框架运行时所需的路径常量、请求常量和系统常量
 */
final class Constant
{
    /**
     * 构造函数
     * 依次初始化系统常量、路径常量、请求常量
     */
    function __construct()
    {
        $this->systemSet();
        $this->path();
        $this->request();
    }

    /**
     * 定义路径常量
     */
    private function path()
    {
        define('DS', DIRECTORY_SEPARATOR);
        defined('__ROOT__') || define('__ROOT__', dirname($_SERVER['SCRIPT_FILENAME']));
        defined('__APP__') || define('__APP__', __ROOT__.'/app');
        defined('__TPL__') || define('__TPL__', __ROOT__.'/template');
        defined('__CONFIG__') || define('__CONFIG__', __ROOT__.'/config');
    }

    /**
     * 定义请求相关常量
     */
    private function request()
    {
        defined('__HOST__') || define('__HOST__', 'http://'.$_SERVER['HTTP_HOST']);
        defined('__SCRIPT__') || define('__SCRIPT__', $_SERVER['SCRIPT_NAME']);
        defined('__SELF__') || define('__SELF__', __HOST__.$_SERVER['PHP_SELF']);

        if (isset($_SERVER['PATH_INFO'])) {
            defined('__INFO__') || define('__INFO__', $_SERVER['PATH_INFO']);
        }

        defined('__PUBLIC__') || define('__PUBLIC__', __HOST__.'/public');
        defined('__METHOD__') || define('__METHOD__', $_SERVER['REQUEST_METHOD']);
        defined('NOW_TIME') || define('NOW_TIME', $_SERVER['REQUEST_TIME']);

        define('IS_GET', __METHOD__ == 'GET' ? true : false);
        define('IS_POST', __METHOD__ == 'POST' ? true : false);
        define('IS_FILE', !empty($_FILES) ? true : false);
        define('IS_PUT', __METHOD__ == 'PUT' ? true : false);
        define('IS_DELETE', __METHOD__ == 'DELETE' ? true : false);
    }

    /**
     * 定义系统常量
     */
    private static function systemSet()
    {
        defined('VERSION') || define('VERSION', '1.0.0');
        $GLOBALS['_beginTime'] = microtime(TRUE);
        define('MEMORY_LIMIT_ON', function_exists('memory_get_usage'));
        if (MEMORY_LIMIT_ON) {
            $GLOBALS['_startUseMems'] = memory_get_usage();
        }
    }
}
