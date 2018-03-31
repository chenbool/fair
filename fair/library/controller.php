<?php
namespace app\library;
use app\library\view;

class Controller
{
	
	function __construct(){}

	// 模版分配
	public function display($tpl=null,$temp=[]){
		View::fetch($tpl,$temp);		
	}	
	
	// 返回ajax
	public function returnAjax($res){
		return die( json_encode($res) );		
	}

}