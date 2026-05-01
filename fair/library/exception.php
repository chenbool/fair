<?php
namespace app\library;

/**
 * 异常处理类
 * 统一处理框架错误信息输出
 */
final class Exception
{
    /**
     * 构造函数
     */
    function __construct(){}

    /**
     * 输出错误信息
     * @param string $title 错误标题
     * @param string $msg 错误详情
     */
    public static function error($title, $msg)
    {
        echo '<div style="border:1px solid #ccc;background:#FAFAFA;padding:5px 15px;z-index:1000;margin:5px;"> <pre>';
        echo '<h2>'.$title.'</h2>';
        echo $msg;
        echo '</pre></div> ';
        exit;
    }
}
