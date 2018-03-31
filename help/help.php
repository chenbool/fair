<?php
namespace app\help;
/**
* 辅助类
*/
final class Help
{
	public static function dump($val){
		echo '<div style="border:1px solid #ccc;background:#FAFAFA;padding:5px 15px;z-index:1000;margin:5px;"> <pre>';
		var_dump($val);
		echo '</pre></div> ';
	}
	
}