<?php
namespace app\vendor;

/**
 * 文件上传类
 * 支持多文件上传，可限制文件类型、大小、扩展名
 *
 * 使用示例:
 * $upload = new Upload('jpg,png,gif', 2097152, 'image/jpeg,image/png,image/gif');
 * if ($upload->upload('/upload/')) {
 *     $fileInfo = $upload->getUploadFileInfo();
 * }
 */
class Upload
{
    /**
     * 允许的扩展名
     * @var array
     */
    public $allowExts = array();

    /**
     * 允许的文件类型
     * @var array
     */
    public $allowTypes = array();

    /**
     * 文件保存路径
     * @var string
     */
    public $savePath = '/upload/';

    /**
     * 子目录名称
     * @var string
     */
    public $subName;

    /**
     * 最大上传大小 (默认 2M = 2097152 字节)
     * @var int
     */
    public $maxSize = 2097152;

    /**
     * 最近一次的错误信息
     * @var string
     */
    private $error = '';

    /**
     * 自动检测文件 (默认开启)
     * @var bool
     */
    public $autoCheck = true;

    /**
     * 是否覆盖同名文件 (默认不覆盖)
     * @var bool
     */
    public $uploadReplace = false;

    /**
     * 文件上传信息
     * @var array
     */
    private $uploadFileInfo;

    /**
     * 构造函数
     * @param string $allowExts 允许的扩展名 (逗号分隔或数组)
     * @param int $maxSize 最大上传大小 (字节)
     * @param string $allowTypes 允许的文件类型 (逗号分隔或数组)
     */
    public function __construct($allowExts = '', $maxSize = '', $allowTypes = '')
    {
        $this->subName = date('Y-m-d', time());

        if (!empty($allowExts)) {
            if (is_array($allowExts)) {
                $this->allowExts = array_map('strtolower', $allowExts);
            } else {
                $this->allowExts = explode(',', strtolower($allowExts));
            }
        }

        if (!empty($maxSize) && is_numeric($maxSize)) {
            $this->maxSize = $maxSize;
        }

        if (!empty($allowTypes)) {
            if (is_array($allowTypes)) {
                $this->allowTypes = array_map('strtolower', $allowTypes);
            } else {
                $this->allowTypes = explode(',', strtolower($allowTypes));
            }
        }
    }

    /**
     * 保存单个文件
     * @param array $file 文件信息数组
     * @return bool 保存是否成功
     */
    private function save($file)
    {
        $filename = $file['savepath'].$file['savename'];
        if (!$this->uploadReplace && is_file($filename)) {
            $this->error = '文件已经存在！'.$filename;
            return false;
        }

        if (in_array(strtolower($file['extension']), array('gif', 'jpg', 'jpeg', 'bmp', 'png', 'swf')) && false === getimagesize($file['tmp_name'])) {
            $this->error = '非法图像文件';
            return false;
        }

        if (!move_uploaded_file($file['tmp_name'], $filename)) {
            $this->error = '文件上传保存错误！';
            return false;
        }
        return true;
    }

    /**
     * 上传所有文件
     * @param string $savePath 保存路径
     * @return bool 上传是否成功
     */
    public function upload($savePath = '')
    {
        if (empty($savePath)) {
            $savePath = $this->savePath.$this->subName.'/';
        }

        $savePath = rtrim($savePath, '/').'/';

        if (!is_dir($savePath)) {
            if (!mkdir($savePath)) {
                $this->error = "目录{$savePath}不存在";
                return false;
            }
        } else {
            if (!is_writeable($savePath)) {
                $this->error = "目录$savePath不可写";
                return false;
            }
        }

        $fileInfo = array();
        $isUpload = false;
        $files = $this->dealFiles($_FILES);
        foreach ($files as $key => $file) {
            if (!empty($file['name'])) {
                $file['key'] = $key;
                $file['extension'] = $this->getExt($file['name']);
                $file['savepath'] = $savePath;
                $file['savename'] = $this->getSaveName($file);

                if ($this->autoCheck) {
                    if (!$this->check($file)) {
                        return false;
                    }
                }

                if (!$this->save($file)) {
                    return false;
                }

                unset($file['tmp_name'], $file['error']);
                $fileInfo[] = $file;
                $isUpload = true;
            }
        }

        if ($isUpload) {
            $this->uploadFileInfo = $fileInfo;
            return true;
        } else {
            $this->error = '没有选择上传文件';
            return false;
        }
    }

