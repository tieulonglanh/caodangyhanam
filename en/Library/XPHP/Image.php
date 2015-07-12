<?php
/**
 * XPHP Framework
 *
 * LICENSE
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * @category	XPHP
 * @package		XPHP_Image
 * @author		XWEB Dev Team
 * @copyright	Copyright (c) 2010-2011 XWEB. (http://xweb.vn)
 * @license		http://xphp.xweb.vn/license.html     GNU GPL License
 * @version		$Id: Image.php 20109 2011-22-08 02:05:09 Mr.UBKey $
 */
define("JPG", 0);
define("GIF", 1);
define("PNG", 2);
define("BMP", 3);
define("JPG_QUALITY", 100);
define("PNG_QUALITY", 0);
/**
 * XPHP_Exception.
 *
 * @see XPHP_Exception
 */
require_once 'XPHP/Exception.php';
/**
 * Lớp mở rộng hỗ trợ việc xử lý ảnh
 *
 * @category	XPHP
 * @package		XPHP_Image
 * @author		Mr.UBKey
 * @link		http://xphp.xweb.vn/user_guide/xphp_image.html
 */
class XPHP_Image
{
	/**
	 * Kiểu resize với chiều rộng và chiều cao chính xác 
	 * @var string
	 */
	const EXACT = 'exact';
	/**
	 * Kiểu resize với chiều rộng và chiều cao theo chân dung
	 * @var string
	 */
	const PORTRAIT = 'portrait';
	/**
	 * Kiểu resize với chiều rộng và chiều cao theo cảnh quan
	 * @var string
	 */
	const LANDSCAPE = 'landscape';
	/**
	 * Kiểu resize với chiều rộng và chiều cao được căn tự động
	 * @var string
	 */
	const AUTO = 'auto';
	/**
	 * Kiểu resize với chiều rộng và chiều cao bị cắt cho phù hợp
	 * @var string
	 */
	const CROP = 'crop';
	
	
    private $filename;
    private $image;
    private $width;
    private $height;
    private $data;
    private $copy;
    private $type;
    /**
     * @param $filename Tên file ảnh
     */
    function XPHP_Image ($filename)
    {
        if (! is_file($filename))
            throw new XPHP_Exception("File does not exist");
        $this->filename = $filename;
        $this->data = getimagesize($this->filename);
        switch ($this->data['mime']) {
            case 'image/pjpeg':
                $this->type = 'PJPG';
                break;
            case 'image/jpeg':
                $this->type = 'JPG';
                $this->image = imagecreatefromjpeg($this->filename);
                break;
            case 'image/gif':
                $this->type = 'GIF';
                $this->image = imagecreatefromgif($this->filename);
                break;
            case 'image/png':
                $this->type = 'PNG';
                $this->image = imagecreatefrompng($this->filename);
                break;
            case 'image/x-ms-bmp':
                $this->type = 'BMP';
                $this->image = imagecreatefromwbmp($this->filename);
                break;
            default:
                throw new XPHP_Exception("File format is not supported");
                break;
        }
        // *** Lấy ra độ rộng và độ cao gốc
        $this->width  = imagesx($this->image);
        $this->height = imagesy($this->image);
    }
    
    public function getInfo()
    {
    	return $this->data;
    }
    
    /**
     * Tạo một bản copy từ bản gốc của ảnh
     */
    public function duplicate ()
    {
        if (! isset($this->image))
            throw new XPHP_Exception("No image loaded");
        $this->copy = $this->image;
    }
    
    /**
     * Resize ảnh với nhiều lựa chọn
     * @param int $newWidth Chiều rộng
     * @param int $newHeight Chiều cao
     * @param string $option Chọn kiểu resize
     */
    public function resizeImage($newWidth, $newHeight, $option="auto")
    {
    
    	// *** Get optimal width and height - based on $option
    	$optionArray = $this->getDimensions($newWidth, $newHeight, strtolower($option));
    
    	$optimalWidth  = $optionArray['optimalWidth'];
    	$optimalHeight = $optionArray['optimalHeight'];
    
    	// *** Resample - create image canvas of x, y size
    	$this->copy = imagecreatetruecolor($optimalWidth, $optimalHeight);
    	imagecopyresampled($this->copy, $this->image, 0, 0, 0, 0, $optimalWidth, $optimalHeight, $this->width, $this->height);
    
    	// *** if option is 'crop', then crop too
    	if ($option == 'crop') {
    		$this->crop($optimalWidth, $optimalHeight, $newWidth, $newHeight);
    	}
    }
    
