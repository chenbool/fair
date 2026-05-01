<?php
namespace app\vendor;

/**
 * 图像处理类
 * 用于处理和操作图像，支持缩放、旋转、水印等操作
 *
 * 使用示例:
 * $image = new Image('image.png');
 * $image->rotate(90);
 * $image->resize(150, 150, 'crop');
 * $image->display();
 *
 * 或者:
 * $image = new Image('image.png');
 * $image->rotate(90);
 * $image->resize(150, 150, 'crop');
 * $image->save("newFilename", "../test");
 * $image->displayHTML();
 */

class Image {

    //Set variables
    protected $image = "";
    protected $imageInfo = array();
    protected $fileInfo = array();
    protected $tmpfile = array();
    protected $pathToTempFiles = "";
    protected $Watermark;
    protected $newFileType;
    protected $expires = 2592000;    // 30 days by default
    protected $lastModified = 0; 
    protected $isSourceImage = true;

    /**
     * 构造函数
     * @param string $image 图片路径
     * @param bool $isSourceImage 是否为源图片
     */
    public function __construct($image, $isSourceImage=true)
    {
        if(function_exists("sys_get_temp_dir")){
            $this->setPathToTempFiles(sys_get_temp_dir());
        }else{
            $this->setPathToTempFiles($_SERVER["DOCUMENT_ROOT"]);
        }
        
        $this->isSourceImage = (bool)$isSourceImage;

        if(file_exists($image)){
            $this->image  = $image;
            $this->readImageInfo();
        }else{
            die("File does not exist: ".$image);
        }
    }

    /**
     * 析构函数
     */
    public function __destruct()
    {
        if(file_exists($this->tmpfile)){
            unlink($this->tmpfile);
        }
    }

    /**
     * 设置缓存过期时间
     * @param int $expires 过期时间(秒)
     */
    public function setExpires($expires=0){
        $this->expires = intval($expires);
    }//function


    /**
     * Read and set some basic info about the image
     * @param string $image (path to image)
     */
    protected function readImageInfo()
    {
        $data = getimagesize($this->image);

        $this->imageInfo["width"] = $data[0];
        $this->imageInfo["height"] = $data[1];
        $this->imageInfo["imagetype"] = $data[2];
        $this->imageInfo["htmlWidthAndHeight"] = $data[3];
        $this->imageInfo["mime"] = $data["mime"];
        $this->imageInfo["channels"] = ( isset($data["channels"]) ? $data["channels"] : NULL );
        $this->imageInfo["bits"] = $data["bits"];
        if($this->isSourceImage && filemtime($this->image)!==time()){
            $this->lastModified = filemtime($this->image);
        }
        return true;
    }

    /************************************
    /* 设置器 (SETTERS)
    /************************************

    /**
     * 设置临时文件目录
     * @param string $path 目录路径
     */
    public function setPathToTempFiles($path)
    {
        $path = realpath($path).DIRECTORY_SEPARATOR;
        $this->pathToTempFiles = $path;
        $this->tmpfile = tempnam($this->pathToTempFiles, "classImagePhp_");

        return true;
    }

    /**
     * 设置新文件类型
     * @param string $newFileType 文件类型 (jpeg, png, bmp, gif, vnd.wap.wbmp, xbm)
     */
    public function setNewFileType($newFileType)
    {
        $this->newFileType = strtolower( $newFileType );

        return true;
    }

    /**
     * 设置新的主图片
     * @param string $pathToImage 图片路径
     */
    protected function setNewMainImage($pathToImage)
    {
        $this->image = $pathToImage;
        $this->readImageInfo();

        return true;
    }

