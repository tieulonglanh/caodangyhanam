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
 * @version		$Id: Interface.php 20109 2011-22-08 02:05:09 Mr.UBKey $
 */
/**
 * Lớp giao diện của các Attribute.
 * Giao diện bắt buộc cho toàn bộ các Attribute
 * @package		XPHP_Attribute
 * @author		XWEB Dev Team
 */
interface XPHP_Attribute_Interface
{
    public function init ($properties);
}