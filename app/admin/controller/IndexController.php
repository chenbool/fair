<?php
// namespace app\home\controller;
use app\help\Help;
use app\library\Input;

/**
* IndexController
*/
class IndexController
{
	
	function __construct(){}

	public function index(){
		Help::dump('这是后台首页');
	}
}