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
 * @package		XPHP_Time
 * @author		XWEB Dev Team
 * @copyright	Copyright (c) 2010-2011 XWEB. (http://xweb.vn)
 * @license		http://xphp.xweb.vn/license.html     GNU GPL License
 * @version		$Id: Time.php 20109 2011-22-08 02:05:09 Mr.UBKey $
 */
/**
 * Lớp hỗ trợ thời gian trong PHP
 *
 * @category	XPHP
 * @package		XPHP_Time
 * @author		Mr.UBKey
 * @link		http://xphp.xweb.vn/user_guide/xphp_time.html
 */
class XPHP_Time
{
    /**
     * Phương thức chuyển kiểu chuỗi thành thời gian (s)
     * @param string $timestr
     * @return int
     */
    public static function toTime ($timestr)
    {
        $timestr = trim($timestr);
        //Lấy ra kí tự cuối cùng của chuỗi
        $unit = substr($timestr, strlen($timestr) - 1, 1);
        $val = substr($timestr, 0, strlen($timestr) - 1);
        switch ($unit) {
            case 's':
                return (int) $val;
            case 'm':
                return ((int) $val) * 60;
            case 'h':
                return ((int) $val) * 3600;
            case 'd':
                return ((int) $val) * 86400;
            case 'M':
                return ((int) $val) * 2592000;
            case 'y':
                return ((int) $val) * 31104000;
            default:
                return (int) $val * 60;
        }
    }
}