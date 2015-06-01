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
 * @package        XPHP_Rewrite
 * @author        XWEB Dev Team
 * @copyright    Copyright (c) 2010-2011 XWEB. (http://xweb.vn)
 * @license        http://xphp.xweb.vn/license.html     GNU GPL License
 * @version        $Id: Rewrite.php 20109 2011-22-08 02:05:09 Mr.UBKey $
 */
/**
 * XPHP_Array.
 *
 * @see XPHP_Array
 */
require_once 'XPHP/Array.php';

/**
 * XPHP_Runtime.
 *
 * @see XPHP_Runtime
 */
require_once 'XPHP/Runtime.php';

/**
 * Lớp xử lý rewrite url của hệ thống
 * Rewrite xử lý tự động và có thể bật tắt trong file cấu hình
 *
 * @category    XPHP
 * @package        XPHP_Rewrite
 * @author        Mr.UBKey
 * @link        http://xphp.xweb.vn/user_guide/xphp_rewrite.html
 */
class XPHP_Rewrite
{
    /**
     * Router hiện tại
     * @var XPHP_Router
     */
    protected $_router;
    /**
     * Trạng thái có cho phép xử lý rewrite hay không?
     * @var boolean
     */
    public $enable;
    /**
     * Đường dẫn tới file định nghĩa các route
     * @var string
     */
    public $file;
    /**
     * Các router được định nghĩa trong file rewrite
     * @var array
     */
    private $_routers;
    /**
     * Url gốc
     * @var string
     */
    private $_baseUrl;
    /**
     * Request uri
     * @var string
     */
    private $_requestUri;

    /**
     * Khởi tạo XPHP_Rewrite hỗ trợ rewrite
     * @param XPHP_Router $router
     * @throws XPHP_Exception
     */
    public function __construct($router)
    {
        //Load cấu hình trong file config với node rewrite
        require_once 'XPHP/Config.php';
        XPHP_Config::load($this, 'rewrite');
        //Gán router hiện tại cho rewrite
        $this->_router = $router;
        //Nếu có bật rewrite
        if ($this->enable) {
            require_once 'XPHP/Exception.php';
            //Kiểm tra có file rewrite hay không?
            if (!is_file($this->file))
                throw new XPHP_Exception(
                    "Không tìm thấy tệp tin cấu hình rewrite đường dẫn " .
                    $this->file .
                    ". Nếu không sử dụng rewrite hãy tắt chức năng này trong file cấu hình");
            else {
                //Lấy ra toàn bộ danh sách các router
                $runtime = XPHP_Runtime::getInstance();
                $key = "rewrite_";
                if (isset($runtime->$key)) {
                    $this->_routers = $runtime->$key;
                } else {
                    require_once 'XPHP/Xml.php';
                    $xml = new XPHP_Xml($this->file);
                    $routes = $xml->getArray();
                    $runtime->$key = $routes['router'];
                    $this->_routers = $routes['router'];
                }

                //Lấy ra các thông số URI cơ bản
                require_once 'XPHP/Uri.php';
                $uri = new XPHP_Uri();
                $this->_baseUrl = $uri->getBaseUrl();
                $this->_requestUri = $uri->getRequestUri();
            }
        } //Nếu rewrite = 0 thì không xử lý rewrite
        else
            return;
    }

    /**
     * Xử lý hệ thống rewrite
     */
    public function processing()
    {
        //Nếu không bật cấu hình trả về fale
        if (!$this->enable)
            return false;
        /*
         * Lấy ra request hiện tại của hệ thống
         */
        //Lấy ra uri hiện tại
        $request = $this->_getRequest();
        /*
           * Kiểm tra xem có trùng request hay không?
           */
        foreach ($this->_routers as $rout) {
            if (isset($rout['@preg']) && $rout['@preg'] != "") {
                //Nếu request trùng với router
                if ($this->compareRouter($rout['@preg'],
                                         $request)
                ) {
                    if (isset($rout['@module']) && $rout['@module'] != "")
                        $this->_router->module = $rout['@module'];
                    if (isset($rout['@controller']) && $rout['@controller'] != "")
                        $this->_router->controller = $rout['@controller'];
                    else
                        $this->_router->setController("Index");
                    if (isset($rout['@action']) && $rout['@action'] != "")
                        $this->_router->action = $rout['@action'];
                    else
                        $this->_router->action = "index";
                    //Phân tích & Gán args
                    $this->_router->args = $this->parseArg($rout['#text']);
                    //Trả về true để đánh dấu router đã được rewrite
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Lưu trữ mảng tham số truyền vào
     * @var array
     */
    private $_args;

    /**
     * Phương thức so sanh request hiện tại và router
     * @param string $router_preg
     * @param string $request
     */
    private function compareRouter($router_preg, $request)
    {
        $router_preg = trim($router_preg);
        $request = trim($request);
        $out = array();
        $x = preg_match("/^$router_preg/i", $request, $out);
        for ($i = 1; $i < sizeof($out); $i++) {
            $this->_args[] = $out[$i];
        }
        return $x;
    }

    /**
     * Phân tích url định nghĩa trong rewrite để lấy tham số truyền vào dạng args
     * @param string $rewrite_url
     * @return array
     */
    private function parseArg($rewrite_url)
    {
        $total = array();
        $out = array();
        $prg = '/\{(\w+)\}/';
        while (preg_match($prg, $rewrite_url, $out)) {
            $total = array_merge($total, array($out[1]));
            $rewrite_url = str_replace($out[1], "", $rewrite_url);
        }
        $args = array();
        for ($i = 0; $i < sizeof($total); $i++) {
            if (isset($this->_args[$i]))
                $args[$total[$i]] = $this->_args[$i];
            else
                $args[$total[$i]] = null;
        }
        $this->_args = $args;
        return $this->_args;
    }

    /**
     * Lấy ra request
     */
    private function _getRequest()
    {
        $request = "";
        //Lấy ra uri hiện tại
        if (!empty($this->_baseUrl)) {
            $arrRequest = explode($this->_baseUrl . "/", $this->_requestUri);
            //Xóa toàn bộ khoảng trắng trong mảng
            require_once 'XPHP/Array.php';
            $arrRequest = XPHP_Array::trim($arrRequest);
            if (isset($arrRequest[1]))
                $request = $arrRequest[1];
        } else {
            if (substr($this->_requestUri, 0, 1) == "/")
                $request = substr($this->_requestUri, 1);
            else
                $request = $this->_requestUri;
        }
        return $request;
    }

    /**
     * Lấy ra đường dẫn đã rewrite
     */
    public function getUrl()
    {
        foreach ($this->_routers as $rout) {
            /* 
			 * Kiểm tra $router trùng với router được định nghĩa trong file
			 */
            //Kiểm tra $area, $controller, $action
            if ($this->_router->module) {
                if (!isset($rout['@module']) ||
                    $rout['@module'] != $this->_router->module
                )
                    continue;
            }
            if ($this->_router->controller) {
                if (!isset($rout['@controller']) ||
                    $rout['@controller'] != $this->_router->controller
                )
                    continue;
            } else
                continue;
            if ($this->_router->action) {
                if (!isset($rout['@action']) ||
                    $rout['@action'] != $this->_router->action
                )
                    continue;
            } else
                continue;
            //Link được định nghĩa trong rewrite
            $url = $rout['#text'];
            //Kiểm tra args
            if (sizeof($this->_router->args) !=
                sizeof($this->parseArg($url))
            )
                continue;
            //Replace các tham số thành các giá trị tương ứng
            foreach ($this->_router->args as $n => $v) {
                $url = str_replace("{" . $n . "}", $v, $url);
            }
            require_once 'XPHP/Url.php';
            return XPHP_Url::getAbsoluteUrl() . '/' . $url;
        }
        return null;
    }
}