<?php
namespace app\help;

/**
 * 助手类
 * 提供调试辅助方法
 */
final class Help
{
    /**
     * 打印变量
     * @param mixed $val 要打印的变量
     */
    public static function dump($val)
    {
        echo '<div style="border:1px solid #ccc;background:#FAFAFA;padding:5px 15px;z-index:1000;margin:5px;"> <pre>';
        var_dump($val);
        echo '</pre></div> ';
    }
}
