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
 * @package		XPHP_Action
 * @subpackage  XPHP_Action_Attribute
 * @author		XWEB Dev Team
 * @copyright	Copyright (c) 2010-2011 XWEB. (http://xweb.vn)
 * @license		http://xphp.xweb.vn/license.html     GNU GPL License
 * @version		$Id: Compresses.php 20109 2011-22-08 02:05:09 Mr.UBKey $
 */
/**
 * XPHP_Action_Attribute_Abstract
 * @see XPHP_Action_Attribute_Abstract
 */
require_once 'XPHP/Action/Attribute/Abstract.php';
/**
 * Lớp Action Attribute Compresses dùng để nén output cho Action
 * @package		XPHP_Action
 * @subpackage  XPHP_Action_Attribute
 * @author 		XWEB Dev Team
 */
class XPHP_Action_Attribute_Compresses extends XPHP_Action_Attribute_Abstract
{
    /**
     * Sự kiện này được thực thi khi trước khi action được thực thi
     */
    public function onActionExecute ()
    {
        if (! ob_start("ob_gzhandler"))
            ob_start();
    }
    
    static function __set_state(array $array)
    {
        $tmp = new self();
        foreach($array as $k => $v) {
            $tmp->$k = $v;
        }
        return $tmp;
    }
}