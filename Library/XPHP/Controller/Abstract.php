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
 * @version		$Id: Interface.php 20109 2011-20-09 02:05:09 Mr.UBKey $
 */
/**
 * XPHP_Controller_Interface
 *
 * @see XPHP_Controller_Interface
 */
require_once 'XPHP/Controller/Interface.php';
/**
 * XPHP_Controller_Abstract
 *
 * @category	XPHP
 * @package		XPHP_Controller
 * @author		Mr.UBKey
 * @link		http://xphp.xweb.vn/user_guide/xphp_controller_abstract.html
 */
abstract class XPHP_Controller_Abstract implements XPHP_Controller_Interface
{
    /**
     * Router lưu thông tin về area, controller, action
     * @var XPHP_Router
     */
    protected $router;
    /**
     * Model lưu thông tin về mô hình
     * @var XPHP_Model
     * @abstract XPHP_Model
     */
    protected $model;
    /**
     * Khởi tạo lớp controller
     * @param XPHProuter $router
     */
    /**
     * Mảng lưu trữ các tham số truyền vào từ _GET, _POST, REQUEST URI
     * @var array
     */
    protected $params = array();
    /**
     * Hỗ trợ mở rộng cho XPHP_Controller
     * @var XPHP_Controller_Helper
     */
    protected $helper;
    public function __construct (&$router)
    {
        //Load cấu hình trong file config với node controller
        require_once 'XPHP/Config.php';
        XPHP_Config::load($this, 'controller');
        //Gán router cho controller
        $this->router = $router;
        //Lấy toàn bộ các tham số truyền vào dạng POST VÀ GET đưa vào $this->params
        if (isset($_POST)) {
            foreach ($_POST as $key => $value) {
                $this->params[$key] = XPHP_String::secure($value);
            }
        }
        if (isset($_GET)) {
            foreach ($_GET as $key => $value) {
                $this->params[$key] = XPHP_String::secure($value);
            }
        }
        //Lấy toàn bộ các tham số truyền vào dạng GET đưa vào $this->params
        $args = $this->router->args;
        if (! empty($args)) {
            foreach($args as $a => $v)
                $this->params[$a] = $v;
        }
        //Khởi tạo lớp hỗ trợ cho controller truyền vào router
        require_once 'XPHP/Controller/Helper.php';
        $this->helper = new XPHP_Controller_Helper($this->router);
    }
    /**
     * Trả về router của hệ thống
     */
    public function getRouter ()
    {
        return $this->router;
    }
    /**
     * Nếu truyền vào tên tham số phương thức sẽ kiểm tra xem có tồn tại tham số đó không ?
     * Nếu không truyền vào tham số phương thức sẽ kiểm tra xem có tham số truyền vào hay không ?
     * @param string $param || void
     * @return boolean
     */
    public function hasParams ($param = NULL)
    {
        if ($param !== NULL) {
            if (isset($this->params[$param]))
                return true;
            else
                return false;
        } else {
            if (sizeof($this->params) > 0)
                return true;
            else
                return false;
        }
    }
    /**
     * Nếu truyền vào tham số sẽ lấy ra giá trị của param
     * Nếu không truyền vào tham số sẽ lấy ra mảng tất cả các param
     * @param string $param || void
     * @return array
     */
    public function getParams ($param = NULL)
    {
        if ($param !== NULL) {
            if (isset($this->params[$param]))
                return $this->params[$param];
            else
                return false;
        }
        return $this->params;
    }
    /**
     * Phương thức sẽ gán đối tượng thuộc class MF_Model cho model
     * @param $object XPHP_Model
     * @throws XPHP_Exception
     */
    public function setModel ($obj)
    {
        if (is_object($obj)) {
            if (empty($this->model))
                $this->model = $obj;
        } else
            throw new XPHP_Exception("Param model is not object");
    }
    /**
     * Lấy ra model
     */
    public function getModel ()
    {
        return $this->model;
    }
    
    /**
     * Load extends của module
     * @param $module string Tên module cần load extends nếu không truyền vào mặc định lấy modules hiện tại
     */
    public function loadModuleExtends($module=NULL)
    {
        if($module === NULL)
            $module = $this->router->module;
        if(empty($module))
        {
            set_include_path(get_include_path() . PATH_SEPARATOR . APPLICATION_PATH . '/' . "Extends");
        }
        else
            set_include_path(get_include_path() . PATH_SEPARATOR . $this->router->getModulePath($module) . '/' . "Extends");
    }
    
