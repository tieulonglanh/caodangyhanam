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
 * @package		XPHP_DataType
 * @author		XWEB Dev Team
 * @copyright	Copyright (c) 2010-2011 XWEB. (http://xweb.vn)
 * @license		http://xphp.xweb.vn/license.html     GNU GPL License
 * @version		$Id: DataType.php 20116 2011-22-08 02:05:09 Mr.UBKey $
 */
/**
 * Lớp hỗ trợ về kiểu dữ liệu trong PHP
 *
 * @category	XPHP
 * @package		XPHP_Array
 * @author		Mr.UBKey
 * @link		http://xphp.xweb.vn/user_guide/xphp_datatype.html
 */
class XPHP_DataType
{
    /**
     * Các giá trị trả về true đối với kiểu boolean
     */
    private static $boolean_true_array = array('true', 'yes', '1');
    public static function convert ($var, $type)
    {
        switch ($type) {
            case 'bool':
            case 'boolean':
                return in_array(strtolower($var), self::$boolean_true_array);
            default:
                settype($var, $type);
                return $var;
        }
    }
}