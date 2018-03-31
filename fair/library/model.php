<?php
namespace app\library;
use app\vendor\Page;
use app\library\Input;
/**
 * 模型
 * http://medoo.lvtao.net/doc.last_query.php
 */
class Model{
	
	public $database;
	public $tableName;
	public $pageSize=10;
	function __construct()
	{
		$config= $GLOBALS['database'];
		
		$this->database = new medoo([
		    // 必须配置项
		    'database_type' => $config['DB_TYPE'],
		    'database_name' => $config['DB_NAME'],
		    'server' 		=> $config['DB_HOST'],
		    'username'		=> $config['DB_USER'],
		    'password' 		=> $config['DB_PWD'],
		    'charset' 		=> $config['DB_CHARSET'],
		 
		    // 可选参数
		    'port' 			=> $config['DB_PORT'],
		 
		    // 可选，定义表的前缀
		    'prefix' 		=> $config['DB_PREFIX'],
		 
		    // 连接参数扩展, 更多参考 http://www.php.net/manual/en/pdo.setattribute.php
		    'option' 		=> [
		        \PDO::ATTR_CASE => \PDO::CASE_NATURAL
		    ]
		]);
	}

	/**
	 * [page 分页]
	 * @param  integer $current [当前页]
	 * @param  integer $size    [每页数量]
	 * @return [type]           [description]
	 */
	public function page(){
		$size=$this->pageSize;
		$current = Input::get('p') ? Input::get('p') : 1;
		$count=$this->database->count($this->tableName);

		// page
		$page = new Page($count,$size);
		$page->AbsolutePage = $current; //当前锁定页
		$page=$page->pageShow(); //当前锁定页


		// limit
		$start=($current-1)*$size;
		$end=$size;
	
		$list = $this->database->select($this->tableName,"*",[
			"LIMIT" => [$start, $end]
		]);	

		return [
			'list'	=>	$list,
			'page'	=>	$page
		];
	}


}