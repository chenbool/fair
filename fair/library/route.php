<?php
namespace app\library;
use app\help\Help;
use app\library\Loader;
use app\library\Exception;
/**
* 路由类
*/
final class Route
{
	public $config;
	function __construct(){
		

		self::dispatch( $this->config );
	}

	// 处理路由
	private static function dispatch(){

		// 判断是否有 path_info
		if(  !isset( $_SERVER['PATH_INFO'] )  ){
			$module = defined('BIND_MODULE') ? BIND_MODULE : 'home';
			$controller="index";
			$action='index';
			Loader::config($module);
		}else{

			// 获取path_info
			$pathInfo=trim( $_SERVER['PATH_INFO'] ,'/');

			
			// 拆分url
			$route = explode('/',$pathInfo); 

			// 检测用户是否绑定模块
			if( defined('BIND_MODULE') ){
				$module =  BIND_MODULE;
				$controller=$route[0];
				unset($route[0]);
				$action=$route[1];
				unset($route[1]);
			}else{
				$module=$route[0];
				unset($route[0]);
				$controller=$route[1];
				unset($route[1]);
				$action=$route[2];
				unset($route[2]);				
			}

			// 获取模块下配置
			$config=Loader::config($module);

			// 过滤完的参数
			$arg=array_values($route);

			// 去除url后缀
			if( isset($config['URL_HTML_SUFFIX']) ){
				$arg=str_replace('.'.$config['URL_HTML_SUFFIX'],"",$route);
			}	
			
			// 判断是否设置了参数分隔符
			if( isset($config['URL_ARG_DEPR']) ){

				//循环拆分参数
				foreach ($arg as $key => $value) {
					$args = explode($config['URL_ARG_DEPR'],$value); 
					//封装到GET
					foreach ($args as $k => $v) {
						if($k%2==0){
							$val = isset($args[$k+1]) ? $args[$k+1] : '';
							$_GET[ $args[$k] ] = $val;
						}else{
							continue;
						}
					}
					
				}
				

			}else{
				// 把参数存入GET
				foreach ($arg as $k => $v) {
					if($k%2==0){
						$val = isset($arg[$k+1]) ? $arg[$k+1] : '';
						$_GET[ $arg[$k] ] = $val;
					}else{
						continue;
					}
				}
			}


		}


		self::_load($module,$controller,$action);

	}


	// 加载控制器
	private static function _load($module,$controller,$action){

		define('CONTROLLER_NAME', ucfirst($controller));
		
		// 控制器首字母大写
		$controller=ucfirst($controller).'Controller';
		//收字母小写 
		$action=lcfirst($action);

		define('__MODULE__', $module);
		define('__ACTION__', $action);
		define('__CONTROLLER__', $controller);
		

		//载入路径
		$path = __APP__.'/'.$module.'/controller/'.$controller.'.php';

		// 判断控制器是否存在
		if( is_file($path) ){
			require  $path;		
			$controller=new $controller();

			// 验证方法是否存在
			$methods = get_class_methods($controller);
			if( in_array($action, $methods) ){
				$controller->$action();		
			}else{
				Exception::error($action.'方法不存在!<br>',$path);
			}
			
		}else{
			Exception::error($controller.'控制器不存在!<br>',$path);
		}


	}



}