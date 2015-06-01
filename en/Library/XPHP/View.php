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
 * @package		XPHP_View
 * @author		XWEB Dev Team
 * @copyright	Copyright (c) 2010-2011 XWEB. (http://xweb.vn)
 * @license		http://xphp.xweb.vn/license.html     GNU GPL License
 * @version		$Id: View.php 20109 2011-22-08 02:05:09 Mr.UBKey $
 */
/**
 * XPHP_Config.
 * @see XPHP_Config
 */
require_once 'XPHP/Config.php';
/**
 * XPHP_View
 *
 * @category	XPHP
 * @package		XPHP_View
 * @author		Mr.UBKey
 * @link		http://xphp.xweb.vn/user_guide/xphp_view.html
 */
class XPHP_View
{
    /**
     * Router lưu thông tin về area, controller, action
     * @var XPHP_Router
     */
    public $router;
    /**
     * Lớp khởi tạo View
     * @param XPHP_Router $router
     */
    public function __construct(&$router)
    {
        $this->router = $router; // Gán Router
    }
    public function getDefaultViewPath()
    {
        //Lấy ra đường dẫn đến thư mục views
        $folder = $this->getViewFolder();
        //Đường dẫn tới view của mobile
        $mobilePath = $this->getMobileViewPath();
        if ($mobilePath)
            $path = $mobilePath;
        else
            $path = "{$folder}/{$this->router->action}.phtml";
        //Xử lý trả về kết quả
        if (is_file($path))
            return $path;
        else {
            require_once 'XPHP/Exception.php';
            throw new XPHP_Exception(
                "Không tìm thấy tệp tin View $path của
                 action = {$this->router->action} 
                 controller = {$this->router->controller} "
                . (empty($this->router->module) ? "" : "module = {$this->router->module}")
            );
        }
    }
    public function getViewFolder()
    {
        if ($this->router->module) {
            return $this->router->getModulePath($this->router->module)
             	   . '/Views/' . $this->router->controller;
        } else {
            return APPLICATION_PATH
            	   . '/Views/' . $this->router->controller;
        }
    }
    /**
     * Lấy ra đường dẫn đến file View của Mobile
     * @return mixed string | boolean Trả về chuỗi hoặc false nếu không tìm thấy file mobile view
     */
    public function getMobileViewPath()
    {
        //Lấy ra folder
        $folder = $this->getViewFolder();
        //Lấy ra Application từ Registry và kiểm tra có detect hay không?
        $application = XPHP_Registry::get("Application");
        if ($application->mode == "mobile") {
            //Mobile path
            $mobilePath = "$folder/{$this->router->action}.mobile.phtml";
            //Device path
            $devicePath = "$folder/{$this->router->action}.{$application->detector->device}.phtml";
            if (is_file($devicePath))
                return $devicePath;
            if (is_file($mobilePath))
                return $mobilePath;
        }
        return false;
    }
}