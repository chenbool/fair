<?php
namespace app\library;

/**
 * 加载器类
 * 负责加载配置文件
 */
final class Loader
{
    /**
     * 构造函数
     */
    function __construct(){}

    /**
     * 初始化加载器
     * 加载应用配置和数据库配置
     */
    public static function _init()
    {
        $conf = include __ROOT__.'/config/config.php';
        $GLOBALS['config'] = $conf;

        $database = include __ROOT__.'/config/database.php';
        $GLOBALS['database'] = $database;
    }

    /**
     * 加载文件
     * @param string $dir 目录路径
     * @param string $file 文件名
     * @param string $ext 文件后缀
     * @return mixed 加载的文件内容
     */
    public static function load($dir, $file, $ext = 'php')
    {
        return include $dir.'/'.$file.'.'.$ext;
    }

    /**
     * 加载模块配置
     * @param string $module 模块名
     * @param string $file 配置文件名
     * @return array 合并后的配置
     */
    public static function config($module, $file = "config")
    {
        $conf = $GLOBALS['config'];

        if (file_exists(__APP__.'/'.$module.'/'.$file.'.php')) {
            $config = include __APP__.'/'.$module.'/'.$file.'.php';
            $conf = array_merge($conf, $config);
        }

        $GLOBALS['config'] = $conf;
        return $GLOBALS['config'];
    }
}