    /**
     * 上传单个文件
     * @param array $file 文件信息数组
     * @param string $savePath 保存路径
     * @return bool 上传是否成功
     */
    public function uploadOne($file, $savePath = '')
    {
        if (empty($savePath)) {
            $savePath = $this->savePath.$this->subName.'/';
        }

        $savePath = rtrim($savePath, '/').'/';

        if (!is_dir($savePath)) {
            if (!mk_dir($savePath)) {
                $this->error = '上传目录'.$savePath.'不存在';
                return false;
            }
        } else {
            if (!is_writeable($savePath)) {
                $this->error = '上传目录'.$savePath.'不可写';
                return false;
            }
        }

        if (!empty($file['name'])) {
            $fileArray = array();
            if (is_array($file['name'])) {
                $keys = array_keys($file);
                $count = count($file['name']);
                for ($i = 0; $i < $count; $i++) {
                    foreach ($keys as $key) {
                        $fileArray[$i][$key] = $file[$key][$i];
                    }
                }
            } else {
                $fileArray[] = $file;
            }

            $fileInfo = array();
            foreach ($fileArray as $key => $file) {
                $file['extension'] = $this->getExt($file['name']);
                $file['savepath'] = $savePath;
                $file['savename'] = $this->getSaveName($file);

                if ($this->autoCheck) {
                    if (!$this->check($file)) {
                        return false;
                    }
                }

                if (!$this->save($file)) {
                    return false;
                }

                unset($file['tmp_name'], $file['error']);
                $fileInfo[] = $file;
            }

            $this->uploadFileInfo = $fileInfo;
            return true;
        } else {
            $this->error = '没有选择上传文件';
            return false;
        }
    }

    /**
     * 处理 $_FILES 数组信息，将多个文件分离
     * @param array $files 上传文件数组
     * @return array 处理后的文件数组
     */
    private function dealFiles($files)
    {
        $fileArray = array();
        $n = 0;
        foreach ($files as $file) {
            if (is_array($file['name'])) {
                $keys = array_keys($file);
                $count = count($file['name']);
                for ($i = 0; $i < $count; $i++) {
                    foreach ($keys as $key) {
                        $fileArray[$n][$key] = $file[$key][$i];
                    }
                    $n++;
                }
            } else {
                $fileArray[$n] = $file;
                $n++;
            }
        }

        return $fileArray;
    }

    /**
     * 获取文件扩展名
     * @param string $filename 文件名
     * @return string 扩展名
     */
    private function getExt($filename)
    {
        $pathinfo = pathinfo($filename);
        return $pathinfo['extension'];
    }

    /**
     * 生成保存文件名
     * @param array $file 文件信息数组
     * @return string 保存的文件名
     */
    private function getSaveName($file)
    {
        $saveName = md5(uniqid()).'.'.$file['extension'];
        return $saveName;
    }

    /**
     * 获取错误代码对应的错误信息
     * @param int $errorCode PHP 上传错误代码
     */
    private function error($errorCode)
    {
        switch ($errorCode) {
            case 1:
                $this->error = '上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值';
                break;
            case 2:
                $this->error = '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值';
                break;
            case 3:
                $this->error = '文件只有部分被上传';
                break;
            case 4:
                $this->error = '没有文件被上传';
                break;
            case 6:
                $this->error = '找不到临时文件夹';
                break;
            case 7:
                $this->error = '文件写入失败';
                break;
            default:
                $this->error = '未知上传错误！';
        }
        return;
    }

    /**
     * 检测文件是否合法
     * @param array $file 文件信息数组
     * @return bool 是否通过检测
     */
    private function check($file)
    {
        if ($file['error'] !== 0) {
            $this->error($file['error']);
            return false;
        }

        if (!$this->checkSize($file['size'])) {
            $this->error = '上传文件大小不符！';
            return false;
        }

        if (!$this->checkExt($file['extension'])) {
            $this->error = '上传文件类型不允许！';
            return false;
        }

        if (!$this->checkType($file['type'])) {
            $this->error = '上传文件MIME类型不允许！';
            return false;
        }

        if (!$this->checkUpload($file['tmp_name'])) {
            $this->error = '非法上传文件！';
            return false;
        }
        return true;
    }

    /**
     * 检测文件大小
     * @param int $size 文件大小
     * @return bool 大小是否合法
     */
    private function checkSize($size)
    {
        return $size < $this->maxSize;
    }

    /**
     * 检测文件扩展名
     * @param string $extension 文件扩展名
     * @return bool 扩展名是否允许
     */
    private function checkExt($extension)
    {
        if (!empty($this->allowExts)) {
            return in_array(strtolower($extension), $this->allowExts, true);
        }
        return true;
    }

    /**
     * 检测文件 MIME 类型
     * @param string $type 文件类型
     * @return bool 类型是否允许
     */
    private function checkType($type)
    {
        if (!empty($this->allowTypes)) {
            return in_array(strtolower($type), $this->allowTypes, true);
        }
        return true;
    }

    /**
     * 检测是否非法上传
     * @param string $filename 临时文件名
     * @return bool 是否为合法上传文件
     */
    private function checkUpload($filename)
    {
        return is_uploaded_file($filename);
    }

    /**
     * 获取文件上传成功后的信息
     * @return array 上传文件信息
     */
    public function getUploadFileInfo()
    {
        return $this->uploadFileInfo;
    }

    /**
     * 获取最近一次的错误信息
     * @return string 错误信息
     */
    public function getErrorMsg()
    {
        return $this->error;
    }
}
