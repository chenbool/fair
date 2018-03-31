## fair

自己写的mvc框架,依靠composer自动加载，可以通过composer安装第三方插件

http://localhost/fair/index.php/控制器/方法/参数名-参数值

http://localhost/fair/index.php/Index/index/id-1


## composer安装

	composer require bool/fair


## 请求

	use app\library\Input;
	  
	  Input::get()
	  
	  Input::post()
	  
	  Input::put()
	  
	  Input::delete()
	  

## 视图 

    use app\library\view;
    
	View::fetch('',[
		'name'	=>	'bool',
		'sex'	=>	'man'
	]);

	return view();
	
	return view('',[
		'name'	=>	'bool',
		'sex'	=>	'man'
	]);	

	$this->display('',[
		'name'	=>	'bool',
		'sex'	=>	'man'
	]);
	

## 模型操作 

	D()
	$model=D('User');
	$model->add();
	$model->lists();
	$page=$model->page();
  


## 打印

	  dd()
	  
	  dump()
	  
	  use app\help\Help
	  
	  Help::dump()


## 基本配置


	return array(

		//'配置项'=>'配置值'	
		'URL_ARG_DEPR'		=>	'-',//修改URL的分隔符
		'URL_HTML_SUFFIX'	=>	'html',

		'TPL_L_DELIM'=>'<{', //修改左定界符
		'TPL_R_DELIM'=>'}>', //修改右定界符
		'URL_PATHINFO_DEPR'=>'-',//修改URL的分隔符
		'SHOW_PAGE_TRACE'=>true,//开启页面Trace
		'TMPL_TEMPLATE_SUFFIX'=>'.php',//更改模板文件后缀名
		'TPL_FILE_DEPR'=>'_',//修改模板文件目录层次
		'TPL_ENGINE_TYPE' =>'PHP'
	);

	
## 文件上传

		$upload=new Upload();
		$upload->maxSize  = 3*pow(2,20) ;// 设置附件上传大小 3M  默认为2M
		$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');	//设置附件上传类型   默认为空不检测扩展
		$upload->savePath =  __ROOT__.'/public/upload/';	//设置附件上传目录   默认上传目录为 ./uploads/

		if(!$upload->upload()) {
			// 上传错误提示错误信息
			$upload->getErrorMsg();
			dd( $upload->getErrorMsg() );
		}else{
			// 上传成功 获取上传文件信息
			$info =  $upload->getUploadFileInfo();
			dd( $info );
		}



## 验证码

		$code = new Captcha();
		// $code->width=75;
		// $code->height=25;
		$code->CreateImg();
		// $code->check($code);		



## 图片处理


		$path=__ROOT__.'/public/upload/1.png';
		// dump( $path );
		$image = new Image($path);
		$image->rotate(90);
		$image->resize(150,150,'crop'); 
		$image->save("newFilename", __ROOT__."/public/upload");




## CURL

		$curl = new Curl;
		$res=$curl->url('http://medoo.lvtao.net/doc.query.php');

		// 任务结果状态
		if ($curl->error()) {
		    echo $curl->message();
		} else {
		    // 任务进程信息
		    $info = $curl->info();
		    
		    // 任务结果内容
		    $content = $curl->data();
		    echo $content;
		}
		
