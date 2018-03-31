<?php
namespace app\library;
/**
* 路由类
*/
final class Input
{
	
	function __construct(){}

	/**
	 * [get description]
	 * @param  string $name [description]
	 * @return [type]       [description]
	 */
	public static function get($name=''){
		if( empty($name) ){
			return self::filter( $_GET );
		}else{
			$val = isset($_GET[$name]) ?  $_GET[$name] :null;
			return self::filter( $val );
		}
		
	}

	/**
	 * [post description]
	 * @param  string $name [description]
	 * @return [type]       [description]
	 */
	public static function post($name=''){
		if( empty($name) ){
			return self::filter( $_GET );
		}else{
			$val = isset($_GET[$name]) ?  $_GET[$name] :null;
			return self::filter( $val );
		}
	}


	public static function file($name=''){
		if( empty($name) ){
			return self::filter( $_GET );
		}else{
			$val = isset($_GET[$name]) ?  $_GET[$name] :null;
			return self::filter( $val );
		}
		
	}


	/**
	 * [filter 过滤]
	 * @param  [type] $val [description]
	 * @return [type]      [description]
	 */
	public static function filter($val){
		if( is_array($val) ){
			foreach ($val as $key => $value) {
				$val[$key] = htmlentities($value);
			}
			return $val;
		}else{
			$val = htmlentities($val);
			return $val;			
		}

	}

}