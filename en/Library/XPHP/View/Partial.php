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
 * @version		$Id: Partial.php 20109 2011-22-08 02:05:09 Mr.UBKey $
 */
/**
 * XPHP_View_Partial Lớp thành phần của view
 *
 * @category	XPHP
 * @package		XPHP_View
 * @author		Mr.UBKey
 * @link		http://xphp.xweb.vn/user_guide/xphp_view_partial.html
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
class XPHP_View_Partial
{
    /**
     * File partial
     * @var string
     */
    public $file;
    
    /**
     * Dữ liệu truyền sang partial
     * @var stdObject
     */
    public $data;
    
    /**
     * Model của partial
     * @var XPHP_Model
     */
    public $model;
    
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
     * Router của hệ thống
     * @var XPHP_Router
     */
    public $router;
    
    /**
     * Render partial
     */
    public function render()
    {
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

    	//Khởi tạo lớp hỗ trợ widget
    	$this->widget = new XPHP_Widget();
    	
    	//Xử lý resource
    	if(XPHP_Registry::isRegistered("DefaultResource"))
    		$this->resource = XPHP_Registry::get("DefaultResource");
    	
        //Extract dữ liệu
        if(is_object($this->data))
        	extract(get_object_vars($this->data), EXTR_OVERWRITE);
        else
        	extract($this->data, EXTR_OVERWRITE);
        //Include file
        include $this->file;
    }
}