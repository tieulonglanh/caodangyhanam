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
 * @package		XPHP_Attribute
 * @author		XWEB Dev Team
 * @copyright	Copyright (c) 2010-2011 XWEB. (http://xweb.vn)
 * @license		http://xphp.xweb.vn/license.html     GNU GPL License
 * @version		$Id: Abstract.php 20109 2011-22-08 02:05:09 Mr.UBKey $
 */
/**
 * XPHP_Attribute_Exception
 * @see XPHP_Attribute_Exception
 */
require_once 'XPHP/Attribute/Exception.php';
/**
 * XPHP_Attribute_Interface
 * @see XPHP_Attribute_Interface
 */
require_once 'XPHP/Attribute/Interface.php';
/**
 * Lớp abstract của tất cả các lớp Attribute
 * @package		XPHP_Attribute
 * @author		XWEB Dev Team
 */
abstract class XPHP_Attribute_Abstract implements 
XPHP_Attribute_Interface
{
    /**
     * @internal Lấy giá trị các thuộc tính chưa được khai báo
     */
    public function __get ($name)
    {
        throw new XPHP_Attribute_Exception(
        get_class($this) . "::\${$name} thuộc tính chưa được khai báo");
    }
    /**
     * @internal Gán giá trị tới một thuộc tính chưa được khai báo
     */
    public function __set ($name, $value)
    {
        throw new XPHP_Attribute_Exception(
        get_class($this) . "::\${$name} thuộc tính chưa được khai báo");
    }
    /**
     * Phương thức trợ giúp khởi tạo các thuộc tính vô danh.
     * @param array $properties Mảng chưa các thuộc tính của Attribute được đưa vào XPHP_Attribute_Interface::init()
     * @param array $indexes Mảng chứa các thuộc tính vô danh
     */
    protected function _map (&$properties, $indexes)
    {
        foreach ($indexes as $index => $name) {
            if (isset($properties[$index])) {
                $this->$name = $properties[$index];
                unset($properties[$index]);
            }
        }
    }
    /**
     * @internal Khởi tạo Attribute.
     * @see XPHP_Attribute_Interface
     */
    public function init ($properties)
    {
        foreach ($properties as $name => $value)
            $this->$name = $value;
    }
}
