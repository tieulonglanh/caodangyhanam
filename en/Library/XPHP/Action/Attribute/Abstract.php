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
 * @version		$Id: Abstract.php 20109 2011-22-08 02:05:09 Mr.UBKey $
 */
/**
 * XPHP_Attribute_Abstract
 * @see XPHP_Attribute_Abstract
 */
require_once 'XPHP/Attribute/Abstract.php';
/**
 * Lớp Abstract của Action Attribute
 * @package		XPHP_Action
 * @subpackage  XPHP_Action_Attribute
 * @author		XWEB Dev Team
 */
#[Usage(method = true, inherited = true)]
class XPHP_Action_Attribute_Abstract extends XPHP_Attribute_Abstract
{
    /**
     * Sự kiện này được thực thi khi trước khi action được thực thi
     */
    public function onActionExecute ()
    {}
    /**
     * Sự kiện này tự động thực thi sau khi action được thực thi
     * @param XPHP_View_Result_Abstract $result
     */
    public function onActionExecuted ($result)
    {}
    /**
     * Controller
     * @var XPHP_Controller
     */
    protected $controller;
    /**
     * Gán controller cho Attribute
     * @param XPHP_Controller $controller
     */
    public function setController (&$controller)
    {
        $this->controller = $controller;
    }
    
    public function freeController()
    {
        $this->controller = null;
    }
}