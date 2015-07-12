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
 * @version		$Id: Usage.php 20109 2011-22-08 02:05:09 Mr.UBKey $
 */
/**
 * XPHP_Attribute_Abstract
 * @see XPHP_Attribute_Abstract
 */
require_once 'XPHP/Attribute/Abstract.php';
/**
 * Attribute bắt buộc phải có trong các lớp Attribute, định nghĩa khả năng sử dụng Attribute này
 * @package		XPHP_Attribute
 * @author		XWEB Dev Team
 */
#[Usage(class = true, method = true, property = true, inherited = true, multiple = true)]
class XPHP_Attribute_Usage extends XPHP_Attribute_Abstract
{
    /**
     * @var boolean Thiết lập true cho các Attribute có thể được áp dụng cho các lớp.
     */
    public $class = false;
    /**
     * @var boolean Thiết lập true cho các Attribute có thể được áp dụng cho các thuộc tính.
     */
    public $property = false;
    /**
     * @var boolean Thiết lập true cho các Attribute có thể được áp dụng cho các phương thức.
     */
    public $method = false;
    /**
     * @var boolean $multiple Thiết lập true cho Attribute cho phép nhiều trường hợp.
     */
    public $multiple = false;
    /**
     * @var boolean $inherited Thiết lập này true cho các Attribute áp dụng đối với lớp con.
     */
    public $inherited = false;
    
    static function __set_state(array $array)
    {
        $tmp = new self();
        foreach($array as $k => $v) {
            $tmp->$k = $v;
        }
        return $tmp;
    }
}