    /**
     * Kiểm tra xem model có dạng giống tham số truyền vào không
     * VD: Nếu người dùng POST dữ liệu phương thức sẽ kiểm tra xem có dạng 
     * giống với model truyền vào không
     * Nếu kiểm tra thành công gọi phương thức parseModelParams phân tich và gán
     * các giá trị tương ứng vào $this->model
     * Trả về true, false
     * @param $object XPHP_Model Truyền vào đối tượng thuộc lớp XPHP_Model để kiểm tra
     * @throws XPHP_Exception
     * @return bool
     */
    protected function hasModel ($obj = NULL)
    {
        if ($obj === NULL) {
            if (empty($this->model))
                throw new XPHP_Exception(
                "Model chưa được định kiểu bạn cần phải truyền vào tham số là đối tượng cho hasModel()");
            else
                $obj = $this->model;
        }
        if (is_object($obj)) {
            if (get_parent_class($obj) != "XPHP_Model") {
                throw new XPHP_Exception(
                "Kiểu của model phải được kế thừa từ lớp XPHP_Model");
                return;
            }
            $ref = new XPHP_Reflection($obj);
            $refproperties = $ref->getProperties();
            //Mảng lưu trữ các thuộc tính
            $arrProperties = array();
            foreach ($refproperties as $refproperty) {
                $arrProperties[] = $refproperty->getName();
            }
            //Nếu là ajax request
            if ($this->isAjaxRequest() && isset($_POST["jsondata"])) {
                $arr = json_decode($_POST["jsondata"], true);
                $arrData = array();
                foreach ($arr as $key => $value)
                    $arrData[$key] = $value;
                     //Kiểm tra các thuộc tính với arrData
                foreach ($arrProperties as $property) {
                    if (isset($arrData[$property])) {
                        //Gán đối tượng làm model
                        $this->model = $obj;
                        //Gọi tới phương thức phân tích để gán giá trị vào model
                        $this->parseModelParams();
                        //Have property
                        return true;
                    } else
                        continue;
                }
            } //Nếu người dùng POST dữ liệu
else {
                //Kiểm tra thuộc tính với các tham số người dùng POST lên
                foreach ($arrProperties as $property) {
                    if (isset($this->params[$property])) {
                        //Gán đối tượng làm model
                        $this->model = $obj;
                        //Gọi tới phương thức phân tích để gán giá trị vào model
                        $this->parseModelParams();
                        return true;
                    } else
                        continue;
                }
                return false;
            }
        } else
            throw new XPHP_Exception(
            "Phương thức hasModel() tham số truyền vào là một đối tượng kế thừa từ lớp XPHP_Model");
    }
    /**
     * Phân tích tham số truyền vào từ POST, GET, AJAX để gán các giá trị tương ứng vào model
     * @access private
     * @param XPHP_Model $obj Truyền vào đối tượng thuộc lớp XPHP_Model để kiểm tra
     * @return boolean
     */
    public function parseModelParams ()
    {
        //Đánh dấu model đã được gán thuộc tính
        $hasProperty = false;
        if (is_object($this->model)) {
            //Lấy ra tên các property của model
            $ref = new XPHP_Reflection($this->model);
            $refproperties = $ref->getProperties();
            //Mảng lưu trữ các thuộc tính
            $propertyNames = array();
            foreach ($refproperties as $refproperty) {
                $propertyNames[] = $refproperty->getName();
            }
            //Nếu người dùng sử dụng POST
            //Kiểm tra thuộc tính với các tham số POST từ client
            //Gán các giá trị tương ứng vào model
            foreach ($propertyNames as $property) {
                if (isset($this->params[$property])) {
                    $this->model->$property = XPHP_String::secure(
                    $this->params[$property]);
                    $hasProperty = true;
                } else
                    continue;
            }
        }
        return $hasProperty;
    }
    /**
     * Kiểm tra xem yêu cầu có phải là ajax hay không ?
     * @param void
     * @return bool
     */
    public function isAjaxRequest ()
    {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
         ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'));
    }
} 