    /************************************
    /* 操作 (ACTIONS)
    /************************************

    /**
     * 调整图片大小
     * @param int $max_width 最大宽度
     * @param int $max_height 最大高度
     * @param string $method 调整方式: fit(适应), crop(裁剪), fill(填充)
     * @param string $cropAreaLeftRight 裁剪区域水平位置: l(左), c(中), r(右)
     * @param string $cropAreaBottomTop 裁剪区域垂直位置: t(上), c(中), b(下)
     * @param int $jpgQuality JPG 质量
     * @param bool $enlarge 是否放大
     */
    public function resize($max_width, $max_height, $method="fit", $cropAreaLeftRight="c", $cropAreaBottomTop="c", $jpgQuality=75, $enlarge=false)
    {
        $width  = $this->getWidth();
        $height = $this->getHeight();

        $newImage_width  = $max_width;
        $newImage_height = $max_height;
        $srcX = 0;
        $srcY = 0;

        //Get ratio of max_width : max_height
        $ratioOfMaxSizes = $max_width / $max_height;

        //Want to fit in the area?
        if($method == "fit"){

            if($ratioOfMaxSizes >= $this->getRatioWidthToHeight()){
                $max_width = $max_height * $this->getRatioWidthToHeight();
            }else{
                $max_height = $max_width * $this->getRatioHeightToWidth();
            }

            //set image data again
            $newImage_width = $max_width;
            $newImage_height = $max_height;


        //or want to crop it?
        }elseif($method == "crop"){

            //set new max height or width
            if($ratioOfMaxSizes > $this->getRatioWidthToHeight()){
                $max_height = $max_width * $this->getRatioHeightToWidth();
            }else{
                $max_width = $max_height * $this->getRatioWidthToHeight();
            }

            //which area to crop?
            if (is_array($cropAreaLeftRight)) {
                $srcX    = $cropAreaLeftRight[0];
                if($ratioOfMaxSizes > $this->getRatioWidthToHeight()){
                    $width = $cropAreaLeftRight[1];
                }else{
                    $width = $cropAreaLeftRight[1] * $this->getRatioWidthToHeight();
                }
            } elseif ($cropAreaLeftRight == "r") {
                $srcX = $width - (($newImage_width / $max_width) * $width);
            } elseif ($cropAreaLeftRight == "c") {
                $srcX = ($width/2) - ((($newImage_width / $max_width) * $width) / 2);
            }

            if (is_array($cropAreaBottomTop)) {
                $srcY    = $cropAreaBottomTop[0];
                if ($ratioOfMaxSizes > $this->getRatioWidthToHeight()) {
                    $height = $cropAreaBottomTop[1] * $this->getRatioHeightToWidth();
                } else {
                    $height = $cropAreaBottomTop[1];
                }
            } elseif ($cropAreaBottomTop == "b") {
                $srcY = $height - (($newImage_height / $max_height) * $height);
            } elseif ($cropAreaBottomTop == "c") {
                $srcY = ($height/2) - ((($newImage_height / $max_height) * $height) / 2);
            }
        }

        if(!$enlarge && ($newImage_width>$width || $newImage_height>$height)){
                $newImage_width = $width;
                $max_width = $width;
                $newImage_height = $height;
                $max_height = $height;
        }

	//Let's get it on, create image!
        list($image_create_func, $image_save_func) = $this->getFunctionNames();

		// check if it is a jpg and if there are exif data about Orientation (e.g. on uploading an image from smartphone)
		if( $this->getMimeType() == "image/jpg" || $this->getMimeType() == "image/jpeg")
		{
			$exif = exif_read_data($this->image);
			if(!empty($exif['Orientation'])) {
				switch($exif['Orientation']) {
					case 8:
						$this->rotate(90, $jpgQuality);
					break;
					case 3:
						$this->rotate(180, $jpgQuality);
					break;
					case 6:
						$this->rotate(-90, $jpgQuality);
					break;
				}
			}
		}

        $imageC = ImageCreateTrueColor($newImage_width, $newImage_height);
        $newImage = $image_create_func($this->image);

        if($image_save_func == 'ImagePNG'){
            //http://www.akemapa.com/2008/07/10/php-gd-resize-transparent-image-png-gif/
            imagealphablending($imageC, false);
            imagesavealpha($imageC, true);
            $transparent = imagecolorallocatealpha($imageC, 255, 255, 255, 127);
            imagefilledrectangle($imageC, 0, 0, $newImage_width, $newImage_height, $transparent);
        }
        ImageCopyResampled($imageC, $newImage, 0, 0, $srcX, $srcY, $max_width, $max_height, $width, $height);

        //Set image
        if($image_save_func == "imageJPG" || $image_save_func == "ImageJPEG"){
            if(!$image_save_func($imageC, $this->tmpfile, $jpgQuality)){
                throw new Exception("Cannot save file ".$this->tmpfile);
            }
        }else{
            if(!$image_save_func($imageC, $this->tmpfile)){
                throw new Exception("Cannot save file ".$this->tmpfile);
            }
        }

        //Set new main image
        $this->setNewMainImage($this->tmpfile);

        //Free memory!
        imagedestroy($imageC);
    }

    /**
     * 添加水印图片
     * @param string $imageWatermark 水印图片路径
     */
    public function addWatermark($imageWatermark)
    {
        $this->Watermark = new self($imageWatermark, false);
        $this->Watermark->setPathToTempFiles($this->pathToTempFiles);

        return $this->Watermark;
    }


