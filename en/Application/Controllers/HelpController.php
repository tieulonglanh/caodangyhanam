<?php
class HelpController extends XPHP_Controller
{
    public function indexAction()
    {
        $host = "http://{$_SERVER['HTTP_HOST']}";
        $host = str_replace('www.', '', $host);
        $photo_url = urldecode($this->url->decode($this->params[0]));
        $photo_url = str_replace($host, '', $photo_url);
        $photo_url = trim($photo_url, '/');
        $width = (int)$this->params[1];
        $height = (int)$this->params[2];

        //Xu ly lay file name va duong dan
        $filename = array_pop(explode('/', $photo_url));
        $thumburl = "Data/files/thumbnails/{$width}_{$height}";

        if(!is_file("{$thumburl}/{$filename}"))
        {
            return $this->redirect('avatar', array($this->url->encode($photo_url), $width, $height));
        }
        else
            return $this->redirectUrl("/{$thumburl}/{$filename}");
    }

    public function avatarAction()
    {
        $photo_url = urldecode($this->url->decode($this->params[0]));
        $photo_url = trim($photo_url, '/');
        $width = (int)$this->params[1];
        $height = (int)$this->params[2];
        //Resize anh
        $image = new XPHP_Image($photo_url);
        $imagesize = $image->getInfo();
        $oldw = $imagesize[0];
        $oldh = $imagesize[1];
        if(($oldw > $oldh && $width > $height) || ($oldw < $oldh && $width > $height) || ($oldw < $oldh && $width <= $height))
        	$image->resizeImage($width, $height, XPHP_Image::LANDSCAPE);
        else if(($oldw > $oldh && $width <= $height))
        	$image->resizeImage($width, $height, XPHP_Image::PORTRAIT);
        else
            $image->resizeImage($width, $height, XPHP_Image::LANDSCAPE);
        //Lưu vào thư mục thumb
        $filename = array_pop(explode('/', $photo_url));
        $thumburl = "Data/files/thumbnails/{$width}_{$height}";
        if(!is_file("{$thumburl}/{$filename}"))
        {
            if(!is_dir($thumburl))
                mkdir($thumburl, 0777, true);
            $image->save("{$thumburl}/{$filename}", NULL, false);
        }
        //Show ảnh
        $image->show();
        die;
    }
    
    public function avatarsubAction()
    {
        $photo_url = urldecode($this->url->decode($this->params[0]));
        $photo_url = trim($photo_url, '/');
        $width = (int)$this->params[1];
        $height = (int)$this->params[2];
        //Resize anh
        $image = new XPHP_Image($photo_url);
        $imagesize = $image->getInfo();
        $oldw = $imagesize[0];
        $oldh = $imagesize[1];
        if(($oldw > $oldh && $width > $height) || ($oldw < $oldh && $width > $height) || ($oldw < $oldh && $width <= $height))
            $image->resizeImage($width, $height, XPHP_Image::LANDSCAPE);
        else if(($oldw > $oldh && $width <= $height))
            $image->resizeImage($width, $height, XPHP_Image::PORTRAIT);
        else
            $image->resizeImage($width, $height, XPHP_Image::LANDSCAPE);
        //Lưu vào thư mục thumb
        $filename = array_pop(explode('/', $photo_url));
        $thumburl = "Data/files/thumbnails/{$width}_{$height}";
        if(!is_file("{$thumburl}/{$filename}"))
        {
            if(!is_dir($thumburl))
                mkdir($thumburl, 0777, true);
            $image->save("{$thumburl}/{$filename}", NULL, false);
        }
        //Show ảnh
        $image->show();
        die;
    }
}