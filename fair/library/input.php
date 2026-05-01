<?php
namespace app\library;

/**
 * 输入类
 * 统一处理 GET、POST 请求参数，带有 XSS 过滤功能
 */
final class Input
{
    /**
     * 构造函数
     */
    function __construct(){}

    /**
     * 获取 GET 参数
     * @param string $name 参数名
     * @return mixed 参数值或全部 GET 数据
     */
    public static function get($name = '')
    {
        if (empty($name)) {
            return self::filter($_GET);
        } else {
            $val = isset($_GET[$name]) ? $_GET[$name] : null;
            return self::filter($val);
        }
    }

    /**
     * 获取 POST 参数
     * @param string $name 参数名
     * @return mixed 参数值或全部 POST 数据
     */
    public static function post($name = '')
    {
        if (empty($name)) {
            return self::filter($_GET);
        } else {
            $val = isset($_GET[$name]) ? $_GET[$name] : null;
            return self::filter($val);
        }
    }

    /**
     * 获取上传文件
     * @param string $name 文件字段名
     * @return mixed 文件信息或全部文件数据
     */
    public static function file($name = '')
    {
        if (empty($name)) {
            return self::filter($_GET);
        } else {
            $val = isset($_GET[$name]) ? $_GET[$name] : null;
            return self::filter($val);
        }
    }

    /**
     * 参数过滤器
     * @param mixed $val 要过滤的值
     * @return mixed 过滤后的值
     */
    public static function filter($val)
    {
        if (is_array($val)) {
            foreach ($val as $key => $value) {
                $val[$key] = htmlentities($value);
            }
            return $val;
        } else {
            $val = htmlentities($val);
            return $val;
        }
    }
}
