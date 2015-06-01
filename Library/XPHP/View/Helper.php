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
 * @package		XPHP_View
 * @author		XWEB Dev Team
 * @copyright	Copyright (c) 2010-2011 XWEB. (http://xweb.vn)
 * @license		http://xphp.xweb.vn/license.html     GNU GPL License
 * @version		$Id: Helper.php 20109 2011-22-08 02:05:09 Mr.UBKey $
 */
/**
 * XPHP_View_Helper Lớp hỗ trợ của View
 *
 * @category	XPHP
 * @package		XPHP_View
 * @author		Mr.UBKey
 * @link		http://xphp.xweb.vn/user_guide/xphp_view_helper.html
 */
class XPHP_View_Helper
{
    public function __construct ()
    {
        //Load cấu hình trong file config với node view
        require_once 'XPHP/Config.php';
        $helpers = XPHP_Config::get('view > helper');
        //Có một helper duy nhất
        if (isset($helpers->class) && isset($helpers->name)) {
            $name = $helpers->name;
            $class = $helpers->class;
            $this->$name = new $class();
        } //Có nhiều helper
        else 
            if (is_array($helpers)) {
                foreach ($helpers as $helper) {
                    $name = $helper->name;
                    $class = $helper->class;
                    $this->$name = new $class();
                }
            }
    }
}