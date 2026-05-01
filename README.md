# Fair

> 基于 Composer 自动加载的 PHP MVC 框架 | 支持 Composer 第三方插件

## 项目概述

| 属性 | 说明 |
|------|------|
| 框架类型 | PHP MVC 框架 |
| 自动加载 | Composer PSR-4 |
| 路由风格 | PATH_INFO |
| 模板引擎 | PHP / 自定义 |

## 目录结构

```
fair/
├── app/                      # 应用目录
│   ├── admin/               # 后台模块
│   │   └── controller/      # 控制器
│   └── home/               # 前台模块
│       ├── controller/      # 控制器
│       ├── model/          # 模型
│       ├── view/           # 视图
│       └── config.php      # 模块配置
├── config/                  # 配置目录
│   ├── config.php          # 应用配置
│   └── database.php        # 数据库配置
├── fair/                   # 核心框架
│   ├── app.php            # 启动类
│   ├── library/           # 框架类库
│   │   ├── controller.php # 控制器基类
│   │   ├── model.php     # 模型基类
│   │   ├── view.php      # 视图类
│   │   ├── route.php     # 路由类
│   │   ├── input.php     # 输入类
│   │   ├── loader.php    # 加载器
│   │   ├── medoo.php     # ORM
│   │   └── constant.php  # 常量
│   └── vendor/            # 扩展工具
│       ├── captcha.php    # 验证码
│       ├── curl.php       # CURL
│       ├── image.php      # 图片处理
│       ├── upload.php     # 文件上传
│       ├── page.php       # 分页
│       ├── validator.php  # 验证器
│       └── xss.php       # XSS 过滤
├── help/                   # 助手函数
├── public/                  # 公共资源
│   ├── index.php          # 前台入口
│   ├── admin.php         # 后台入口
│   └── upload/           # 上传目录
├── template/               # 模板目录
├── vendor/                 # Composer 依赖
└── composer.json
```

## 快速开始

### 1. 安装

```bash
# 克隆项目
git clone https://github.com/chenbool/fair.git

# 安装依赖
composer install
```

### 2. URL 路由

```
http://localhost/fair/index.php/控制器/方法/参数名-参数值
```

示例：
```
http://localhost/fair/index.php/Index/index/id-1
```

## 请求处理

```php
use app\library\Input;

// 获取 GET 参数
Input::get()

// 获取 POST 参数
Input::post()

// 获取 PUT 参数
Input::put()

// 获取 DELETE 参数
Input::delete()
```

## 视图

```php
use app\library\View;

// 方式一：return 视图
return view();

return view('', [
    'name' => 'bool',
    'sex'  => 'man'
]);

// 方式二：fetch 获取视图内容
View::fetch('', [
    'name' => 'bool',
    'sex'  => 'man'
]);

// 方式三：display 渲染视图
$this->display('', [
    'name' => 'bool',
    'sex'  => 'man'
]);
```

## 模型操作

```php
use app\library\Model;

// 获取模型实例
$model = D('User');

// 模型方法
$model->add();       // 添加数据
$model->lists();     // 获取列表
$page = $model->page(); // 分页数据
```

## 调试函数

| 函数 | 说明 |
|------|------|
| `dump($var)` | 打印变量 |
| `dd($var)` | 打印并终止 |
| `Help::dump($var)` | 助手类打印 |

## 配置文件

编辑 `config/config.php`：

```php
return [
    // URL 配置
    'URL_ARG_DEPR'       => '-',     // URL 参数分隔符
    'URL_HTML_SUFFIX'   => 'html',  // URL 后缀
    'URL_PATHINFO_DEPR' => '-',     // PATH_INFO 分隔符

    // 模板配置
    'TPL_L_DELIM'       => '<{',    // 模板左定界符
    'TPL_R_DELIM'       => '}>',    // 模板右定界符
    'TMPL_TEMPLATE_SUFFIX' => '.php', // 模板文件后缀
    'TPL_FILE_DEPR'     => '_',     // 模板目录分隔符
    'TPL_ENGINE_TYPE'   => 'PHP',   // 模板引擎

    // 调试配置
    'SHOW_PAGE_TRACE'   => true,    // 开启页面 Trace
];
```

## 文件上传

```php
$upload = new Upload();
$upload->maxSize  = 3 * pow(2, 20);  // 3MB
$upload->allowExts = ['jpg', 'gif', 'png', 'jpeg'];
$upload->savePath = __ROOT__.'/public/upload/';

if (!$upload->upload()) {
    dd($upload->getErrorMsg());
} else {
    $info = $upload->getUploadFileInfo();
    dd($info);
}
```

## 验证码

```php
$code = new Captcha();
$code->CreateImg();
```

## 图片处理

```php
$path = __ROOT__.'/public/upload/1.png';
$image = new Image($path);
$image->rotate(90);
$image->resize(150, 150, 'crop');
$image->save("newFilename", __ROOT__."/public/upload");
```

## CURL 请求

```php
$curl = new Curl;
$res = $curl->url('http://example.com/api');

if ($curl->error()) {
    echo $curl->message();
} else {
    $info = $curl->info();    // 请求信息
    $content = $curl->data(); // 响应内容
    echo $content;
}
```

## 数据库配置

编辑 `config/database.php`：

```php
return [
    'database_type' => 'mysql',
    'database_name' => 'your_db',
    'server'        => 'localhost',
    'username'      => 'root',
    'password'      => 'root',
    'charset'       => 'utf8',
    'prefix'        => '',
    'port'          => 3306,
];
```

## 依赖

- [Medoo](https://medoo.in/) - 轻量级 PHP ORM
