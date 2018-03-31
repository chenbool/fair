<?php
namespace app\library;
use app\library\Exception;
/**
* 视图类
*/
final class View
{

	function __construct(){}
	
	/**
	 * [fetch 分配模板]
	 * @return [type] [description]
	 */
	public static function fetch($tpl=null,$arg=[]){
		//模板后缀
		$ext=isset($GLOBALS['config']['TPL_TEMPLATE_SUFFIX']) ? $GLOBALS['config']['TPL_TEMPLATE_SUFFIX'] : '.tpl';
		
		// 目录分割符
		$depr=isset($GLOBALS['config']['TPL_FILE_DEPR']) ? $GLOBALS['config']['TPL_FILE_DEPR'] : '/';

		// 当前模版
		if( empty($tpl) ){
			$tpl=__ACTION__;
		}

		// 分配变量
		if( !is_null($arg) && !empty($arg) ){
			error_reporting(0);
			extract( $arg);
		}
		
		// 主题
		$theme = isset($GLOBALS['config']['TPL_THEME']) ? $GLOBALS['config']['TPL_THEME'].'/' :'';
		$viewPath = isset($GLOBALS['config']['VIEW_PATH']) ? $GLOBALS['config']['VIEW_PATH'] :'';

		// 判断是否设置模板路径
		if( empty( $viewPath ) ){
			$path = __APP__.'/'.__MODULE__.'/view/'.$theme.lcfirst(CONTROLLER_NAME).$depr.$tpl.$ext;
		}else{
			$path = __ROOT__.'/'.$viewPath.$theme.lcfirst(CONTROLLER_NAME).$depr.$tpl.$ext;
		}

		//检测模板是否存在
		file_exists($path) || Exception::error($tpl.'模板不存在!<br>',$path);

		//引入视图
		include $path;

	}

}