    /**
     * 写入水印到图片文件
     * @param int $opacity 透明度 (0-100)
     * @param int $marginH 水平边距 (像素)
     * @param int $marginV 垂直边距 (像素)
     * @param string $positionWatermarkLeftRight 水印水平位置: l(左), c(中), r(右)
     * @param string $positionWatermarkTopBottom 水印垂直位置: t(上), c(中), b(下)
     */
    public function writeWatermark($opacity=50, $marginH=0, $marginV=0, $positionWatermarkLeftRight="c", $positionWatermarkTopBottom="c")
    {
        //add Watermark
        list($image_create_func, $image_save_func) = $this->Watermark->getFunctionNames();
        $watermark = $image_create_func($this->Watermark->getImage());

        //get base image
        list($image_create_func, $image_save_func) = $this->getFunctionNames();
        $baseImage = $image_create_func($this->image);

        //Calculate margins
        if($positionWatermarkLeftRight == "r"){
            $marginH = imagesx($baseImage) - imagesx($watermark) - $marginH;
        }

        if($positionWatermarkLeftRight == "c"){
            $marginH = (imagesx($baseImage)/2) - (imagesx($watermark)/2) - $marginH;
        }

        if($positionWatermarkTopBottom == "b"){
            $marginV = imagesy($baseImage) - imagesy($watermark) - $marginV;
        }

        if($positionWatermarkTopBottom == "c"){
            $marginV = (imagesy($baseImage)/2) - (imagesy($watermark)/2) - $marginV;
        }

        //****************************
        //Add watermark and keep alpha channel of pngs.
        //The following lines are based on the code found on
        //http://ch.php.net/manual/en/function.imagecopymerge.php#92787
        //****************************

        // creating a cut resource
        $cut = imagecreatetruecolor(imagesx($watermark), imagesy($watermark));

        // copying that section of the background to the cut
        imagecopy($cut, $baseImage, 0, 0, $marginH, $marginV, imagesx($watermark), imagesy($watermark));

        // placing the watermark now
        imagecopy($cut, $watermark, 0, 0, 0, 0, imagesx($watermark), imagesy($watermark));
        imagecopymerge($baseImage, $cut, $marginH, $marginV, 0, 0, imagesx($watermark), imagesy($watermark), $opacity);

        //****************************
        //****************************

        //Set image
        if(!$image_save_func($baseImage, $this->tmpfile)){
            throw new Exception("Cannot save file ".$this->tmpfile);
        }

        //Set new main image
        $this->setNewMainImage($this->tmpfile);

        //Free memory!
        imagedestroy($baseImage);
        unset($Watermark);
    }

    /**
     * 旋转图片
     * @param int $degrees 旋转角度
     * @param int $jpgQuality JPG 质量
     */
    public function rotate($degrees, $jpgQuality=75)
    {
        list($image_create_func, $image_save_func) = $this->getFunctionNames();

        $source = $image_create_func($this->image);
        if(function_exists("imagerotate")){
            $imageRotated = imagerotate($source, $degrees, 0, true);
        }else{
            $imageRotated = $this->rotateImage($source, $degrees);
        }

        if($image_save_func == "ImageJPEG"){
            if(!$image_save_func($imageRotated, $this->tmpfile, $jpgQuality)){
                throw new Exception("Cannot save file ".$this->tmpfile);
            }
        }else{
            if(!$image_save_func($imageRotated, $this->tmpfile)){
                throw new Exception("Cannot save file ".$this->tmpfile);
            }
        }

        //Set new main image
        $this->setNewMainImage($this->tmpfile);

        return true;
    }

    /**
     * 输出图片到浏览器
     */
    public function display()
    {
        $mime = $this->getMimeType();
        header("Content-Type: ".$mime);
        header("Cache-Control: public");
        header("Expires: ". date("r",time() + ($this->expires)));
        if($this->lastModified>0){
            header("Last-Modified: ".gmdate("D, d M Y H:i:s", $this->lastModified)." GMT");
        }
        readfile($this->image);
    }

    /**
     * 输出 HTML 图片标签到浏览器
     * @param string $alt alt 属性
     * @param string $title title 属性
     * @param string $class class 属性
     * @param string $id id 属性
     * @param string $extras 额外属性
     */
    public function displayHTML($alt=false, $title=false, $class=false, $id=false, $extras=false)
    {
        print $this->getHTML($alt, $title, $class, $id, $extras);
    }

