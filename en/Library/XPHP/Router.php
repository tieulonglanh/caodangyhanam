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
 * @package		XPHP_Router
 * @author		XWEB Dev Team
 * @copyright	Copyright (c) 2010-2011 XWEB. (http://xweb.vn)
 * @license		http://xphp.xweb.vn/license.html     GNU GPL License
 * @version		$Id: Router.php 20109 2011-22-08 02:05:09 Mr.UBKey $
 */
/**
 *
 **
 * XPHP_String.
 *
 * @see XPHP_String
 */
require_once 'XPHP/String.php';
/**
 * XPHP_Array.
 *
 * @see XPHP_Array
 */
require_once 'XPHP/Array.php';
/**
 * XPHP_Rewrite.
 *
 * @see XPHP_Rewrite
 */
require_once 'XPHP/Rewrite.php';
/**
 * XPHP_Runtime.
 * @see XPHP_Runtime
 */
require_once 'XPHP/Runtime.php';
/**
 * XPHP_Router.
 *
 * @category	XPHP
 * @package		XPHP_Array
 * @author		Mr.UBKey
 * @link		http://xphp.xweb.vn/user_guide/xphp_router.html
 */
class XPHP_Router
{
    /**
     * Mảng lưu trữ  request uri
     * @var array
     */
    private $_requestUri;
    /**
     * Danh sách các modules và đường dẫn đến area
     * @var array
     */
    protected $modules = array();
    /**
     * Tên area
     * @var string
     */
    public $module;
    /**
     * Tên controller
     * @var string
     */
    public $controller;
    /**
     * Tên action
     * @var string
     */
    public $action;
    /**
     * Tham số truyền vào từ QueryString
     * @var array 
     */
    public $args = array();
    /**
     * Khởi tạo các giá trị cho Router
     */
    public function __construct()
    {
        //Nhận toàn bộ modules và đường dẫn
        $this->_detectModuleDefined();
        //Nhận request uri
        $this->_detectRequestUri();
    }
    /**
     * Gán một thể hiện của Router
     * @param $router
     * @return XPHP_Router
     */
    public function setInstance($router)
    {
        if (is_array($router)) {
            if (isset($router['action']))
                $this->action = $router['action'];
            if (isset($router['controller']))
                    $this->controller = $router['controller'];
            if (isset($router['module']))
                    $this->module = $router['module'];
            if (isset($router['args']))
                    $this->args = $router['args'];
            return $this;
        }  
        else if (get_class($router) == "XPHP_Router")
        {
            $this->action = $router->action;
            $this->controller = $router->controller;
            $this->module = $router->module;
            $this->args = $router->args;
        } 
    }
    /**
     * Kiểm tra, phân tích request uri lấy ra Area, Controller, Action, Param (Thông tin router)
     * @param void
     * @return array Trả về mảng lưu trữ thông tin của router
     */
    public function analysisRequestUri()
    {
        $params = $this->_requestUri;
        //Tạo thể hiện lớp XPHP_Rewrite
        $rewrite = new XPHP_Rewrite($this);
        //Xử lý XPHP_Rewrite nếu chưa có các tham số controller, action
        if ($this->isEmpty() && ! $rewrite->processing()) {
            foreach ($params as $param) {
                //Bỏ qua dấu ? của get method hay không ?
                $pos = stripos($param, "?");
                if ($pos) {
                    $param = substr($param, 0, $pos);
                }
                //Detect module
                if (!$this->module && $this->_detectModule($param)) {
                    array_shift($params);
                    continue;
                }
                //Detect controller
                if (!$this->controller && $this->_detectController($param)) {
                    array_shift($params);
                    continue;
                }
                //Detect action
                if (!$this->action && $this->_detectAction($param)) {
                    array_shift($params);
                    break;
                }
            }
            //Bỏ qua dấu ? của get method
            for ($i = 0; $i < count($params); $i ++) {
                $pos = stripos($params[$i], "?");
                if ($pos) {
                    $params[$i] = substr($params[$i], 0, $pos);
                }
            }
            //Tham số args
            $this->args = XPHP_Array::secure($params);
            //Default controller and action
            if (! $this->controller)
                $this->controller = "Index";
            if (! $this->action)
                $this->action = "index";
        }
    }
    /**
     * Nhận diện toàn bộ modules có trong thư mục Modules và đường dẫn của nó
     * @return array $moduleName => $modulePath
     */
    private function _detectModuleDefined()
    {
        $runtime = XPHP_Runtime::getInstance();
        $runtimeKey = 'Router_Modules';
        if(!isset($runtime->$runtimeKey))
        {
            require_once 'XPHP/Directory.php';
            $subdirs = XPHP_Directory::getSubDirectories(
                       APPLICATION_PATH . '/' . "Modules");
            foreach ($subdirs as $fname => $fpath)
                $this->modules[strtolower($fname)] = array('name' => $fname,
                                               			   'path' => $fpath);
            $runtime->$runtimeKey = $this->modules;
        }
        else
            $this->modules = $runtime->$runtimeKey;
    }
    /**
     * Kiểm tra xem tham số truyền vào có phải module hay không ?
     * @param string Chuỗi cần kiểm tra
     * @return boolean
     */
    private function _detectModule($part)
    {
        $part = strtolower($part);
        if(isset($this->modules[$part]))
            $this->module = $this->modules[$part]['name'];
        return isset($this->modules[$part]);
    }
    /**
     * Kiểm tra xem tham số truyền vào có phải controller hay không ?
     * @param string Chuỗi cần kiểm tra
     * @return boolean
     */
    private function _detectController($part)
    {
        $runtime = XPHP_Runtime::getInstance();
        //Runtime key
        $runtimeKey = "Router_Controllers" . (empty($this->module) ? "" : "_{$this->module}");
        $controllerName = false;
        if(!isset($runtime->$runtimeKey))
        {
            //Lấy ra đường dẫn tới thư mục Controllers
            if ($this->module)
                $controllerFolder = "{$this->getModulePath($this->module)}/Controllers/";
            else
                $controllerFolder = APPLICATION_PATH . '/Controllers/';
            //Lấy toàn bộ danh sách các file trong thư mục Controllers
            $phpFiles = glob($controllerFolder . "*.php");
            $runtime->$runtimeKey = $phpFiles;
        }
        else 
            $phpFiles = $runtime->$runtimeKey;
        //Kiểm tra từng file trong Controllers
        foreach ($phpFiles as $f)
        {
            $f = str_replace(array("/", "\\"), "/", $f);
            $f = substr($f, strrpos($f, "/") + 1);
            if(strtolower($f) == strtolower("{$part}Controller.php"))
            {
                preg_match('/(\w+)Controller.php/i', $f, $matches);
                $controllerName = $matches[1];
            }
        }
        //Trả về false nếu không tìm thấy controller
        if(!$controllerName)
            return false;
        //Gán kết quả vè trả về true
        $this->controller = $controllerName;
        return true; 
    }
    /**
     * Kiểm tra xem tham số truyền vào có phải action hay không ?
     * @param string Chuỗi cần kiểm tra
     * @return boolean
     */
    private function _detectAction($part)
    {
    	//Lấy ra include path ban đầu
    	$oldPath = get_include_path();
    	
        $runtime = XPHP_Runtime::getInstance();
        //Lấy ra đường dẫn tới thư mục Controllers
        if ($this->module)
            $controllerFolder = "{$this->getModulePath($this->module)}/Controllers/";
        else
            $controllerFolder = APPLICATION_PATH . '/Controllers/';
        
        //Include thêm đường dẫn tới thư mục chứa controller
        set_include_path(get_include_path() . PATH_SEPARATOR . $controllerFolder);
        
        if ($this->controller)
        {
            require_once "{$this->controller}Controller.php";
        }
        else 
            return false;
        $class = "{$this->controller}Controller";
        $runtimeKey = "Router_Actions"
                      . (empty($this->module) ? "" : "_{$this->module}")
                      . "_{$class}";
        $actionName = false;
        if(!isset($runtime->$runtimeKey))
        {
            $methods = get_class_methods($class);
            $runtime->$runtimeKey = $methods;
        }
        else 
            $methods = $runtime->$runtimeKey;
        //Nhận action
        foreach ($methods as $m){
            //Kiểm tra xem có một trong những thể hiện của action hay không ?
            if (preg_match('/^'.$part.'(Action|Ajax|Post|Get)/i', $m))
                $actionName = preg_replace("/(Action|Ajax|Post|Get)/", "", $m);
        }
        //Trả về false nếu không tìm thấy action
        if(!$actionName)
            return false;
        //Gán kết quả vè trả về true
        $this->action = $actionName;
        
        //Gán lại include path ban đầu
        set_include_path($oldPath);
        
        return true; 
    }
    /**
     * Nhận diện request uri lưu lại dưới dạng mảng
     */
    private function _detectRequestUri()
    {
        require_once 'XPHP/Uri.php';
        $uri = new XPHP_Uri();
        $this->_requestUri = $uri->parseRequestUri();
    }
    
    /**
     * Lấy ra request uri
     * @return string
     */
    public function getRequestUri()
    {
        require_once 'XPHP/Uri.php';
        $uri = new XPHP_Uri();
        return $uri->getRequestUri();
    }
    
    /**
     * Lấy ra đường dẫn của một module
     * @param string $moduleName Tên module cần lấy đường dẫn
     * @return string
     */
    public function getModulePath($moduleName)
    {
        foreach ($this->modules as $module)
        {
            if($moduleName == $module['name'])
                return $module['path'];
        }
        return false;
    }
    /**
     * Lấy ra thể hiện của của Router
     * @return XPHP_Router
     */
    public function getInstant()
    {
        return clone $this;
    }  
    /**
     * Kiểm tra xem router hiện tại có rỗng không
     */
    public function isEmpty()
    {
        return empty($this->action) 
               && empty($this->controller) 
               && empty($this->module);
    } 
    /**
     * Xóa toàn bộ các thông tin action, controller, module detect được
     * @return bool
     */
    public function emptyRouter()
    {
        $this->action = null;
        $this->controller = null;
        $this->module = null;
    }
    
    public function getRouterString()
    {
        if($this->module)
            $rt = array($this->module, $this->controller, $this->action);
        else
            $rt = array($this->controller, $this->action);
        return implode("_", $rt);
    }
}