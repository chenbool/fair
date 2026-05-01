<?php
namespace app\library;

use app\library\view;

/**
 * 控制器基类
 * 所有业务控制器的父类，提供视图渲染和 JSON 返回功能
 */
class Controller
{
    /**
     * 构造函数
     */
    function __construct(){}

    /**
     * 渲染视图模板
     * @param string|null $tpl 模板名称
     * @param array $temp 传递给视图的变量
     */
    public function display($tpl = null, $temp = [])
    {
        View::fetch($tpl, $temp);
    }

    /**
     * 返回 JSON 数据
     * @param mixed $res 要返回的数据
     */
    public function returnAjax($res)
    {
        return die(json_encode($res));
    }
}