    /**
     * 生成 HTML 图片标签
     * @param string $alt alt 属性
     * @param string $title title 属性
     * @param string $class class 属性
     * @param string $id id 属性
     * @param string $extras 额外属性
     * @return string HTML 标签
     */
    public function getHTML($alt=false, $title=false, $class=false, $id=false, $extras=false)
    {
        $path = str_replace($_SERVER["DOCUMENT_ROOT"], "", $this->image);

        $code = '<img src="/'.$path.'" width="'.$this->getWidth().'" height="'.$this->getHeight().'"';
        if($alt   ){ $code .= ' alt="'.$alt.'"';}
        if($title ){ $code .= ' title="'.$title.'"';}
        if($class ){ $code .= ' class="'.$class.'"';}
        if($id    ){ $code .= ' id="'.$id.'"';}
        if($extras){ $code .= ' '.$extras;}
        $code .= ' />';

        return $code;
    }

    /**
     * 保存图片到文件
     * @param string $filename 文件名
     * @param string $path 保存路径
     * @param string $extension 文件扩展名
     * @return bool 保存是否成功
     */
    public function save($filename, $path="", $extension="")
    {
        //add extension
        if($extension == ""){
            $filename .= $this->getExtension(true);
        }else{
            $filename .= ".".$extension;
        }

        //add trailing slash if necessary
        if($path != ""){
            $path = realpath($path).DIRECTORY_SEPARATOR;
        }

        //create full path
        $fullPath = $path.$filename;

        //Copy file
        if(!copy($this->image, $fullPath)){
            throw new Exception("Cannot save file ".$fullPath);
        }

        //Set new main image
        $this->setNewMainImage($fullPath);

        return true;
    }

    /************************************
    /* 检测器 (CHECKERS)
    /************************************

    /**
     * 检查是否为 RGB 颜色模式
     * @return bool 是否为 RGB
     */
    public function isRGB()
    {
        if($this->imageInfo["channels"] == 3){
            return true;
        }
        return false;
    }

    /**
     * 检查是否为 CMYK 颜色模式
     * @return bool 是否为 CMYK
     */
    public function isCMYK()
    {
        if($this->imageInfo["channels"] == 4){
            return true;
        }
        return false;
    }

    /**
     * 检查宽高比是否符合要求
     * @param int $ratio1 宽度比
     * @param int $ratio2 高度比
     * @param bool $ignoreOrientation 是否忽略方向
     * @return bool 是否符合比例
     */
    public function checkRatio($ratio1, $ratio2, $ignoreOrientation=false)
    {
        $actualRatioWidthToHeight = $this->getRatioWidthToHeight();
        $shouldBeRatio = $ratio1 / $ratio2;

        if($actualRatioWidthToHeight == $shouldBeRatio){
            return true;
        }

        $actualRatioHeightToWidth = $this->getRatioHeightToWidth();
        if($ignoreOrientation && $actualRatioHeightToWidth == $shouldBeRatio){
            return true;
        }

        return false;
    }

    /************************************
    /* 获取器 (GETTERS)
    /************************************

    /**
     * 获取图像处理函数名
     * @return array 函数名数组
     */
    protected function getFunctionNames()
    {
        if (null == $this->newFileType) {
            $this->setNewFileType($this->getType());
        }

        switch ($this->getType()) {
            case 'jpg':
            case 'jpeg':
                $image_create_func = 'ImageCreateFromJPEG';
                break;

            case 'png':
                $image_create_func = 'ImageCreateFromPNG';
                break;

            case 'bmp':
                $image_create_func = 'ImageCreateFromBMP';
                break;

            case 'gif':
                $image_create_func = 'ImageCreateFromGIF';
                break;

            case 'vnd.wap.wbmp':
                $image_create_func = 'ImageCreateFromWBMP';
                break;

            case 'xbm':
                $image_create_func = 'ImageCreateFromXBM';
                break;

            default:
                $image_create_func = 'ImageCreateFromJPEG';
        }

        switch ($this->newFileType) {
            case 'jpg':
            case 'jpeg':
                $image_save_func = 'ImageJPEG';
                break;

            case 'png':
                $image_save_func = 'ImagePNG';
                break;

            case 'bmp':
                $image_save_func = 'ImageBMP';
                break;

            case 'gif':
                $image_save_func = 'ImageGIF';
                break;

            case 'vnd.wap.wbmp':
                $image_save_func = 'ImageWBMP';
                break;

            case 'xbm':
                $image_save_func = 'ImageXBM';
                break;

            default:
                $image_save_func = 'ImageJPEG';
        }

        return array($image_create_func, $image_save_func);
    }

