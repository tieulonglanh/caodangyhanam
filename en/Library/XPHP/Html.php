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
 * @package		XPHP_Html
 * @author		XWEB Dev Team
 * @copyright	Copyright (c) 2010-2011 XWEB. (http://xweb.vn)
 * @license		http://xphp.xweb.vn/license.html     GNU GPL License
 * @version		$Id: Html.php 20112 2011-22-08 02:05:09 Mr.UBKey $
 */
/**
 * XPHP_Html_Base.
 *
 * @see XPHP_Html_Base
 */
require_once 'XPHP/Html/Base.php';
/**
 * XPHP_String.
 *
 * @see XPHP_String
 */
require_once 'XPHP/String.php';
/**
 * XPHP_Config.
 *
 * @see XPHP_Config
 */
require_once 'XPHP/Config.php';
/**
 * XPHP_Exception.
 *
 * @see XPHP_Exception
 */
require_once 'XPHP/Exception.php';
/**
 * Lớp hỗ trợ View hiển thị HTML và một số tính năng render 
 *
 * @category	XPHP
 * @package		XPHP_Html
 * @author		Mr.UBKey
 * @link		http://xphp.xweb.vn/user_guide/xphp_html.html
 */
class XPHP_Html
{
    /**
     * XPHP_Action_Result_View hệ thống
     * @var XPHP_Action_Result_View
     */
    private $_actionViewResult;
    
    /**
     * XPHP_Model
     * @var XPHP_Model
     */
    private $_model;
    
    /**
     * Router của hệ thống
     * @var XPHP_Router
     */
    private $_router;
    
    /**
     * Model của hệ thống
     * @param XPHP_Router $router
     * @param XPHP_Action_Result_View $actionViewResult
     */
    public function __construct ($router = NULL, $actionViewResult = NULL)
    {
        //Load cấu hình trong file config với node view
        XPHP_Config::load($this, 'html');
        if ($router !== NULL)
            $this->_router = $router;
        if ($actionViewResult !== NULL) {
            $this->_actionViewResult = $actionViewResult;
            $this->_model = &$actionViewResult->model;
        }
    }
    /**
     * Load tệp tin Css
     * @param mixed $path
     * @return XPHP_Html
     * @throws XPHP_Exception
     */
    public function loadCss ($path)
    {
        if (is_string($path)) {
            if (is_file($path))
                echo '<link href="' . XPHP_Url::getRelativeUrl() . '/' . $path .
                 '" rel="stylesheet" type="text/css" />';
            else
                throw new XPHP_Exception(
                "Không tìm thấy tệp tin css tại " . XPHP_Url::getRelativeUrl() .
                 '/' . $path);
        } else 
            if (is_array($path)) {
                foreach ($path as $p) {
                    if (is_file($p))
                        echo '<link href="' . XPHP_Url::getRelativeUrl() . '/' .
                         $p . '" rel="stylesheet" type="text/css" />';
                    else
                        throw new XPHP_Exception(
                        "Không tìm thấy tệp tin css tại " .
                         XPHP_Url::getRelativeUrl() . '/' . $p);
                }
            }
        return $this;
    }
    /**
     * Load tệp tin Javascript
     * @param mixed $path
     * @return XPHP_Html
     * @throws XPHP_Exception
     */
    public function loadJs ($path)
    {
        if (is_string($path)) {
            if (is_file($path))
                echo '<script language="javascript" src="' .
                 XPHP_Url::getRelativeUrl() . '/' . $path .
                 '" type="text/javascript"></script>';
            else
                throw new XPHP_Exception(
                "Không tìm thấy tệp tin javascript tại " .
                 XPHP_Url::getRelativeUrl() . '/' . $path);
        } else 
            if (is_array($path)) {
                foreach ($path as $p) {
                    if (is_file($p))
                        echo '<script language="javascript" src="' .
                         XPHP_Url::getRelativeUrl() . '/' . $p .
                         '" type="text/javascript"></script>';
                    else
                        throw new XPHP_Exception(
                        "Không tìm thấy tệp tin javascript tại " .
                         XPHP_Url::getRelativeUrl() . '/' . $p);
                }
            }
        return $this;
    }
    /**
     * Overload method action.
     */
    function __call ($method_name, $arguments)
    {
        //we inspect number of arguments
        if ($method_name == "renderAction" && count($arguments) == 1) {
            return $this->renderAction1($arguments[0]);
        } else 
            if ($method_name == "renderAction" && count($arguments) == 2 &&
             is_string($arguments[1])) {
                return $this->renderAction2($arguments[0], $arguments[1]);
            } else 
                if ($method_name == "renderAction" && count($arguments) == 2 &&
                 is_array($arguments[1])) {
                    return $this->renderAction5($arguments[0], $arguments[1]);
                } else 
                    if ($method_name == "renderAction" && count($arguments) == 3 &&
                     is_string($arguments[2])) {
                        return $this->renderAction3($arguments[0], 
                        $arguments[1], $arguments[2]);
                    } else 
                        if ($method_name == "renderAction" &&
                         count($arguments) == 3 && is_array($arguments[2])) {
                            return $this->renderAction6($arguments[0], 
                            $arguments[1], $arguments[2]);
                        } else 
                            if ($method_name == "renderAction" &&
                             count($arguments) == 4) {
                                return $this->renderAction4($arguments[0], 
                                $arguments[1], $arguments[2], $arguments[3]);
                            }
    }
    function renderAction1 ($action)
    {
        $router = $this->_router->getInstant();
        $router->action = $action;
        //Xóa toàn bộ include path
        set_include_path("Library");
        //Execute request
        $frontController = new XPHP_Controller_Front($router);
        $frontController->dispatch();
    }
    function renderAction2 ($action, $controller)
    {
        $router = $this->_router->getInstant();
        $router->action = $action;
        $router->controller = $controller;
        //Delete all include path
        set_include_path("Library");
        //Execute request
        $frontController = new XPHP_Controller_Front($router);
        $frontController->dispatch();
    }
    function renderAction3 ($action, $controller, $module)
    {
        $router = $this->_router->getInstant();
        $router->action = $action;
        $router->controller = $controller;
        $router->module = $module;
        //Delete all include path
        set_include_path("Library");
        //Execute request
        $frontController = new XPHP_Controller_Front($router);
        $frontController->dispatch();
    }
    function renderAction4 ($action, $controller, $module, $args)
    {
        $router = $this->_router->getInstant();
        $router->action = $action;
        $router->controller = $controller;
        $router->module = $module;
        $router->args = $args;
        //Delete all include path
        set_include_path("Library");
        //Execute request
        $frontController = new XPHP_Controller_Front($router);
        $frontController->dispatch();
    }
    function renderAction5 ($action, $args)
    {
        $router = $this->_router->getInstant();
        $router->action = $action;
        $router->args = $args;
        //Delete all include path
        set_include_path("Library");
        //Execute request
        $frontController = new XPHP_Controller_Front($router);
        $frontController->dispatch();
    }
    function renderAction6 ($action, $controller, $args)
    {
        $router = $this->_router->getInstant();
        $router->action = $action;
        $router->controller = $controller;
        $router->args = $args;
        //Delete all include path
        set_include_path("Library");
        //Execute request
        $frontController = new XPHP_Controller_Front($router);
        $frontController->dispatch();
    }
    public function renderContent($url)
    {
        echo file_get_contents($url);
    }
    /**
     * Render một view thành phần
     * @param string $partial Tên file view thành phần
     * @param stdObject|null $data Dữ liệu truyền sang view thành phần
     * @param XPHP_Model|null $model Model truyền sang view thành phần
     */
    public function renderPartial($file, $data = null,  $model = null)
    {
        require_once 'XPHP/View/Partial.php';
        $partial = new XPHP_View_Partial();
        //Chuyen router
        $partial->router = $this->_router;
        //Chuyển data
        $partial->data = $data;
        //Chuyển model
        $partial->model = $model;
        //Chuyển tên file
        $view = new XPHP_View($this->_router);
        $folder = $view->getViewFolder();
        $partial->file = "{$folder}/{$file}.phtml";
        //Render
        $partial->render();
    }
    public function stringEncode ($html)
    {
        return XPHP_String::HtmlStringEncode($html);
    }
    public function stringDecode ($html)
    {
        return XPHP_String::HtmlStringDecode($html);
    }
    public function encode ($html)
    {
        return XPHP_String::HtmlEncode($html);
    }
    public function decode ($html)
    {
        return XPHP_String::HtmlDecode($html);
    }
    public function raw ($html_stringencoded)
    {
        echo $this->stringDecode($html_stringencoded);
    }
}