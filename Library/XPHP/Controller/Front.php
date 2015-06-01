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
 * @subpackage	Front
 * @author		XWEB Dev Team
 * @copyright	Copyright (c) 2010-2011 XWEB. (http://xweb.vn)
 * @license		http://xphp.xweb.vn/license.html     GNU GPL License
 * @version		$Id: Front.php 20109 2010-21-12 02:05:09 Mr.UBKey $
 */
/**
 * XPHP_Controller_Front_Abstract
 *
 * @see XPHP_Controller_Front_Abstract
 */
require_once 'XPHP/Controller/Front/Abstract.php';
/**
 * XPHP_Attribute
 *
 * @see XPHP_Attribute
 */
require_once 'XPHP/Attribute.php';
/**
 * XPHP_Controller_Front lớp xử lý điều hướng, phân loại gọi tới Controller, Action
 *
 * @category	XPHP
 * @package		XPHP_Controller
 * @subpackage	Front
 * @author		Mr.UBKey
 * @link		http://xphp.xweb.vn/user_guide/xphp_controller_front.html
 */
class XPHP_Controller_Front extends XPHP_Controller_Front_Abstract
{
    /**
     * Chạy Front Controller
     */
    public function dispatch ()
    {
        //Xử lý Controller
        $class = "{$this->router->controller}Controller";
        //Gọi tới action lấy giá trị trả về XPHP_View_Result_Interface
        $obj = new $class($this->router);
        /*
		 * Gọi đến action đặc biệt
		 */
        //Nếu request là ajax request thì gọi tới actionAjax và trả về kết quả
        if ($obj->isAjaxRequest() && method_exists($class, "{$this->router->action}Ajax")) 
        {
            //Xử lý gọi actionAjax
            $result = $this->_callAction($obj, "{$this->router->action}Ajax");
        } else {
            //Nếu người dùng sử dụng form POST dữ liệu thì gọi tới actionPost trước khi gọi action
            if (sizeof($_POST) > 0 && method_exists($class, "{$this->router->action}Post")) {
                //Xử lý gọi actionPost
                $result = $this->_callAction($obj, "{$this->router->action}Post");
            }
            //Nếu người dùng sử dụng form GET để gửi dữ liệu thì gọi tới actionGet trước khi gọi action
            if (sizeof($_GET) > 1 && method_exists($class, "{$this->router->action}Get")) {
                //Xử lý gọi actionPost
                $result = $this->_callAction($obj, "{$this->router->action}Get");
            }
            //Nếu POST và GET không trả về View_Result => Gọi action
            if (! isset($result)) {
                //Xử lý gọi action
                $result = $this->_callAction($obj, "{$this->router->action}Action");
            }
        }
        /*
		 * XỬ LÝ KẾT QUẢ TRẢ VỀ
		 */
        //Nếu kết quả trả về khác null
        if ($result !== NULL) {
            if($result instanceof XPHP_Action_Result_Interface)
                if($result instanceof XPHP_Action_Result_View_Abstract)
                {
                    $result->setModel($obj->getModel());
                    $result->setRouter($this->router);
                    $result->setViewData($obj->view);
                    $result->setLayoutFile($obj->getLayout());
                }
            //Thực thi kết quả
            $result->execute();
        }
    }
    /**
     * Phương thức gọi action từ controller và các event của action
     * @param XPHP_Controller $controller
     * @param string $action
     */
    private function _callAction (&$controller, $action)
    {
        //Lấy ra toàn bộ danh sách các Attribute của Action
        $attributes = XPHP_Attribute::ofMethod($controller, $action, 
        'XPHP_Action_Attribute_Abstract');
        //Gán Controller cho toàn bộ các Action Attribute
        foreach ($attributes as $attribute) {
            /* @var  $attribute XPHP_Action_Attribute_Abstract */
            $attribute->setController($controller);
        }
        //Kích hoạt sự kiện onActionExecute
        foreach ($attributes as $attribute) {
            /* @var  $attribute XPHP_Action_Attribute_Abstract */
            $attribute->onActionExecute();
        }
        //Gọi Action
        $model = $this->_getModel($controller, $action);
        if ($model)
            $result = $controller->$action($model);
        else
            $result = $controller->$action();
        //Kích hoạt sự kiện onActionExecuted với tham số truyền vào là ActionResult
        foreach ($attributes as $attribute) {
            /* @var  $attribute XPHP_Action_Attribute_Abstract */
            $attribute->onActionExecuted($result);
        }
        //Free controller
        foreach ($attributes as $attribute) {
            /* @var  $attribute XPHP_Action_Attribute_Abstract */
            $attribute->freeController();
        }
        return $result;
    }
    /**
     * Phương thức phân tích model, lấy ra model
     * @param XPHP_Controller $controller
     * @param string $action
     * @return XPHP_Model | boolean
     */
    private function _getModel (&$controller, $action)
    {
        require_once 'XPHP/Runtime.php';
        $runtime = XPHP_Runtime::getInstance();
        $runtimeKey = "Model_Of_" . md5("{$this->router->module}
        								_{$this->router->controller}
        								_{$this->router->action}");
        if (isset($runtime->$runtimeKey)) {
            $modelClass = $runtime->$runtimeKey;
            $obj = new $modelClass();
            $controller->setModel($obj);
            $controller->parseModelParams();
            $model = &$controller->getModel();
            return $model;
        } else {
            //Lấy ra toàn bộ các params phương thức $action của $controller
            $refMethod = new ReflectionMethod($controller, $action);
            $params = $refMethod->getParameters();
            foreach ($params as $param) {
                //Lấy ra lớp của param
                if ($param->getClass()) {
                    $modelClass = $param->getClass()->getName();
                    $obj = new $modelClass();
                    if ($obj instanceof XPHP_Model) {
                        $controller->setModel($obj);
                        $controller->parseModelParams();
                        $model = &$controller->getModel();
                        
                        //Lưu vào runtime
                        $runtime->$runtimeKey = $modelClass;
                        return $model;
                    }
                }
            }
        }
        return false;
    }
}