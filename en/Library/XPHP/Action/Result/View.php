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
 * @version		$Id: View.php 20116 2011-22-08 02:05:09 Mr.UBKey $
 */
/**
 * XPHP_Registry
 * @see XPHP_Registry
 */
require_once 'XPHP/Registry.php';
/**
 * XPHP_View_Helper
 * @see XPHP_View_Helper
 */
require_once 'XPHP/View/Helper.php';
/**
 * XPHP_Html
 * @see XPHP_Html
 */
require_once 'XPHP/Html.php';
/**
 * XPHP_Form
 * @see XPHP_Form
 */
require_once 'XPHP/Form.php';
/**
 * XPHP_Url
 * @see XPHP_Url
 */
require_once 'XPHP/Url.php';
/**
 * XPHP_Asset
 * @see XPHP_Asset
 */
require_once 'XPHP/Asset.php';
/**
 * XPHP_Cookie
 * @see XPHP_Cookie
 */
require_once 'XPHP/Cookie.php';
/**
 * XPHP_Session
 * @see XPHP_Session
 */
require_once 'XPHP/Session.php';
/**
 * XPHP_Cache_Frontend
 * @see XPHP_Cache_Frontend
 */
require_once 'XPHP/Cache/Frontend.php';
/**
 * XPHP_Layout_Section
 * @see XPHP_Layout_Section
 */
require_once 'XPHP/Layout/Content.php';
/**
 * XPHP_Layout_Section
 * @see XPHP_Layout_Section
 */
require_once 'XPHP/Layout/Section.php';
/**
 * XPHP_Action_Result_View_Abstract
 * @see XPHP_Action_Result_View_Abstract
 */
require_once 'XPHP/Action/Result/View/Abstract.php';
/**
 * Lớp XPHP_Action_Result_View hiển thị View hệ thống
 * @package		XPHP_Action
 * @subpackage  XPHP_Action_Result
 * @author		XWEB Dev Team
 */
class XPHP_Action_Result_View extends XPHP_Action_Result_View_Abstract
{	
	/**
	 * Lớp hỗ trợ HTML
	 * @var XPHP_Html
	 */
	protected $html;
	
	/**
	 * Lớp hỗ trợ Form
	 * @var XPHP_Form
	 */
	protected $form;
	
	/**
	 * Lớp hỗ trợ URL
	 * @var XPHP_Url
	 */
	protected $url;
	
	/**
	 * Lớp hỗ trợ cache frontend
	 * @var XPHP_Cache_Frontend
	 */
	protected $cache;
	
	/**
	 * Lớp xử lý section
	 * @var XPHP_Layout_Section
	 */
	protected $section;
	
	/**
	 * Lớp xử lý content
	 * @var XPHP_Layout_Content
	 */
	protected $content;
	
	/**
	 * Lớp hỗ trợ xử lý session
	 * @var XPHP_Session
	 */
	protected $session;
	
	/**
	 * Lớp hỗ trợ xử lý cookie
	 * @var XPHP_Cookie
	 */
	protected $cookie;
	
	/**
	 * Xử lý resource
	 * @var XPHP_Resource
	 */
	protected $resource;
	
	/**
	 * Lớp hỗ trợ xử lý Asset
	 * @var XPHP_Asset
	 */
	protected $asset;
	
	/**
	 * Lớp các Widget hỗ trợ
	 * @var XPHP_Widget
	 */
	protected $widget;

	/**
	 * (non-PHPdoc)
	 * @see XPHP_Action_Result_Interface::ExecuteResult()
	 */
	public function execute()
	{
	    $this->view = new XPHP_View($this->router);
	    
	    $this->_layout = new XPHP_Layout($this->router);
	    
		extract(get_object_vars($this->viewData), EXTR_OVERWRITE);

		//Lớp Helper
		$this->helper = new XPHP_View_Helper();
		
		//Lớp hỗ trợ xử lý Session
		$this->session = XPHP_Session::getInstance();
		
		//Lớp hỗ trợ xử lý Cookie
		$this->cookie = new XPHP_Cookie();
		
		//Lớp hỗ trợ xử lý HTML
		$this->html = new XPHP_Html($this->router, $this);
		
		//Lớp hỗ trợ xử lý Form
		$this->form = new XPHP_Form($this->router, $this);
		
		//Lớp hỗ trợ URL
		$this->url = new XPHP_Url($this->router);
		
		//Lớp hỗ trợ Asset
		$this->asset = new XPHP_Asset($this->url);
		
		//Xử lý resource
		if(XPHP_Registry::isRegistered("DefaultResource"))
			$this->resource = XPHP_Registry::get("DefaultResource");
			
		//Khởi tạo lớp xử lý Section Layout
		$this->section = new XPHP_Layout_Section();
		
		//Khởi tạo lớp xử lý Content Layout
		$this->content = new XPHP_Layout_Content();
		
		//Khởi tạo lớp hỗ trợ cache phía frontend
		$this->cache = new XPHP_Cache_Frontend();
		
		//Khởi tạo lớp hỗ trợ widget
		$this->widget = new XPHP_Widget();
		
		//Load các helper hỗ trợ view
		$this->_loadResultHelper();
		
		/*
		 * Include file View
		 * Nếu có định nghĩa section, content, layout thì load layout
		 * Nếu không thì hiển thị view
		 */
		require $this->view->getDefaultViewPath();
		
		if($this->layout !== NULL)
		{
		    //Xóa toàn bộ các hiển thị từ file view
		    //if(ob_get_contents())
    		    //ob_end_clean();
		    //Thiết lập file layout
			$this->_layout->setLayoutFile($this->layout);
			//Include file layout
			require $this->_layout->getLayoutPath();
		}
	}
}