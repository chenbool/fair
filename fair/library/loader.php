<?php
namespace app\library;
/**
* 路由类
*/
final class Loader
{
	
	function __construct(){}

	/**
	 * [_init 加载配置目录下文件]
	 * @return [type]         [description]
	 */
	public static function _init(){
		$conf=include __ROOT__.'/config/config.php';
		$GLOBALS['config'] = $conf;

		$database=include __ROOT__.'/config/database.php';
		$GLOBALS['database'] = $database;
	}
	

	/**
	 * [load description]
	 * @param  [type] $dir  [目录]
	 * @param  [type] $file [文件名]
	 * @param  string $ext  [后缀]
	 * @return [array]       []
	 * Loader::load('config','config');
	 */
	public static function load($dir,$file,$ext='php'){
		return include $dir.'/'.$file.'.'.$ext;
	}



	public static function config($module,$file="config"){
		$conf=$GLOBALS['config'];

		// 检测app模块目录下是否有config.php
		if( file_exists( __APP__.'/'.$module.'/'.$file.'.php' ) ){
			$config=include __APP__.'/'.$module.'/'.$file.'.php';
			$conf = array_merge($conf,$config);
		}
		$GLOBALS['config'] = $conf;
		return $GLOBALS['config'];
	}

}