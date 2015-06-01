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
 * @category    XPHP
 * @package        XPHP_Controller
 * @subpackage    Front
 * @author        XWEB Dev Team
 * @copyright    Copyright (c) 2010-2011 XWEB. (http://xweb.vn)
 * @license        http://xphp.xweb.vn/license.html     GNU GPL License
 * @version        $Id: Abstract.php 20109 2010-21-12 02:05:09 Mr.UBKey $
 */
/**
 * @see XPHP_Registry
 */
require_once 'XPHP/Registry.php';
/**
 * @see XPHP_Runtime
 */
require_once 'XPHP/Runtime.php';
/**
 * Lớp Abstract của XPHP_Controller_Front lớp xử lý điều hướng, phân loại gọi tới Controller, Action
 *
 * @category    XPHP
 * @package        XPHP_Controller
 * @subpackage    Front
 * @author        Mr.UBKey
 * @link        http://xphp.xweb.vn/user_guide/xphp_controller_front_abstract.html
 */
abstract class XPHP_Controller_Front_Abstract
{
    /**
     * Router của hệ thống
     * @var XPHP_Router
     */
    public $router;

    /**
     * Khởi tạo front controller
     * @param XPHP_Router $router || null
     */
    public function __construct(XPHP_Router $router = null)
    {
        if($router === null)
        {
            //Router
            $this->router = new XPHP_Router();
            $this->router->analysisRequestUri();
        }
        else 
            $this->router = $router;
        //Include Path
        $this->setIncludePath();
        //Khởi tạo các tham số của ứng dụng
        $application = new stdClass();
        //Mặc định khi khởi tạo chế độ detect false
        $application->detect = false;
        $application->mode = "standard";
        XPHP_Registry::set("Application", $application);
    }
    /**
     * Gán include path
     */
    public function setIncludePath()
    {
        //Lấy ra đường dẫn module sử dụng
        if(!empty($this->router->module))
            $folder = $this->router->getModulePath($this->router->module);
        else 
            $folder = APPLICATION_PATH;
            
        //Set đường dẫn
        set_include_path(get_include_path() 
                         . PATH_SEPARATOR . $folder
                         . PATH_SEPARATOR . $folder . '/Models'
                         . PATH_SEPARATOR . $folder . '/Controllers');
    }

    /**
     * Phương thức nhận biết các thiết bị di động
     */
    public function detect()
    {
        $application = XPHP_Registry::get("Application");
        //Đặt ứng dụng trong chế độ tự động detect
        $application->detect = true;
        require_once 'XPHP/Mobile/Detector.php';
        $detector = new XPHP_Mobile_Detector();
        $application->detector = $detector;
        if ($detector->isMobile)
            $application->mode = "mobile";
        else
            $application->mode = "standard";
        //Lưu vào Registry
        XPHP_Registry::set("Application", $application);
    }

    /**
     * Chạy Front Controller
     */
    abstract function dispatch();
} 