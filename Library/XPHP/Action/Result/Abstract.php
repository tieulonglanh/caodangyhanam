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
 * @subpackage  XPHP_Action_Result
 * @author		XWEB Dev Team
 * @copyright	Copyright (c) 2010-2011 XWEB. (http://xweb.vn)
 * @license		http://xphp.xweb.vn/license.html     GNU GPL License
 * @version		$Id: Abstract.php 20116 2011-22-08 02:05:09 Mr.UBKey $
 */
/**
 * XPHP_Action_Result_Interface
 * @see XPHP_Action_Result_Interface
 */
require_once 'XPHP/Action/Result/Interface.php';
/**
 * Lớp Abstract của Action Result
 * @package		XPHP_Action
 * @subpackage  XPHP_Action_Result
 * @author		XWEB Dev Team
 */
abstract class XPHP_Action_Result_Abstract implements XPHP_Action_Result_Interface
{
	/**
	 * Model hệ thống
	 * @var XPHP_Model
	 */
	public $model;
	
	/**
	 * Lớp hỗ trợ cho result
	 */
	protected $helper;
	
	/**
	 * Lấy ra model của result
	 */
	public function getModel()
	{
		if(!empty($this->model))
			return $this->model;
	}
	
	/**
	 * Thiết lập model cho result
	 * @param XPHP_Model $model
	 */
	public function setModel(&$model)
	{
		$this->model = $model;
	}
	
	/**
	 * Thiết lập helper cho result
	 * @param unknown_type $helper
	 */
	public function setHelper($helper)
	{
		$this->helper = $helper;
	}
	
	/**
	 * Lấy ra helper
	 */
	public function getHelper()
	{
		return $this->helper;
	}
	
	/**
	 * Load helper vào result
	 */
	protected function _loadResultHelper()
	{
		foreach (get_object_vars($this->helper) as $name => $objHelper)
			$this->$name = $objHelper;
	}
}