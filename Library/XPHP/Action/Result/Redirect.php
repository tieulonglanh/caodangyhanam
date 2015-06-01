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
 * @version		$Id: Redirect.php 20116 2011-22-08 02:05:09 Mr.UBKey $
 */
/**
 * XPHP_Action_Result_Interface
 * @see XPHP_Action_Result_Interface
 */
require_once 'XPHP/Action/Result/Interface.php';
/**
 * XPHP_Url
 * @see XPHP_Url
 */
require_once 'XPHP/Url.php';
/**
 * Lớp XPHP_Action_Result_Redirect thực thi redirect
 * @package		XPHP_Action
 * @subpackage  XPHP_Action_Result
 * @author		XWEB Dev Team
 */
class XPHP_Action_Result_Redirect implements XPHP_Action_Result_Interface
{
	/**
	 * Đường dẫn URL
	 * @var string
	 */
	public $url;
	
	public function __construct($url)
	{
		require_once 'XPHP/Router.php';
		if($url instanceof XPHP_Router)
		{
			$this->url = XPHP_Url::getActionUrl($url);
		}
		else if(is_string($url))
		{
			$this->url = $url;
		}
		else 
		{
			throw new XPHP_Exception("Tham số truyền vào của lớp XPHP_Action_Result_Redirect là một chuỗi hoặc thể hiện của XPHP_Router");
		}
	}
	
	public function execute()
	{
		XPHP_Url::redirectUrl($this->url);
	}
}