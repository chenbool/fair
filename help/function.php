<?php
use app\library\View;
use app\library\Medoo;
use app\library\Exception;

function view($tpl='',$arg=[]){
	View::fetch($tpl,$arg);	
}

function dump($val){
	echo '<div style="border:1px solid #ccc;background:#FAFAFA;padding:5px 15px;z-index:1000;margin:5px;"> <pre>';
	var_dump($val);
	echo '</pre></div> ';
}


function dd($val){
	echo '<div style="border:1px solid #ccc;background:#FAFAFA;padding:5px 15px;z-index:1000;margin:5px;"> <pre>';
	var_dump($val);
	echo '</pre></div> ';
	exit;
}


/**
 * [D 载入模型]
 * @param [type] $name [description]
 */
function D($name,$module=''){
		$module = empty($module) ? __MODULE__: $module;
		$path = __APP__.'/'.$module.'/model/'.$name.'.php';

		file_exists($path) || Exception::error($name.'模型不存在!<br>',$path);;

		include $path;
		return new $name;	
}
