<?php
use app\library\View;
use app\library\Medoo;
use app\library\Exception;

/**
 * 视图渲染函数
 * @param string $tpl 模板名称
 * @param array $arg 传递给模板的变量
 */
function view($tpl = '', $arg = [])
{
    View::fetch($tpl, $arg);
}

/**
 * 打印变量 (调试用)
 * @param mixed $val 要打印的变量
 */
function dump($val)
{
    echo '<div style="border:1px solid #ccc;background:#FAFAFA;padding:5px 15px;z-index:1000;margin:5px;"> <pre>';
    var_dump($val);
    echo '</pre></div> ';
}

/**
 * 打印变量并终止 (调试用)
 * @param mixed $val 要打印的变量
 */
function dd($val)
{
    echo '<div style="border:1px solid #ccc;background:#FAFAFA;padding:5px 15px;z-index:1000;margin:5px;"> <pre>';
    var_dump($val);
    echo '</pre></div> ';
    exit;
}

/**
 * 加载模型
 * @param string $name 模型名称
 * @param string $module 模块名称
 * @return object 模型实例
 */
function D($name, $module = '')
{
    $module = empty($module) ? __MODULE__ : $module;
    $path = __APP__.'/'.$module.'/model/'.$name.'.php';

    file_exists($path) || Exception::error($name.'模型不存在!<br>', $path);

    include $path;
    return new $name;
}
