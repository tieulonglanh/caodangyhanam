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
 * @package		XPHP_Url
 * @author		XWEB Dev Team
 * @copyright	Copyright (c) 2010-2011 XWEB. (http://xweb.vn)
 * @license		http://xphp.xweb.vn/license.html     GNU GPL License
 * @version		$Id: Url.php 20109 2011-22-08 02:05:09 Mr.UBKey $
 */
/**
 * XPHP_Url
 *
 * @category	XPHP
 * @package		XPHP_Url
 * @author		Mr.UBKey
 * @link		http://xphp.xweb.vn/user_guide/xphp_url.html
 */
class XPHP_Url
{
    /**
     * Router của hệ thống
     * @var XPHP_Router
     */
    public $_router;
    /**
     * Router của hệ thống
     * @param XPHP_Router $router
     */
    public function __construct ($router = NULL)
    {
        //Load cấu hình trong file config với node view
        require_once 'XPHP/Config.php';
        XPHP_Config::load($this, 'url');
        if ($router !== NULL)
            $this->_router = $router;
    }
    /**
     * Lấy ra đường dẫn tới tệp tin chứa nội dung
     * @param string $path
     * @param bool $check_exist
     * @throws XPHP_Exception
     */
    public function content ($path, $check_exist = false)
    {
        if($check_exist)
        {
            if (! is_file($path))
                require_once 'XPHP/Exception.php';
                throw new XPHP_Exception("Không tìm thấy tệp tin tại " . $path);
        }
        return self::getApplicationUrl() . "/" . $path;
    }
    /**
     * Overload method action and geturl
     */
    function __call ($method_name, $arguments)
    {
        //we inspect number of arguments
        if ($method_name == "action" && count($arguments) == 1) {
            return $this->action1($arguments[0]);
        } else 
            if ($method_name == "action" && count($arguments) == 2 &&
             is_string($arguments[1])) {
                return $this->action2($arguments[0], $arguments[1]);
            } else 
                if ($method_name == "action" && count($arguments) == 2 &&
                 is_array($arguments[1])) {
                    return $this->action5($arguments[0], $arguments[1]);
                } else 
                    if ($method_name == "action" && count($arguments) == 3 &&
                     is_string($arguments[2])) {
                        return $this->action3($arguments[0], $arguments[1], 
                        $arguments[2]);
                    } else 
                        if ($method_name == "action" && count($arguments) == 3 &&
                         is_array($arguments[2])) {
                            return $this->action6($arguments[0], $arguments[1], 
                            $arguments[2]);
                        } else 
                            if ($method_name == "action" &&
                             count($arguments) == 4) {
                                return $this->action4($arguments[0], 
                                $arguments[1], $arguments[2], $arguments[3]);
                            } //application
                            else 
                                if ($method_name == "getUrl" &&
                                 count($arguments) == 0) {
                                    return $this->getUrl1();
                                } else 
                                    if ($method_name == "getUrl" &&
                                     count($arguments) == 1) {
                                        return $this->getUrl2($arguments[0]);
                                    }
    }
    function action1 ($action)
    {
        $router = $this->_router->getInstant();
        $router->action = $action;
        //Thiết lập args rỗng
        $router->args = array();
        return self::getActionUrl($router);
    }
    function action2 ($action, $controller)
    {
        $router = $this->_router->getInstant();
        $router->action = $action;
        $router->controller = $controller;
        //Thiết lập args rỗng
        $router->args = array();
        return self::getActionUrl($router);
    }
    function action3 ($action, $controller, $module)
    {
        $router = $this->_router->getInstant();
        $router->action = $action;
        $router->controller = $controller;
        $router->module = $module;
        //Thiết lập args rỗng
        $router->args = array();
        return self::getActionUrl($router);
    }
    function action4 ($action, $controller, $module, $args)
    {
        $router = $this->_router->getInstant();
        $router->action = $action;
        $router->controller = $controller;
        $router->module = $module;
        $router->args = $args;
        return self::getActionUrl($router);
    }
    function action5 ($action, $args)
    {
        $router = $this->_router->getInstant();
        $router->action = $action;
        $router->args = $args;
        return self::getActionUrl($router);
    }
    function action6 ($action, $controller, $args)
    {
        $router = $this->_router->getInstant();
        $router->action = $action;
        $router->controller = $controller;
        $router->args = $args;
        return self::getActionUrl($router);
    }
    function getUrl1 ()
    {
        return self::getApplicationUrl();
    }
    function getUrl2 ($path)
    {
        if (strpos($path, self::getRelativeUrl()) === false) {
            return self::getApplicationUrl() . '/' . $path;
        } else 
            if (strpos($path, self::getRelativeUrl()) == 0) {
                $url = "";
                if (! empty($_SERVER['HTTPS']))
                    $url .= "https://" . $_SERVER['SERVER_NAME'];
                else
                    $url .= "http://" . $_SERVER['SERVER_NAME'];
                return $url . $path;
            }
    }
    /*
  	 * Các phương thức tĩnh của lớp URL
  	 */
    /**
     * Lấy ra Url của ứng dụng
     */
    public static function getApplicationUrl ()
    {
        $url = "";
        if (! empty($_SERVER['HTTPS']) && strtolower($_SERVER["HTTPS"]) == "on")
            $url .= "https://" . $_SERVER['SERVER_NAME'];
        else
            $url .= "http://" . $_SERVER['SERVER_NAME'];
        $url .= self::getRelativeUrl();
        return $url;
    }
    /**
     * Lấy ra đường dẫn tuyệt đối
     * @param string|null $url đường dẫn thứ cấp
     */
    public static function getAbsoluteUrl($url = null)
    {
        if($url)
            return self::getApplicationUrl() . '/' . trim($url, '/');
        else
            return self::getApplicationUrl();
    }
    /**
     * Lấy ra đường dẫn tương đối
     */
    public static function getRelativeUrl ()
    {
        $phpself = explode('/', $_SERVER['PHP_SELF']);
        $url = "";
        for ($i = 0; $i < sizeof($phpself); $i ++) {
            if ($i != 0 && $i != sizeof($phpself) - 1)
                $url .= '/' . $phpself[$i];
        }
        return $url;
    }
    /**
     * Lấy ra đường dẫn tuyệt đối tới một action
     * @param XPHP_Router $ro
     * @param bool $origin Truyền vào true nếu muốn lấy link không có rewrite
     */
    public static function getAbsoluteActionUrl($ro, $origin = false)
    {
        return self::getAbsoluteUrl(self::getActionUrl($ro, $origin));
    }
    /**
     * Lấy ra Url của action
     * @param mixed $ro array|XPHP_Router
     * @@param bool $origin Truyền vào true nếu muốn lấy link không có rewrite
     */
    public static function getActionUrl ($ro, $origin = false)
    {
    	//Chuyển ro thành Router
    	require_once 'XPHP/Router.php';
    	$router = new XPHP_Router();
    	$router->setInstance($ro);

        if(!$origin)
        {
            /*
    		 * Kiểm tra xem người dùng có bật rewrite hay không ?
    		 * Nếu bật gọi tới hàm lấy đường dẫn đã rewrite nếu trả về null thì tiếp tục xử lý
    		 * Nếu chưa thì tiếp tục xử lý
    		 */
            //Lấy ra cấu hình trạng thái trong file config
            $rw_status = XPHP_Config::get('rewrite')->enable;
            if ($rw_status) {
                $url = self::getActionUrlRewrite($router);
                if ($url != null)
                    return $url;
            }
        }
        //Lấy ra đường dẫn đầy đủ
        $url = self::getAbsoluteUrl() . "/";
        //Xử lý bình thường
        //Lấy các giá trị từ XPHP_Router
        $module = $router->module;
        $args = $router->args;
        $controller = $router->controller;
        $action = $router->action;
        if ($module) {
            $url .= $module;
            if (isset($args['module']))
                $url .= '-' . $args['module'];
            $url .= '/';
        }
        //Nếu controller = Index và action != index gán controller giá trị Index
        //Nếu url = default url Index/index -> $url = ''
        if ($controller &&
         ($controller != "Index" ||
         ($controller == "Index" && $action != "index")))
            $url .= $controller;
        if ($action != "index")
            $url .= '/' . $action;
        if (count($args) > 0) {
            $i = 1;
            foreach ($args as $key => $arg) {
                if ((string) $key != "module")
                {
                    if($i == 1 && ! empty($module) && ($action == "index") && ($controller == "Index"))
                        $url .= $arg;
                    else
                        $url .= '/' . $arg;
                    $i++;
                }                
            }
        }
        return trim($url, '/');
    }
    /**
     * Lấy ra Url của action khi hệ thống sử dụng rewrite
     * @param mixed $ro array|XPHP_Router
     */
    public static function getActionUrlRewrite ($ro)
    {
        require_once 'XPHP/Rewrite.php';
        $rewrite = new XPHP_Rewrite($ro);
        return $rewrite->getUrl();
    }
    /**
     * Điều hướng với Router
     * @param mixed $ro
     */
    public static function redirectAction ($ro)
    {
        //Nếu tham số là một đối tượng (XPHP_Router) hoặc một mảng
        if (is_object($ro) || is_array($ro))
            self::redirectUrl(self::getActionUrl($ro));
             //Nếu tham số là chuỗi
        else 
            if (is_string($ro)) {
                self::redirectUrl(self::getRelativeUrl() . '/' . $ro);
            }
    }
    /**
     * Điều hướng với Url
     * @param string $url
     */
    public static function redirectUrl ($url)
    {
        if ($url) {
            header('Location:' . $url);
            exit();
        }
    }
    /**
     * Lấy ra Url hiện tại
     */
    public static function getCurrentUrl ()
    {
    	if (! empty($_SERVER['HTTPS']) && strtolower($_SERVER["HTTPS"]) == "on")
    		$url = "https://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    	else
    		$url = "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        return $url;
    }
    /**
     * Mã hóa url
     * @param string $url
     */
    public function encode ($url)
    {
        $url = str_replace('/', '~', $url);
        $url = str_replace('.', '$', $url);
        return urlencode($url);
    }
    /**
     * Giải mã url đã được mã hóa
     * @param string $url
     */
    public function decode ($url)
    {
        $url = urldecode($url);
        $url = str_replace('~', '/', $url);
        $url = str_replace('$', '.', $url);
        return $url;
    }
    /**
     * Lấy ra Url hiện tại bằng router hiện tại của hệ thống
     */
    public function current ()
    {
        return self::getActionUrl($this->_router);
    }
}