<?php
namespace app\vendor;

/**
 * Curl 网络请求类
 * 一个轻量级的网络操作类，实现GET、POST、UPLOAD、DOWNLOAD常用操作，支持链式写法
 *
 * 使用示例:
 * $curl = new Curl(); 或者 $curl = Curl::init();
 * $curl->url('目标网址');
 * $curl->post('变量名', '变量值')->post('多维数组')->url('目标网址');
 *
 * 任务结果状态:
 * if ($curl->error()) {
 *     echo $curl->message();
 * } else {
 *     $info = $curl->info();
 *     $content = $curl->data();
 * }
 */
class Curl
{
    /**
     * POST 数据
     * @var array
     */
    private $post;

    /**
     * 重试次数
     * @var int
     */
    private $retry = 0;

    /**
     * 自定义选项
     * @var array
     */
    private $custom = array();

    /**
     * 默认配置选项
     * @var array
     */
    private $option = array(
        'CURLOPT_HEADER'         => 0,
        'CURLOPT_TIMEOUT'        => 30,
        'CURLOPT_ENCODING'       => '',
        'CURLOPT_IPRESOLVE'      => 1,
        'CURLOPT_RETURNTRANSFER' => true,
        'CURLOPT_SSL_VERIFYPEER' => false,
        'CURLOPT_CONNECTTIMEOUT' => 10,
    );

    /**
     * 请求信息
     * @var array
     */
    private $info;

    /**
     * 返回数据
     * @var string
     */
    private $data;

    /**
     * 错误码
     * @var int
     */
    private $error;

    /**
     * 错误信息
     * @var string
     */
    private $message;

    /**
     * 单例实例
     * @var Curl
     */
    private static $instance;

    /**
     * 获取单例实例
     * @return self
     */
    public static function init()
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * 获取请求信息
     * @return array
     */
    public function info()
    {
        return $this->info;
    }

    /**
     * 获取返回数据
     * @return string
     */
    public function data()
    {
        return $this->data;
    }

    /**
     * 获取错误状态
     * @return int
     */
    public function error()
    {
        return $this->error;
    }

    /**
     * 获取错误信息
     * @return string
     */
    public function message()
    {
        return $this->message;
    }

    /**
     * 设置 POST 数据
     * @param array|string $data 数据
     * @param string|null $value 值
     * @return self
     */
    public function post($data, $value = null)
    {
        if (is_array($data)) {
            foreach ($data as $key => $val) {
                $this->post[$key] = $val;
            }
        } else {
            if ($value === null) {
                $this->post = $data;
            } else {
                $this->post[$data] = $value;
            }
        }
        return $this;
    }

    /**
     * 文件上传
     * @param string $field 字段名
     * @param string $path 文件路径
     * @param string $type 文件类型
     * @param string $name 文件名
     * @return self
     */
    public function file($field, $path, $type, $name)
    {
        $name = basename($name);
        if (class_exists('CURLFile')) {
            $this->set('CURLOPT_SAFE_UPLOAD', true);
            $file = curl_file_create($path, $type, $name);
        } else {
            $file = "@{$path};type={$type};filename={$name}";
        }
        return $this->post($field, $file);
    }

    /**
     * 保存内容到文件
     * @param string $path 保存路径
     * @return self
     * @throws Exception
     */
    public function save($path)
    {
        if ($this->error) {
            throw new Exception($this->message, $this->error);
        }
        $fp = @fopen($path, 'w');
        if ($fp === false) {
            throw new Exception('Failed to save the content', 500);
        }
        fwrite($fp, $this->data);
        fclose($fp);
        return $this;
    }

    /**
     * 设置请求 URL
     * @param string $url 目标网址
     * @return self
     * @throws Exception
     */
    public function url($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return $this->set('CURLOPT_URL', $url)->process();
        }
        throw new Exception('Target URL is required.', 500);
    }

    /**
     * 设置选项
     * @param array|string $item 选项
     * @param string|null $value 值
     * @return self
     */
    public function set($item, $value = null)
    {
        if (is_array($item)) {
            foreach ($item as $key => $val) {
                $this->custom[$key] = $val;
            }
        } else {
            $this->custom[$item] = $value;
        }
        return $this;
    }

    /**
     * 设置重试次数
     * @param int $times 重试次数
     * @return self
     */
    public function retry($times = 0)
    {
        $this->retry = $times;
        return $this;
    }

    /**
     * 处理请求
     * @param int $retry 当前重试次数
     * @return self
     */
    private function process($retry = 0)
    {
        $ch = curl_init();

        $option = array_merge($this->option, $this->custom);
        foreach ($option as $key => $val) {
            if (is_string($key)) {
                $key = constant(strtoupper($key));
            }
            curl_setopt($ch, $key, $val);
        }

        if ($this->post) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->convert($this->post));
        }

        $this->data = (string) curl_exec($ch);
        $this->info = curl_getinfo($ch);
        $this->error = curl_errno($ch);
        $this->message = $this->error ? curl_error($ch) : '';

        curl_close($ch);

        if ($this->error && $retry < $this->retry) {
            $this->process($retry + 1);
        }

        $this->post = array();
        $this->retry = 0;

        return $this;
    }

    /**
     * 转换数组格式
     * @param array $input 输入数组
     * @param string $pre 前缀
     * @return array
     */
    private function convert($input, $pre = null)
    {
        if (is_array($input)) {
            $output = array();
            foreach ($input as $key => $value) {
                $index = is_null($pre) ? $key : "{$pre}[{$key}]";
                if (is_array($value)) {
                    $output = array_merge($output, $this->convert($value, $index));
                } else {
                    $output[$index] = $value;
                }
            }
            return $output;
        }
        return $input;
    }
}
