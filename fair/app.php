<?php
namespace app;
use app\library\Route;
use app\library\Loader;
use app\library\Constant;

/**
* app
*/
final class app{
	
	function __construct(){}

	public static function run(){
		self::_init();
		self::constant();
		self::loader();
		self::route();
	}

	/**
	 * [_init [初始化]
	 */
	public static function _init(){
		session_start();
		date_default_timezone_set('PRC');
		header("Content-type: text/html; charset=utf-8");
	}

	/**
	 * [constant 定义常量]
	 */
	public static function constant(){
		new Constant();
	}

	/**
	 * [route 路由]
	 */
	public static function route(){
		new Route();	
	}
	
	/**
	 * [route 加载]
	 */
	public static function loader(){
		Loader::_init();	
		Loader::load(__ROOT__.'/help','function');	
	}

}
