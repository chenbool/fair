<?php
use app\library\Model;

class User extends Model
{
	public $tableName = 'user'; 
	public $pageSize  = 5;
	function __construct(){
		parent::__construct();
	}

	public function add(){
		$id = $this->database->insert($this->tableName, [    
			'name' => 'bool'.time(),  
		]);

		dump($id);
	}

	public function lists(){
		return $this->database->select($this->tableName, "name", [      
		    "LIMIT" => [0, 10] 
		]);
	}

	public function pages(){
		return $this->page();
	}
}