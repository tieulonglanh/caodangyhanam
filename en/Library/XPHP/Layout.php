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
 * @package		XPHP_Layout
 * @author		XWEB Dev Team
 * @copyright	Copyright (c) 2010-2011 XWEB. (http://xweb.vn)
 * @license		http://xphp.xweb.vn/license.html     GNU GPL License
 * @version		$Id: Layout.php 201011 2011-22-08 02:05:09 Mr.UBKey $
 */
/**
 * XPHP_Exception
 *
 * @see XPHP_Exception
 */
require_once 'XPHP/Exception.php';
/**
 * XPHP_Layout xử lý các vấn đề liên quan tới Layout
 *
 * @category	XPHP
 * @package		XPHP_Layout
 * @author		Mr.UBKey
 * @link		http://xphp.xweb.vn/user_guide/xphp_layout.html
 */
class XPHP_Layout
{
    /**
     * Router lưu thông tin về area, controller, action
     * @var XPHP_Router
     */
    protected $router;
    /**
     * File layout
     * @var string
     */
    private $_layoutFile;
    /**
     * Folder layout
     * @var string
     */
    private $_layoutFolder;
    /**
     * Phương thức khởi tạo layout
     * @param XPHP_Router $router
     * @param XPHP_View $view
     */
    public function __construct (&$router)
    {
        //Gán Router
        $this->router = $router;
    }
    /**
     * Thiết lập layout file
     * @param string $layoutFile
     */
    public function setLayoutFile ($layoutFile)
    {
        $this->_layoutFile = $layoutFile;
        $this->_detectLayoutFolder();
    }
    /**
     * Lấy ra layout file
     */
    public function getLayoutFile ()
    {
        return $this->_layoutFile;
    }
    /**
     * Lấy ra đường dẫn đến file layout
     */
    public function getLayoutPath ()
    {
        //Đường dẫn tới thư mục
        $mobilePath = $this->getMobileLayoutPath();
        if ($mobilePath)
            $path = $mobilePath;
        else
            $path = "{$this->_layoutFolder}/{$this->_layoutFile}.phtml";
        //Xử lý trả về kết quả
        if (is_file($path))
            return $path;
        else {
            throw new XPHP_Exception(
                "Không tìm thấy tệp tin Layout $path được load trong 
                 action = {$this->router->action} 
                 controller = {$this->router->controller} " 
                . ($this->router->module ? "module = {$this->router->module}" : "")
            );
        }
    }
    /**
     * Lấy ra đường dẫn tới thư mục chứa layout
     */
    public function getLayoutFolder()
    {
        return $this->_layoutFolder;
    }
    /**
     * Nhận diện đường dẫn tới thư mục chứa layout
     */
    private function _detectLayoutFolder ()
    {
        if (!$this->router->module) {
            $this->_layoutFolder = APPLICATION_PATH . "/Layouts";
        } else {
            if (strpos($this->_layoutFile, "/") !== false) {
                $layouts = explode("/", $this->_layoutFile);
                if ($layouts[0] != "") {
                    $this->_layoutFolder = $this->router->getModulePath($layouts[0]) . "/Layouts";
                } else {
                    $this->_layoutFolder = APPLICATION_PATH . "/Layouts";
                }
                $this->_layoutFile = $layouts[1];
            } else {
                $this->_layoutFolder = $this->router->getModulePath($this->router->module)
                                       . '/Layouts';
            }
        }
    }
    /**
     * Lấy ra đường dẫn đến file Layout của Mobile
     * @return mixed string | boolean Trả về chuỗi hoặc false nếu không tìm thấy file mobile layout
     */
    public function getMobileLayoutPath ()
    {
        //Lấy ra Application từ Registry và kiểm tra có detect hay không?
        $application = XPHP_Registry::get("Application");
        if ($application->mode == "mobile") {
            //Mobile path
            $mobilePath = "{$this->_layoutFolder}/{$this->_layoutFile}.mobile.phtml";
            //Device path
            $devicePath = "{$this->_layoutFolder}/{$this->_layoutFile}.{$application->detector->device}.phtml";
            if (is_file($devicePath))
                return $devicePath;
            if (is_file($mobilePath))
                return $mobilePath;
        }
        return false;
    }
}