    private function getDimensions($newWidth, $newHeight, $option)
    {
    
    	switch ($option)
    	{
    		case 'exact':
    			$optimalWidth = $newWidth;
    			$optimalHeight= $newHeight;
    			break;
    		case 'portrait':
    			$optimalWidth = $this->getSizeByFixedHeight($newHeight);
    			$optimalHeight= $newHeight;
    			break;
    		case 'landscape':
    			$optimalWidth = $newWidth;
    			$optimalHeight= $this->getSizeByFixedWidth($newWidth);
    			break;
    		case 'auto':
    			$optionArray = $this->getSizeByAuto($newWidth, $newHeight);
    			$optimalWidth = $optionArray['optimalWidth'];
    			$optimalHeight = $optionArray['optimalHeight'];
    			break;
    		case 'crop':
    			$optionArray = $this->getOptimalCrop($newWidth, $newHeight);
    			$optimalWidth = $optionArray['optimalWidth'];
    			$optimalHeight = $optionArray['optimalHeight'];
    			break;
    	}
    	return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
    }
    
    private function getSizeByFixedHeight($newHeight)
    {
    	$ratio = $this->width / $this->height;
    	$newWidth = $newHeight * $ratio;
    	return $newWidth;
    }
    
    private function getSizeByFixedWidth($newWidth)
    {
    	$ratio = $this->height / $this->width;
    	$newHeight = $newWidth * $ratio;
    	return $newHeight;
    }
    
    private function getSizeByAuto($newWidth, $newHeight)
    {
    	if ($this->height < $this->width)
    		// *** Image to be resized is wider (landscape)
    	{
    		$optimalWidth = $newWidth;
    		$optimalHeight= $this->getSizeByFixedWidth($newWidth);
    	}
    	elseif ($this->height > $this->width)
    	// *** Image to be resized is taller (portrait)
    	{
    		$optimalWidth = $this->getSizeByFixedHeight($newHeight);
    		$optimalHeight= $newHeight;
    	}
    	else
    		// *** Image to be resizerd is a square
    	{
    		if ($newHeight < $newWidth) {
    			$optimalWidth = $newWidth;
    			$optimalHeight= $this->getSizeByFixedWidth($newWidth);
    		} else if ($newHeight > $newWidth) {
    			$optimalWidth = $this->getSizeByFixedHeight($newHeight);
    			$optimalHeight= $newHeight;
    		} else {
    			// *** Sqaure being resized to a square
    			$optimalWidth = $newWidth;
    			$optimalHeight= $newHeight;
    		}
    	}
    
    	return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
    }
    
    private function getOptimalCrop($newWidth, $newHeight)
    {
    
    	$heightRatio = $this->height / $newHeight;
    	$widthRatio  = $this->width /  $newWidth;
    
    	if ($heightRatio < $widthRatio) {
    		$optimalRatio = $heightRatio;
    	} else {
    		$optimalRatio = $widthRatio;
    	}
    
    	$optimalHeight = $this->height / $optimalRatio;
    	$optimalWidth  = $this->width  / $optimalRatio;
    
    	return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
    }
    
	private function crop($optimalWidth, $optimalHeight, $newWidth, $newHeight)  
	{  
	    // *** Find center - this will be used for the crop  
	    $cropStartX = ( $optimalWidth / 2) - ( $newWidth /2 );  
	    $cropStartY = ( $optimalHeight/ 2) - ( $newHeight/2 );  
	  
	    $crop = $this->copy;  
	    //imagedestroy($this->imageResized);  
	  
	    // *** Now crop from center to exact requested size  
	    $this->copy = imagecreatetruecolor($newWidth , $newHeight);  
	    imagecopyresampled($this->copy, $crop , 0, 0, $cropStartX, $cropStartY, $newWidth, $newHeight , $newWidth, $newHeight);  
	}
	
	/**
	 * Cắt ảnh
	 * @param int $x Vị trí cắt X
	 * @param int $y Vị trí cắt Y
	 * @param int $width Chiều rộng ảnh
	 * @param int $height Chiều cao ảnh
	 * @param int $newWidth Điểm cắt tới của chiều rộng
	 * @param int $newHeight Điểm cắt tới của chiều cao
	 */
	public function cropImage($x, $y, $width, $height, $newWidth, $newHeight)
	{
		$this->copy = imagecreatetruecolor($width , $height);
		imagecopyresampled($this->copy, $this->image , 0, 0, $x, $y, $newWidth, $newHeight, $width, $height);
	}
  
