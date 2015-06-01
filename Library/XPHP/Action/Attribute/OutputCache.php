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
 * @version		$Id: OutputCache.php 20109 2011-22-08 02:05:09 Mr.UBKey $
 */
require_once 'XPHP/Action/Attribute/Abstract.php';
require_once 'XPHP/Cache/Output.php';
/**
 * Lớp Action Attribute OutputCache dùng để cache output cho Action
 * @package		XPHP_Action
 * @subpackage  XPHP_Action_Attribute
 * @author		XWEB Dev Team
 */
class XPHP_Action_Attribute_OutputCache extends XPHP_Action_Attribute_Abstract
{
    /**
     * Thời gian cache
     * @var string
     */
    private $_time;
    /**
     * Sử dụng nén khi cache
     * @var bool
     */
    private $_compresses;
    /**
     * Prefix của file cache
     * @var string
     */
    private $_prefix;
    /**
     * Đường dẫn tới thư mục chứa cache
     * @var string
     */
    private $_path;
    /**
     * Gán tham số truyền vào cho Attribute
     */
    public function init ($properties)
    {
        if (isset($properties['time']))
            $this->_time = $properties['time'];
        if (isset($properties['compresses']))
            $this->_compresses = $properties['compresses'];
        if (isset($properties['prefix']))
            $this->_prefix = $properties['prefix'];
        if (isset($properties['path']))
            $this->_path = $properties['path'];
    }
    /**
     * Sự kiện này được thực thi khi trước khi action được thực thi
     */
    public function onActionExecute ()
    {
        //Khởi tạo lớp OuputCache
        $cacheOutput = XPHP_Cache_Output::getInstance($this->_time, 
                                                      $this->_compresses, 
                                                      $this->_prefix, 
                                                      $this->_path);
        //Gán router hệ thống để output cache lấy uri
        $cacheOutput->setRouter($this->controller->getRouter());
        //Dừng xử lý nếu tìm thấy file cache và valid
        if($cacheOutput->displayCache(false))
        {
            //free controller
            $this->freeController();
            exit();
        }
        //Mở output buffering và tạo file cache
        ob_start('XPHP_Cache_Output::cache');
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