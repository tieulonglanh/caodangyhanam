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
 * @package		XPHP_Compressor
 * @author		XWEB Dev Team
 * @copyright	Copyright (c) 2010-2011 XWEB. (http://xweb.vn)
 * @license		http://xphp.xweb.vn/license.html     GNU GPL License
 * @version		$Id: Compressor.php 20117 2011-22-08 02:05:09 Mr.UBKey $
 */
/**
 * Lớp hỗ trợ các vấn đề liên quan đến việc nén dữ liệu
 *
 * @category	XPHP
 * @package		XPHP_Compressor
 * @author		Mr.UBKey
 * @link		http://xphp.xweb.vn/user_guide/xphp_compressor.html
 */
class XPHP_Compressor
{
    public function js ($jsCode)
    {
        $jsPaker = new XPHP_Compressor_JsParker();
        return $jsPaker->pack($jsCode);
    }
    public function css ($cssCode)
    {
        $jsPaker = new XPHP_Compressor_JsParker();
        return $jsPaker->pack($cssCode);
         //remove comments
    //$buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
    //remove tabs, spaces, newlines, etc.
    //$buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
    //return $buffer;
    }
}