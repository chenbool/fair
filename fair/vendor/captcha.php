<?php
namespace app\vendor;

/**
 * 验证码类
 * 生成图片验证码并验证
 */
class Captcha
{
    /**
     * 验证码字符
     * @var string
     */
    private $codes = '';

    /**
     * 图片宽度
     * @var int
     */
    public $width = 75;

    /**
     * 图片高度
     * @var int
     */
    public $height = 25;

    /**
     * 构造函数
     * 初始化生成4位随机验证码
     */
    function __construct()
    {
        $code = '0-1-2-3-4-5-6-7-8-9-A-B-C-D-E-F-G-H-I-J-K-L-M-N-O-P-Q-R-S-T-U-V-W-X-Y-Z';
        $codeArray = explode('-', $code);
        shuffle($codeArray);
        $this->codes = implode('', array_slice($codeArray, 0, 4));
    }

    /**
     * 生成验证码图片
     */
    public function CreateImg()
    {
        Header("Content-type: image/gif");
        $_SESSION['captcha'] = $this->codes;

        $img = imagecreate($this->width, $this->height);

        imagecolorallocate($img, 222, 222, 222);
        $testcolor1 = imagecolorallocate($img, 255, 0, 0);
        $testcolor2 = imagecolorallocate($img, 51, 51, 51);
        $testcolor3 = imagecolorallocate($img, 0, 0, 255);
        $testcolor4 = imagecolorallocate($img, 255, 0, 255);

        for ($i = 0; $i < 4; $i++) {
            imagestring($img, rand(5, 6), 8 + $i * 15, rand(2, 8), $this->codes[$i], rand(1, 4));
        }

        imagegif($img);
    }

    /**
     * 验证验证码
     * @param string $code 用户输入的验证码
     * @return bool 验证是否成功
     */
    public function check($code)
    {
        if ($code == $_SESSION['captcha']) {
            return true;
        } else {
            return false;
        }
    }
}
