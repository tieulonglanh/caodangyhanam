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
 * @package		XPHP_Exception
 * @author		XWEB Dev Team
 * @copyright	Copyright (c) 2010-2011 XWEB. (http://xweb.vn)
 * @license		http://xphp.xweb.vn/license.html     GNU GPL License
 * @version		$Id: Exception.php 20109 2011-22-08 02:05:09 Mr.UBKey $
 */
/**
 * Lớp ngoại lệ cơ bản của XPHP
 *
 * @category	XPHP
 * @package		XPHP_Exception
 * @author		Mr.UBKey
 * @link		http://xphp.xweb.vn/user_guide/xphp_exception.html
 */
class XPHP_Exception extends Exception
{
    /**
     * Khởi tạo lớp ngoại lệ
     *
     * @param  string $msg Thông báo lỗi
     * @param  int $code Mã lỗi
     * @param  Exception $previous
     * @return void
     */
    public function __construct ($msg = '', $code = 0)
    {
        parent::__construct($msg, (int) $code);
    }
}
