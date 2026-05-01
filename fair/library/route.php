<?php
namespace app\library;

use app\help\Help;
use app\library\Loader;
use app\library\Exception;

/**
 * 路由类
 * 负责解析 URL 请求并加载对应的控制器和方法
 */
final class Route
{
    public $config;

    /**
     * 构造函数
     * 自动执行路由分发
     */
    function __construct()
    {
        self::dispatch($this->config);
    }

    /**
     * 路由分发入口
     */
    private static function dispatch()
    {
        if (!isset($_SERVER['PATH_INFO'])) {
            $module = defined('BIND_MODULE') ? BIND_MODULE : 'home';
            $controller = "index";
            $action = 'index';
            Loader::config($module);
        } else {
            $pathInfo = trim($_SERVER['PATH_INFO'], '/');
            $route = explode('/', $pathInfo);

            if (defined('BIND_MODULE')) {
                $module = BIND_MODULE;
                $controller = $route[0];
                unset($route[0]);
                $action = $route[1];
                unset($route[1]);
            } else {
                $module = $route[0];
                unset($route[0]);
                $controller = $route[1];
                unset($route[1]);
                $action = $route[2];
                unset($route[2]);
            }

            $config = Loader::config($module);
            $arg = array_values($route);

            if (isset($config['URL_HTML_SUFFIX'])) {
                $arg = str_replace('.'.$config['URL_HTML_SUFFIX'], "", $route);
            }

            if (isset($config['URL_ARG_DEPR'])) {
                foreach ($arg as $key => $value) {
                    $args = explode($config['URL_ARG_DEPR'], $value);
                    foreach ($args as $k => $v) {
                        if ($k % 2 == 0) {
                            $val = isset($args[$k + 1]) ? $args[$k + 1] : '';
                            $_GET[$args[$k]] = $val;
                        } else {
                            continue;
                        }
                    }
                }
            } else {
                foreach ($arg as $k => $v) {
                    if ($k % 2 == 0) {
                        $val = isset($arg[$k + 1]) ? $arg[$k + 1] : '';
                        $_GET[$arg[$k]] = $val;
                    } else {
                        continue;
                    }
                }
            }
        }

        self::_load($module, $controller, $action);
    }

    /**
     * 加载控制器
     * @param string $module 模块名
     * @param string $controller 控制器名
     * @param string $action 方法名
     */
    private static function _load($module, $controller, $action)
    {
        define('CONTROLLER_NAME', ucfirst($controller));
        $controller = ucfirst($controller).'Controller';
        $action = lcfirst($action);

        define('__MODULE__', $module);
        define('__ACTION__', $action);
        define('__CONTROLLER__', $controller);

        $path = __APP__.'/'.$module.'/controller/'.$controller.'.php';

        if (is_file($path)) {
            require $path;
            $controller = new $controller();

            $methods = get_class_methods($controller);
            if (in_array($action, $methods)) {
                $controller->$action();
            } else {
                Exception::error($action.'方法不存在!<br>', $path);
            }
        } else {
            Exception::error($controller.'控制器不存在!<br>', $path);
        }
    }
}