    /**
     * Resize ảnh theo số tối đa hoặc tối thiểu của chiều rộng và cao
     * @param int $wx Chiều rộng tối đa
     * @param int $hx Chiều cao tối đa
     * @param int $wm Chiều rộng tối thiểu
     * @param int $hm Chiều cao tối thiểu
     */
    public function resize ($wx, $hx, $wm = 0, $hm = 0)
    {
        if (! isset($this->image))
            throw new XPHP_Exception("No image loaded");
        if ($wx != $wm && $hx != $hm && $wm != 0 && $hm != 0)
            throw new XPHP_Exception("Bad dimensions specified");
        $r = $this->data[0] / $this->data[1];
        $rx = $wx / $hx;
        if ($wm == 0 || $hm == 0)
            $rm = $rx;
        else
            $rm = $wm / $hm;
        $dx = 0;
        $dy = 0;
        $sx = 0;
        $sy = 0;
        $dw = 0;
        $dh = 0;
        $sw = 0;
        $sh = 0;
        $w = 0;
        $h = 0;
        if ($r > $rx && $r > $rm) {
            $w = $wx;
            $h = $hx;
            $sw = $this->data[1] * $rx;
            $sh = $this->data[1];
            $sx = ($this->data[0] - $sw) / 2;
            $dw = $wx;
            $dh = $hx;
        } elseif ($r < $rm && $r < $rx) {
            $w = $wx;
            $h = $hx;
            $sh = $this->data[0] / $rx;
            $sy = ($this->data[1] - $sh) / 2;
            $sw = $this->data[0];
            $dw = $wx;
            $dh = $hx;
        } elseif ($r >= $rx && $r <= $rm) {
            $w = $wx;
            $h = $wx / $r;
            $dw = $wx;
            $dh = $wx / $r;
            $sw = $this->data[0];
            $sh = $this->data[1];
        } elseif ($r <= $rx && $r >= $rm) {
            $w = $hx * $r;
            $h = $hx;
            $dw = $hx * $r;
            $dh = $hx;
            $sw = $this->data[0];
            $sh = $this->data[1];
        } else {
            throw new XPHP_Exception("Can't resize the image");
        }
        $this->copy = imagecreatetruecolor($w, $h);
        imagecopyresampled($this->copy, $this->image, $dx, $dy, $sx, $sy, $dw, 
        $dh, $sw, $sh);
        return true;
    }
    /**
     * Lưu bản sao của ảnh. Nếu không truyền vào tên file hệ thống sẽ tự ghi đè lên bản gốc
     * @param string $filename Tên file ảnh
     * @param define $type Kiểu ảnh JPG, GIF, PNG, BMP
     * @param bool $fileExtInc Hệ thống tự động add thêm đuôi tệp tin ảnh hay không
     * @throws XPHP_Exception
     */
    public function save ($filename = false, $type = NULL, $fileExtInc = true)
    {
        //Lấy ra kiểu file mặc định lấy từ tập tin gốc
        if($type === NULL)
            $type = $this->type;
        
        if (! isset($this->copy))
            $this->copy = $this->image;
        if ($filename == NULL)
        {
            $fileExtInc = false;
            $filename = $this->filename;
        }
        switch ($type) {
            case GIF:
                if($fileExtInc)
                    $filename .= '.gif';
                imagegif($this->copy, $filename);
                return $filename;
                break;
            case PNG:
                if($fileExtInc)
                    $filename .= '.png';
                imagepng($this->copy, $filename, PNG_QUALITY);
                return $filename;
                break;
            case BMP:
                if($fileExtInc)
                    $filename .= '.bmp';
                imagewbmp($this->copy, $filename);
                return $filename;
                break;
            case JPG:
            default:
                if($fileExtInc)
                    $filename .= '.jpeg';
                imagejpeg($this->copy, $filename, JPG_QUALITY);
                return $filename;
                break;
        }
        throw new XPHP_Exception("Save failed");
    }
    /**
     * Chuyển đổi bản sao thành chuỗi và trả về chuỗi
     * @param string $type
     */
    public function getString ($type = NULL)
    {
        if (! isset($this->copy))
            throw new XPHP_Exception("No copy to return");
        $contents = ob_get_contents();
        if ($contents !== false)
            ob_clean();
        else
            ob_start();
        $this->show($type);
        $data = ob_get_contents();
        if ($contents !== false) {
            ob_clean();
            echo $contents;
        } else
            ob_end_clean();
        return $data;
    }
    /**
     * Hiển thị chuỗi được tạo ra bởi getString()
     * @param $type Kiểu của ảnh JPG, GIF, PNG, BMP mặc định là JPG
     */
    public function show ($type = NULL)
    {
        //Lấy ra kiểu file mặc định lấy từ tập tin gốc
        if($type === NULL)
            $type = $this->type;
        
        if (! isset($this->copy))
            throw new XPHP_Exception("No copy to show");
        switch ($type) {
            case GIF:
                header('Content-Type: image/gif');
                imagegif($this->copy, null);
                return true;
                break;
            case PNG:
                header('Content-Type: image/png');
                imagepng($this->copy, null, PNG_QUALITY);
                return true;
                break;
            case BMP:
                header('Content-Type: image/bmp');
                imagewbmp($this->copy, null);
                return true;
                break;
            case JPG:
            default:
                header('Content-Type: image/jpeg');
                imagejpeg($this->copy, null, JPG_QUALITY);
                return true;
                break;
        }
        throw new XPHP_Exception("Show failed");
    }
    public function __destruct ()
    {
        imagedestroy($this->image);
        if(is_resource($this->copy))
            imagedestroy($this->copy);
        $this->filename = null;
        $this->data = null;
    }
}