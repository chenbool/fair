<?php
use app\vendor\Captcha;
use app\library\Controller;
/**
* Captcha
*/
class CaptchaController extends Controller{	
	public function index(){
		// var_dump( $_SESSION );

		$code = new Captcha();
		// $code->width=75;
		// $code->height=25;
		$code->CreateImg();
		// $code->check($code);
	}
}