    /**
     * 获取图片路径
     * @return string 图片路径
     */
    protected function getImage()
    {
        return $this->image;
    }

    /**
     * return info about the image
     */
    public function getImageInfo()
    {
        return $this->imageInfo;
    }

    /**
     * return info about the file
     */
    public function getFileInfo()
    {
        return $this->fileInfo;
    }

    /**
     * 获取图片宽度
     * @return int 宽度
     */
    public function getWidth()
    {
        return $this->imageInfo["width"];
    }

    /**
     * 获取图片高度
     * @return int 高度
     */
    public function getHeight()
    {
        return $this->imageInfo["height"];
    }

    /**
     * 获取图片扩展名
     * @param bool $withDot 是否包含点
     * @return string 扩展名
     */
    public function getExtension($withDot=false)
    {
        $extension = image_type_to_extension($this->imageInfo["imagetype"]);
        $extension = str_replace("jpeg", "jpg", $extension);
        if(!$withDot){
            $extension = substr($extension, 1);
        }

        return $extension;
    }

    /**
     * 获取图片 MIME 类型
     * @return string MIME 类型
     */
    public function getMimeType()
    {
        return $this->imageInfo["mime"];
    }

    /**
     * 获取图片类型
     * @return string 图片类型
     */
    public function getType()
    {
        return substr(strrchr($this->imageInfo["mime"], '/'), 1);
    }

    /**
     * 获取文件大小 (字节)
     * @return int 文件大小
     */
    public function getFileSizeInBytes()
    {
        return filesize($this->image);
    }

    /**
     * 获取文件大小 (KB)
     * @return float 文件大小
     */
    public function getFileSizeInKiloBytes()
    {
        $size = $this->getFileSizeInBytes();
        return $size/1024;
    }

    /**
     * Returns a human readable filesize
     * @author      wesman20 (php.net)
     * @author      Jonas John
     * @author      Manuel Reinhard
     * @link        http://www.jonasjohn.de/snippets/php/readable-filesize.htm
     * @link        http://www.php.net/manual/en/function.filesize.php
     */
    public function getFileSize()
    {
        $size = $this->getFileSizeInBytes();

        $mod = 1024;
        $units = explode(' ','B KB MB GB TB PB');
        for ($i = 0; $size > $mod; $i++) {
            $size /= $mod;
        }

        //round differently depending on unit to use
        if($i < 2){
            $size = round($size);
        }else{
            $size = round($size, 2);
        }

        return $size . ' ' . $units[$i];
    }

    /**
     * 获取宽高比 (宽度:高度)
     * @return float 宽高比
     */
    public function getRatioWidthToHeight()
    {
        return $this->imageInfo["width"] / $this->imageInfo["height"];
    }

    /**
     * 获取高宽比 (高度:宽度)
     * @return float 高宽比
     */
    public function getRatioHeightToWidth()
    {
        return $this->imageInfo["height"] / $this->imageInfo["width"];
    }

    /************************************
    /* OTHER STUFF
    /************************************

    /**
     * Replacement for imagerotate if it doesn't exist
     * As found on http://www.php.net/manual/de/function.imagerotate.php#93692
     */
    protected function rotateImage($img, $rotation)
    {
        $width = imagesx($img);
        $height = imagesy($img);
        switch($rotation) {
            case 90: $newimg= @imagecreatetruecolor($height , $width );break;
            case 180: $newimg= @imagecreatetruecolor($width , $height );break;
            case 270: $newimg= @imagecreatetruecolor($height , $width );break;
            case 0: return $img;break;
            case 360: return $img;break;
        }

        if($newimg) {
            for($i = 0;$i < $width ; $i++) {
                for($j = 0;$j < $height ; $j++) {
                    $reference = imagecolorat($img,$i,$j);
                    switch($rotation) {
                        case 90: if(!@imagesetpixel($newimg, ($height - 1) - $j, $i, $reference )){return false;}break;
                        case 180: if(!@imagesetpixel($newimg, $width - $i, ($height - 1) - $j, $reference )){return false;}break;
                        case 270: if(!@imagesetpixel($newimg, $j, $width - $i, $reference )){return false;}break;
                    }
                }
            }
            return $newimg;
        }
        return false;
    }
}
