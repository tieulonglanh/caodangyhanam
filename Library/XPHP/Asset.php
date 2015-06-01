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
 * @package		XPHP_Asset
 * @author		XWEB Dev Team
 * @copyright	Copyright (c) 2010-2011 XWEB. (http://xweb.vn)
 * @license		http://xphp.xweb.vn/license.html     GNU GPL License
 * @version		$Id: Asset.php 20117 2011-22-08 02:05:09 Mr.UBKey $
 */
/**
 * XPHP_Compressor.
 *
 * @see XPHP_Compressor
 */
require_once 'XPHP/Compressor.php';
/**
 * Lớp hỗ trợ quản lý Javascript và Css
 *
 * @category	XPHP
 * @package		XPHP_Asset
 * @author		Mr.UBKey
 * @link		http://xphp.xweb.vn/user_guide/xphp_asset.html
 */
class XPHP_Asset
{
    /**
     * Mã Js trong Asset
     * @var string
     */
    private $_js;
    /**
     * Mã Css trong Asset
     * @var string
     */
    private $_css;
    /**
     * Lớp hỗ trợ nén dữ liệu
     * @var XPHP_Helper_Compressor
     */
    public $compressor;
    /**
     * Mảng chứa đường dẫn các file js load
     * @var array
     */
    private $_jsPaths;
    /**
     * Mảng chứa đường dẫn các file css load
     * @var array
     */
    private $_cssPaths;
    /**
     * Lớp hỗ trợ cache
     * @var XPHP_Cache_Simple
     */
    private $_cache;
    /**
     * Tên asset
     * @var string
     */
    private $_name;
    /**
     * Mảng chứa các Block Js
     * @var array
     */
    private $_jsBlocks;
    /**
     * Biến kiểm tra xem đã mở một block chưa
     * @var bool
     */
    private $_blockBegin;
    /**
     * Mảng chứa các Block Css
     * @var array
     */
    private $_cssBlocks;
    /**
     * Hỗ trợ url cho việc lấy src file cache
     * @var XPHP_Url
     */
    private $_url;
    public function __construct ()
    {
        $this->compressor = new XPHP_Compressor();
        $this->_jsPaths = array();
        $this->_cssPaths = array();
        $this->_jsBlock = array();
        $this->_cssBlock = array();
        $this->_blockBegin = false;
        //Mặc định tên của Asset là một chuỗi ngẫu nhiên nếu không được thiết lập tên.
        require_once 'XPHP/String.php';
        $this->_name = XPHP_String::randomString(8);
        //Nếu không có cache khởi tạo cache mặc định cache 1 năm
        $this->_cache = new XPHP_Cache_Simple('1y', false, "ast.", 'Cache/Simple');
    }
    /**
     * Thiết lập tên asset
     * @return XPHP_Helper_Asset
     */
    public function name ($name)
    {
        $this->_name = $name;
        return $this;
    }
    /**
     * Bắt đầu một khối lệnh
     * @param string $codeType Loại code (js|css)
     */
    public function begin ($codeType)
    {
        $codeType = strtolower($codeType);
        if (! in_array($codeType, 
        array("js", "css", "javascript", "stylesheet"))) {
            trigger_error("Tạm thời XPHP chưa hỗ trợ khối lệnh {$codeType}.", 
            E_USER_ERROR);
        }
        //Nếu một khối lệnh Js mà chưa đóng, throw ra lỗi
        if ($this->_blockBegin)
            trigger_error(
            'Phải đóng phiên end() trước khi bắt đầu begin() một khối lệnh mới.', 
            E_USER_ERROR);
        $this->_blockBegin = $codeType;
        ob_start();
        return true;
    }
    /**
     * Kết thúc một khối lệnh
     * @return XPHP_Helper_Asset
     */
    public function end ()
    {
        //Nếu chưa begin đưa ra thông báo lỗi
        if (! $this->_blockBegin)
            trigger_error('Phải sử dụng begin() để mở một khối lệnh mới.', 
            E_USER_ERROR);
             //Nếu là css
        if (in_array($this->_blockBegin, 
        array("css", "stylesheet")))
            $this->_cssBlocks[] = ob_get_contents();
             //Nếu là js
        if (in_array($this->_blockBegin, array("js", "javascript")))
            $this->_jsBlocks[] = ob_get_contents();
             //Xóa trắng bộ đệm
        ob_end_clean();
        //Gán lại thuộc tính _blockBegin = false để có thể mở block mới
        $this->_blockBegin = false;
        return $this;
    }
    /**
     * Phương thức load một tệp tin Js
     * @param string|array $jsPath Đường dẫn tới tệp tin Js có thể là đường dẫn file hoặc online
     * @return XPHP_Helper_Asset
     */
    public function loadJs ($jsPath)
    {
        if(is_array($jsPath))
            $this->_jsPaths = array_merge($this->_jsPaths, $jsPath);
        else
            $this->_jsPaths[] = $jsPath;
        return $this;
    }
    /**
     * Phương thức load một tệp tin Css
     * @param string|array $cssPath Đường dẫn tới tệp tin Css có thể là đường dẫn file hoặc online
     * @return XPHP_Helper_Asset
     */
    public function loadCss ($cssPath)
    {
         if(is_array($cssPath))
            $this->_cssPaths = array_merge($this->_cssPaths, $cssPath);
        else
            $this->_cssPaths[] = $cssPath;
        return $this;
    }
    /**
     * Lấy ra mã Js sau khi được nén
     */
    public function getCompressedJs ()
    {
        //Tên cache		
        $cacheName = "{$this->_name}.js";
        //Kiểm tra cache
        if (isset($this->_cache->$cacheName))
            return $this->_cache->$cacheName;
        else {
            $this->_processsJsGetContent();
            $this->_processJsGetBlock();
            $compJs = $this->compressor->js($this->_js);
            //Cache
            $this->_cache->$cacheName = $compJs;
            return $compJs;
        }
    }
    
    /**
     * Lấy ra mã Js
     */
    public function getJs()
    {
        //Tên cache		
        $cacheName = "{$this->_name}.js";
        //Kiểm tra cache
        if (isset($this->_cache->$cacheName))
            return $this->_cache->$cacheName;
        else {
            $this->_processsJsGetContent();
            $this->_processJsGetBlock();
            //Cache
            $this->_cache->$cacheName = $this->_js;
            return $this->_js;
        }
    }
    
    /**
     * Lấy ra mã Css sau khi được nén
     */
    public function getCompressedCss ()
    {
        //Tên cache
        $cacheName = "{$this->_name}.css";
        //Kiểm tra cache
        if (isset($this->_cache->$cacheName))
            return $this->_cache->$cacheName;
        else {
            $this->_processCssGetContent();
            $this->_processCssGetBlock();
            $compCss = $this->compressor->css($this->_css);
            //Cache
            $this->_cache->$cacheName = $compCss;
            return $compCss;
        }
    }
    
    /**
     * Lấy ra mã Css
     */
    public function getCss()
    {
        //Tên cache
        $cacheName = "{$this->_name}.css";
        //Kiểm tra cache
        if (isset($this->_cache->$cacheName))
            return $this->_cache->$cacheName;
        else {
            $this->_processCssGetContent();
            $this->_processCssGetBlock();
            //Cache
            $this->_cache->$cacheName = $this->_css;
            return $this->_css;
        }
    }
    /**
     * Cache asset
     * @param mixed $time
     * @return XPHP_Helper_Asset
     */
    public function cache ($time)
    {
        //Khởi tạo lớp hỗ trợ cache
        $this->_cache = new XPHP_Cache_Simple($time, false, "ast.", 
        'Cache/Simple');
        return $this;
    }
    /**
     * Tiến trình xử lý lấy Js từ phương thức loadJs
     */
    private function _processsJsGetContent ()
    {
        foreach ($this->_jsPaths as $jsPath) {
            if ($this->_isUrl($jsPath))
                $this->_js .= file_get_contents($jsPath);
            else {
                if (is_file($jsPath))
                    $this->_js .= file_get_contents($jsPath);
            }
        }
    }
    /**
     * Tiến trình xử lý lấy Css từ phương thức loadCss
     */
    private function _processCssGetContent ()
    {
        foreach ($this->_cssPaths as $cssPath) {
            if ($this->_isUrl($cssPath))
                $this->_css .= file_get_contents($cssPath);
            else {
                if (is_file($cssPath))
                    $this->_css .= file_get_contents($cssPath);
            }
        }
    }
    /**
     * Tiến trình xử lý lấy Js từ các Block Js
     */
    private function _processJsGetBlock ()
    {
        if (count($this->_jsBlocks) > 0)
            foreach ($this->_jsBlocks as $block) {
                $this->_js .= $block;
            }
    }
    /**
     * Tiến trình xử lý lấy Css từ các Block Css
     */
    private function _processCssGetBlock ()
    {
        if (count($this->_cssBlocks) > 0)
            foreach ($this->_cssBlocks as $block) {
                $this->_css .= $block;
            }
    }
    /**
     * Phương thức kiểm tra xem đường dẫn nhập vào có phải là đường dẫn online hay không
     * @param string $url
     */
    private function _isUrl ($url)
    {
        $http = substr($url, 0, 7);
        $https = substr($url, 0, 8);
        if (strtolower($http) == "http://")
            return true;
        if (strtolower($http) == "https://")
            return true;
        return false;
    }
    /**
     * Echo chuỗi đưa asset vào document
     */
    public function __toString ()
    {
        $html = "";
        if (sizeof($this->_cssPaths) > 0 || sizeof($this->_cssBlock) > 0) {
            //Gọi phương tích phân tích nén Css và lưu vào file
            $this->getCompressedCss();
            $href = XPHP_Url::getActionUrl(array("action" => "css", "controller" => "Assets", "args" => array($this->_name)), true);
            $html .= "<link rel='stylesheet' type='text/css' href='{$href}' />";
        }
        else
        {
            $cacheName = "{$this->_name}.css";
            if (isset($this->_cache->$cacheName))
            {
                $href = XPHP_Url::getActionUrl(array("action" => "css", "controller" => "Assets", "args" => array($this->_name)), true);
                $html .= "<link rel='stylesheet' type='text/css' href='{$href}' />";
            }
        }
        if (sizeof($this->_jsPaths) > 0 || sizeof($this->_jsBlock) > 0) {
            //Gọi phương tích phân tích nén Js và lưu vào file
            $this->getCompressedJs();
            $src = XPHP_Url::getActionUrl(array("action" => "js", "controller" => "Assets", "args" => array($this->_name)), true);
            $html .= "<script type='text/javascript' src='{$src}'></script>";
        }
        else
        {
            $cacheName = "{$this->_name}.js";
            if (isset($this->_cache->$cacheName))
            {
                $src = XPHP_Url::getActionUrl(array("action" => "js", "controller" => "Assets", "args" => array($this->_name)), true);
                $html .= "<script type='text/javascript' src='{$src}'></script>";
            }
        }
        return $html;
    }
    
    /**
     * Kiểm tra xem có asset hay không?
     * @param string $name Tên asset
     * @param string $type js|css
     * @return bool
     */
    public function hasAsset($name, $type)
    {
        if(strtolower($type) == "js" || strtolower($type) == "javascript")
        {
            $cacheName = "{$this->_name}.js";
        }
        if(strtolower($type) == "css" || strtolower($type) == "stylesheet")
        {
            $cacheName = "{$this->_name}.css";
        }
        return isset($this->_cache->$cacheName);
    }
    
    /**
     * Hiển thị mã script
     */
    public function scriptRegistrar()
    {
        echo "<script type='text/javascript'>" . self::$scriptRegistrar . "</script>";
    }
    
    /**
     * Hiển thị mã style
     */
    public function styleRegistrar()
    {
        echo "<style type='text/css'>" . self::$styleRegistrar . "</style>";
    }
    
    /**
     * Đăng ký mã script
     * @param string $script
     */
    public function registerScript($script)
    {
        self::$scriptRegistrar .= $script;
    }
    
    /**
     * Đăng ký mã style
     * @param string $style
     */
    public function registerStyle($style)
    {
        self::$styleRegistrar .= $style;
    }
    
    /**
     * Mã script
     * @var string
     */
    public static $scriptRegistrar;
    
    /**
     * Mã css
     * @var string
     */
    public static $styleRegistrar;
}