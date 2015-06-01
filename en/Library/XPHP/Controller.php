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
 * @package		XPHP_Controller
 * @author		XWEB Dev Team
 * @copyright	Copyright (c) 2010-2011 XWEB. (http://xweb.vn)
 * @license		http://xphp.xweb.vn/license.html     GNU GPL License
 * @version		$Id: Controller.php 20109 2011-22-08 02:05:09 Mr.UBKey $
 */
/**
 * XPHP_Controller_Abstract
 * 
 * @see XPHP_Controller_Abstract
 */
require_once 'XPHP/Controller/Abstract.php';
/**
 * XPHP_String.
 *
 * @see XPHP_String
 */
require_once 'XPHP/String.php';
/**
 * XPHP_Cache.
 *
 * @see XPHP_Cache
 */
require_once 'XPHP/Cache.php';
/**
 * XPHP_Session
 * 
 * @see XPHP_Session
 */
require_once 'XPHP/Session.php';
/**
 * XPHP_Cookie
 * 
 * @see XPHP_Cookie 
 */
require_once 'XPHP/Cookie.php';
/**
 * XPHP_Controller Lớp abstract của Controller 
 *
 * @category	XPHP
 * @package		XPHP_Controller
 * @author		Mr.UBKey
 * @link		http://xphp.xweb.vn/user_guide/xphp_controller.html
 */
abstract class XPHP_Controller extends XPHP_Controller_Abstract
{
    /**
     * View đối tượng lưu trữ dữ liệu chuyển từ controller sang view
     * @var stdClass
     */
    public $view;
    /**
     * Tên Layout sử dụng trong trang
     * @var string Tên Layout
     */
    protected $layout;
    /**
     * Hỗ trợ Url
     */
    protected $url;
    /**
     * Hỗ trợ cache
     * @var XPHP_Cache
     */
    protected $cache;
    /**
     * Xử lý resource
     * @var XPHP_Resource
     */
    protected $resource;
    /**
     * Hỗ trợ session
     * @var XPHP_Session
     */
    protected $session;
    /**
     * Hỗ trợ cookie
     */
    protected $cookie;
    public function __construct ($router)
    {
        parent::__construct($router);
        //Khởi tạo view lưu trữ dữ liệu truyền từ controller tới view
        $this->view = new stdClass();
        //Khởi tạo lớp hỗ trợ cache
        $this->cache = new XPHP_Cache();
        //Khởi tạo lớp hỗ trợ Session
        $this->session = XPHP_Session::getInstance();
        //Khởi tạo lớp hỗ trợ Cookie
        $this->cookie = new XPHP_Cookie();
        //Xử lý resource
        if (XPHP_Registry::isRegistered("DefaultResource"))
            $this->resource = XPHP_Registry::get("DefaultResource");
             //Khởi tạo cho Hỗ trợ Url
        $this->url = new XPHP_Url($router);
        
        //Gọi tới function __init nếu có sau khi khởi tạo lớp
        //Hàm __init() sẽ chạy trước khi chạy mọi action trong controller
        if (method_exists(get_class($this), '_init'))
        	$this->_init();
    }
    /**
     * Load layout từ Controller
     * @param unknown_type $layout
     */
    protected function loadLayout ($layout)
    {
        $this->layout = $layout;
    }
    /**
     * Bỏ layout đã được load giống loadLayout(null)
     */
    protected function unloadLayout ()
    {
        $this->layout = NULL;
    }
    /**
     * Lấy ra layout đã được load
     */
    public function getLayout ()
    {
        return $this->layout;
    }
    /**
     * Trả về XPHP_View_Result
     * @param mixed $data  null | XPHP_Model | Lớp thừa kế từ lớp XPHP_Model
     * @return XPHP_View_Result
     */
    protected function view ($model = NULL)
    {
        require_once 'XPHP/Action/Result/View.php';
        if ($model !== NULL && get_parent_class($model) == "XPHP_Model") {
            $this->setModel($model);
        }
        return new XPHP_Action_Result_View();
    }
    /**
     * Overload method redirect.
     */
    function __call ($method_name, $arguments)
    {
        require_once 'XPHP/Action/Result/Redirect.php';
        //we inspect number of arguments
        if ($method_name == "redirect" && count($arguments) == 1) {
            return $this->redirect1($arguments[0]);
        } else 
            if ($method_name == "redirect" && count($arguments) == 2 &&
             is_string($arguments[1])) {
                return $this->redirect2($arguments[0], $arguments[1]);
            } else 
                if ($method_name == "redirect" && count($arguments) == 2 &&
                 is_array($arguments[1])) {
                    return $this->redirect5($arguments[0], $arguments[1]);
                } else 
                    if ($method_name == "redirect" && count($arguments) == 3 &&
                     is_string($arguments[2])) {
                        return $this->redirect3($arguments[0], $arguments[1], 
                        $arguments[2]);
                    } else 
                        if ($method_name == "redirect" && count($arguments) == 3 &&
                         is_array($arguments[2])) {
                            return $this->redirect6($arguments[0], 
                            $arguments[1], $arguments[2]);
                        } else 
                            if ($method_name == "redirect" &&
                             count($arguments) == 4) {
                                return $this->redirect4($arguments[0], 
                                $arguments[1], $arguments[2], $arguments[3]);
                            }
    }
    /**
     * Trả về kết quả là đối tượng của lớp XPHP_Action_Result_Redirect
     * @param mix $action - Tên action, $controller - Tên controller, $area - Tên area, $args - Tham số
     */
    public function redirect1 ($action)
    {
        return new XPHP_Action_Result_Redirect($this->url->action($action));
    }
    private function redirect2 ($action, $controller)
    {
        return new XPHP_Action_Result_Redirect(
        $this->url->action($action, $controller));
    }
    private function redirect3 ($action, $controller, $area)
    {
        return new XPHP_Action_Result_Redirect(
        $this->url->action($action, $controller, $area));
    }
    private function redirect4 ($action, $controller, $area, $args)
    {
        return new XPHP_Action_Result_Redirect(
        $this->url->action($action, $controller, $area, $args));
    }
    private function redirect5 ($action, $args)
    {
        return new XPHP_Action_Result_Redirect($this->url->action($action, $args));
    }
    private function redirect6 ($action, $controller, $args)
    {
        return new XPHP_Action_Result_Redirect(
        $this->url->action($action, $controller, $args));
    }
    /**
     * Trả về kết quả là đối tượng của lớp XPHP_Action_Result_Redirect
     * @param string $url Đường dẫn cần redirect tới
     * @return XPHP_Action_Result_Redirect
     */
    protected function redirectUrl ($url)
    {
        require_once 'XPHP/Action/Result/Redirect.php';
        return new XPHP_Action_Result_Redirect($url);
    }
    /**
     * Trả về kết quả là đối tượng của lớp XPHP_Action_Result_Json
     * @param mix $data Dữ liệu cần trả về kiểu json
     * @return XPHP_Action_Result_Json
     */
    protected function json ($data)
    {
        require_once 'XPHP/Action/Result/Json.php';
        return new XPHP_Action_Result_Json($data);
    }
}