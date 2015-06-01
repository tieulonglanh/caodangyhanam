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
 * @package		XPHP_Session
 * @author		XWEB Dev Team
 * @copyright	Copyright (c) 2010-2011 XWEB. (http://xweb.vn)
 * @license		http://xphp.xweb.vn/license.html     GNU GPL License
 * @version		$Id: Session.php 20109 2011-22-08 02:05:09 Mr.UBKey $
 */
/**
 * Lớp hỗ trợ sử dụng SESSION
 * Sử dụng phương thức tĩnh getInstance() để lấy ra một đối tượng
 *
 * @category	XPHP
 * @package		XPHP_Array
 * @author		Mr.UBKey
 * @link		http://xphp.xweb.vn/user_guide/xphp_array.html
 */
class XPHP_Session
{
    const STARTED = TRUE;
    const NOT_STARTED = FALSE;
    /**
     * Lưu trạng thái Session hiện tại
     * @var bool
     */
    private $sessionState = self::NOT_STARTED;
    /**
     * Thể thiện của Session
     * @var XPHP_Session
     */
    private static $instance;
    /**
     * Trả về thể hiện của lớp 'Session'.
     * Session sẽ tự động khởi tạo mà không cần (Re)starts nó
     * 
     * @return XPHP_Session
     */
    public static function getInstance ()
    {
        if (! isset(self::$instance)) {
            self::$instance = new self();
        }
        self::$instance->startSession();
        return self::$instance;
    }
    /**
     * (Re)starts session.
     * 
     * @return bool TRUE nếu session được khởi tạo thành công, ngược lại FALSE.
     */
    public function startSession ()
    {
        if ($this->sessionState == self::NOT_STARTED) {
            $this->sessionState = session_start();
        }
        return $this->sessionState;
    }
    /**
     * Lưu trữ dữ liệu trong session.
     * VD: $instance->foo = 'bar';
     * 
     * @param name Tên của dữ liệu.
     * @param value Dữ liệu được lưu trữ.
     * @return void
     */
    public function __set ($name, $value)
    {
        $_SESSION[$name] = $value;
    }
    /**
     * Lấy dữ liệu từ session.
     * VD: echo $instance->foo;
     * 
     * @param name Tên của dữ liệu.
     * @return mixed Dữ liệu được lưu trong session.
     */
    public function __get ($name)
    {
        if (isset($_SESSION[$name])) {
            return $_SESSION[$name];
        }
    }
    public function __isset ($name)
    {
        return isset($_SESSION[$name]);
    }
    public function __unset ($name)
    {
        unset($_SESSION[$name]);
    }
    /**
     * Hủy session hiện tại
     * 
     * @return bool TRUE là session đã bị xóa, ngược lại FALSE.
     */
    public function destroy ()
    {
        if ($this->sessionState == self::STARTED) {
            $this->sessionState = ! session_destroy();
            unset($_SESSION);
            return ! $this->sessionState;
        }
        return FALSE;
    }
}