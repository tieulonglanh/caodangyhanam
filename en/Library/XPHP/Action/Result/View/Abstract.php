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
 * @subpackage  XPHP_Action_Result_View
 * @author		XWEB Dev Team
 * @copyright	Copyright (c) 2010-2011 XWEB. (http://xweb.vn)
 * @license		http://xphp.xweb.vn/license.html     GNU GPL License
 * @version		$Id: Abstract.php 20116 2011-22-08 02:05:09 Mr.UBKey $
 */
/**
 * Lớp Abstract của XPHP_Action_Result_View
 * @package		XPHP_Action
 * @subpackage  XPHP_Action_Result_View
 * @author		XWEB Dev Team
 */
abstract class XPHP_Action_Result_View_Abstract extends XPHP_Action_Result_Abstract
{
    /**
     * XPHP_Router
     * @var XPHP_Router
     */
    protected $router;
    /** 
	 * XPHP_Layout
	 * @var XPHP_Layout
	 */
	protected $_layout;
	/**
     * Tên file layout
     * @var string
     */
    protected $layout;
	/**
     * Dữ liệu chuyển từ controller sang view
     * @var stdClass
     */
    protected $viewData;	
	/**
 	 * XPHP_View
	 * @var XPHP_View
	 */
	protected $view;
	
	/**
	 * Khởi tạo
	 */
	public function __construct()
	{
	    //Load các biến trong file config với node view > viewData
        require_once 'XPHP/Config.php';
        XPHP_Config::load($this->viewData, 'view > viewData');
	}
	
	/**
	 * Gán router
	 * @param XPHP_Router $router
	 */
	public function setRouter(&$router)
	{
	    $this->router = $router;
	}
	
	/**
	 * Gán giá trị từ action sang view
	 * @param mixed $data
	 * @param bool $override
	 */
	public function setViewData($data, $override = false)
	{
        if ($override)
            $this->viewData = $data;
        else {
            foreach ($data as $key => $value) {
                $this->viewData->$key = $value;
            }
        }
	}
	
	/**
	 * Thiết lập layout file
	 * @param string $layout
	 */
	public function setLayoutFile($layout)
	{
	    $this->layout = $layout;
	}
}