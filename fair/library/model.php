<?php
namespace app\library;

use app\vendor\Page;
use app\library\Input;

/**
 * 模型基类
 * 基于 Medoo ORM 实现数据库操作
 */
class Model
{
    /**
     * 数据库实例
     * @var Medoo
     */
    public $database;

    /**
     * 表名
     * @var string
     */
    public $tableName;

    /**
     * 每页数量
     * @var int
     */
    public $pageSize = 10;

    /**
     * 构造函数
     * 初始化数据库连接
     */
    function __construct()
    {
        $config = $GLOBALS['database'];

        $this->database = new medoo([
            'database_type' => $config['DB_TYPE'],
            'database_name' => $config['DB_NAME'],
            'server'        => $config['DB_HOST'],
            'username'      => $config['DB_USER'],
            'password'      => $config['DB_PWD'],
            'charset'       => $config['DB_CHARSET'],
            'port'         => $config['DB_PORT'],
            'prefix'       => $config['DB_PREFIX'],
            'option'       => [
                \PDO::ATTR_CASE => \PDO::CASE_NATURAL
            ]
        ]);
    }

    /**
     * 分页方法
     * @param int $current 当前页
     * @param int $size 每页数量
     * @return array 包含列表和分页 HTML
     */
    public function page()
    {
        $size = $this->pageSize;
        $current = Input::get('p') ? Input::get('p') : 1;
        $count = $this->database->count($this->tableName);

        $page = new Page($count, $size);
        $page->AbsolutePage = $current;
        $page = $page->pageShow();

        $start = ($current - 1) * $size;
        $end = $size;

        $list = $this->database->select($this->tableName, "*", [
            "LIMIT" => [$start, $end]
        ]);

        return [
            'list' => $list,
            'page' => $page
        ];
    }
}
