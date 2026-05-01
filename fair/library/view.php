<?php
namespace app\library;

use app\library\Exception;

/**
 * 视图类
 * 负责模板渲染
 */
final class View
{
    /**
     * 构造函数
     */
    function __construct(){}

    /**
     * 渲染模板
     * @param string|null $tpl 模板名称
     * @param array $arg 传递给模板的变量
     */
    public static function fetch($tpl = null, $arg = [])
    {
        $ext = isset($GLOBALS['config']['TPL_TEMPLATE_SUFFIX']) ? $GLOBALS['config']['TPL_TEMPLATE_SUFFIX'] : '.tpl';
        $depr = isset($GLOBALS['config']['TPL_FILE_DEPR']) ? $GLOBALS['config']['TPL_FILE_DEPR'] : '/';

        if (empty($tpl)) {
            $tpl = __ACTION__;
        }

        if (!is_null($arg) && !empty($arg)) {
            error_reporting(0);
            extract($arg);
        }

        $theme = isset($GLOBALS['config']['TPL_THEME']) ? $GLOBALS['config']['TPL_THEME'].'/' : '';
        $viewPath = isset($GLOBALS['config']['VIEW_PATH']) ? $GLOBALS['config']['VIEW_PATH'] : '';

        if (empty($viewPath)) {
            $path = __APP__.'/'.__MODULE__.'/view/'.$theme.lcfirst(CONTROLLER_NAME).$depr.$tpl.$ext;
        } else {
            $path = __ROOT__.'/'.$viewPath.$theme.lcfirst(CONTROLLER_NAME).$depr.$tpl.$ext;
        }

        file_exists($path) || Exception::error($tpl.'模板不存在!<br>', $path);
        include $path;
    }
}
