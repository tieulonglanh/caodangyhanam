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
 * @package		XPHP_Cookie
 * @author		XWEB Dev Team
 * @copyright	Copyright (c) 2010-2011 XWEB. (http://xweb.vn)
 * @license		http://xphp.xweb.vn/license.html     GNU GPL License
 * @version		$Id: Cookie.php 20112 2011-22-08 02:05:09 Mr.UBKey $
 */
/**
 * Lớp hỗ trợ việc sử dụng cookie
 *
 * @category	XPHP
 * @package		XPHP_Cookie
 * @author		Mr.UBKey
 * @link		http://xphp.xweb.vn/user_guide/xphp_cookie.html
 */
class XPHP_Cookie
{
    /**
     * Thời gian hết hạn của session
     * @var int
     */
    public $expire;
    /**
     * Thời gian hết hạng của cookie
     * @param int $time
     * @return XPHP_Cookie
     */
    public function expire ($time)
    {
        if (is_integer($time))
            $this->expire = $time;
        else 
            if (is_string($time))
                $this->expire = $this->convertExpireTime($time);
        return $this;
    }
    /**
     * Thêm cookie mới
     * @param string $index Tên cookie
     * @param string $value Giá trị của cookie
     * @param time $expire Thời gian hết hạn của cookie
     */
    public function __set ($name, $value)
    {
        $_COOKIE[$name] = $value;
        if ($this->expire === NULL)
            $expire = time() + $this->convertExpireTime("1M");
        else
            $expire = $this->expire;
        setcookie($name, $value, $expire, '/');
    }
    /**
     * Lấy ra giá trị của cookie
     * @param string $index Tên cookie
     */
    public function __get ($name)
    {
        if (isset($_COOKIE[$name])) {
            return $_COOKIE[$name];
        }
        return null;
    }
    /**
     * Kiểm tra xem một cookie có tồn tại không
     * @param string $index Tên cookie
     */
    public function __isset ($name)
    {
        return isset($_COOKIE[$name]);
    }
    /**
     * Xóa cookie
     * @param string $index Tên của cookie
     */
    public function __unset ($name)
    {
        unset($_COOKIE[$name]);
        setcookie($name, "", time() - $this->convertExpireTime("1M"), '/');
    }
    /**
     * Chuyển định dạng thời gian từ kí tự thành số
     * @param string $timestr VD: 10s, 10m, 10s
     * @return int
     */
    private function convertExpireTime ($timestr)
    {
        require_once 'XPHP/Time.php';
        return XPHP_Time::toTime($timestr);
    }
}