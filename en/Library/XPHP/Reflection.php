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
 * @package		XPHP_Reflection
 * @author		XWEB Dev Team
 * @copyright	Copyright (c) 2010-2011 XWEB. (http://xweb.vn)
 * @license		http://xphp.xweb.vn/license.html     GNU GPL License
 * @version		$Id: Reflection.php 20109 2011-22-08 02:05:09 Mr.UBKey $
 */
/**
 * Lớp hỗ trợ việc Reflection lấy thông tin của lớp , thuộc tính và phương thức
 *
 * @category	XPHP
 * @package		XPHP_Reflection
 * @author		Mr.UBKey
 * @link		http://xphp.xweb.vn/user_guide/xphp_reflection.html
 */
class XPHP_Reflection
{
    private $objRe;
    public function __construct ($agrument)
    {
        if (is_object($agrument))
            $this->objRe = $agrument;
        else 
            if (is_string($agrument)) {
                $this->objRe = new $agrument();
            } else
                throw new XPHP_Exception(
                "Tham số truyền vào của XPHP_Reflection không phải là object");
    }
    public function getProperties ($filer = null)
    {
        $reflect = new ReflectionClass($this->objRe);
        if($filer == null)
            $props = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);
        else
            $props = $reflect->getProperties((int)$filer);
        return $props;
    }
}