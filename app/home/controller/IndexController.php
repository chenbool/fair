<?php
// namespace app\home\controller;
use app\library\view;
use app\library\Input;
use app\library\Controller;

/**
* IndexController
*/
class IndexController extends Controller
{
	
	function __construct(){}

	public function index(){
		$arg = Input::get();
		dump( $arg );

		// $const=get_defined_constants(1);
		// dump( $const['user'] );

		// Help::dump( $GLOBALS['config'] );
		// Help::dump( $GLOBALS['database'] );
		
		$model=D('User');
		// $model->add();
		// dump( $model->lists() );
		$page=$model->page();
		// dump( $page['list'] );
		// echo $page['page'];
		

		// 分配变量
		// View::fetch('',[
		// 	'name'	=>	'bool',
		// 	'sex'	=>	'man'
		// ]);
		
		return view('',[
			'name'	=>	'bool',
			'sex'	=>	'man',
			'list'	=>	$page['list'],
			'page'	=>	$page['page']
		]);	

		// $this->display('',[
		// 	'name'	=>	'bool',
		// 	'sex'	=>	'man'
		// ]);

	}
}