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
 * @version		$Id: Attribute.php 20112 2011-22-08 02:05:09 Mr.UBKey $
 */
/**
 * XPHP_Attribute_Adapter.
 *
 * @see XPHP_Attribute_Adapter
 */
require_once 'XPHP/Attribute/Adapter.php';
/**
 * Lớp giao tiếp truy xuất Attribute
 *
 * @category	XPHP
 * @package		XPHP_Attribute
 * @author		Mr.UBKey
 * @link		http://xphp.xweb.vn/user_guide/xphp_attribute.html
 */
class XPHP_Attribute
{
    /**
     * @var XPHP_Attribute_Adapter Thể hiện của lớp XPHP_Attribute_Adapter
     */
    private static $adapter;
    /**
     * @return XPHP_Attribute_Adapter thể hiện của lớp XPHP_Attribute_Adapter
     */
    public static function getAdapter ()
    {
        if (! isset(self::$adapter))
            self::$adapter = new XPHP_Attribute_Adapter();
        return self::$adapter;
    }
    /**
     * Trả về XPHP_Attribute_Usage
     * @see XPHP_Attribute_Adapter::getUsageAttribute()
     */
    public static function getUsage ($class)
    {
        return self::getAdapter()->getUsageAttribute($class);
    }
    /**
     * Lấy ra Attributes của lớp
     * @see XPHP_Attribute_Manager::getClassAnnotations()
     */
    public static function ofClass ($class, $type = null)
    {
        return self::getAdapter()->getClassAttributes($class, $type);
    }
    /**
     * Lấy ra Attributes của phương thức
     * @see XPHP_Attribute_Manager::getMethodAttributes()
     */
    public static function ofMethod ($class, $method = null, $type = null)
    {
        return self::getAdapter()->getMethodAttributes($class, $method, $type);
    }
    /**
     * Lấy ra Attributes của thuộc tính
     * @see XPHP_Attribute_Manager::getPropertyAttributes()
     */
    public static function ofProperty ($class, $property = null, $type = null)
    {
        return self::getAdapter()->getPropertyAttributes($class, $property, 
        $type);
    }
}