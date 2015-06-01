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
 * @package		XPHP_Cache
 * @author		XWEB Dev Team
 * @copyright	Copyright (c) 2010-2011 XWEB. (http://xweb.vn)
 * @license		http://xphp.xweb.vn/license.html     GNU GPL License
 * @version		$Id: Cache.php 201012 2011-22-08 02:05:09 Mr.UBKey $
 */
/**
 * Lớp trung gian sử dụng Cache sau khi tạo một thể hiện bạn có thể 
 * load lớp Cache nếu không load mặc định hệ thống sẽ sử dụng XPHP_Cache_Simple
 *
 * @category	XPHP
 * @package		XPHP_Cache
 * @author		Mr.UBKey
 * @link		http://xphp.xweb.vn/user_guide/xphp_cache.html
 */
class XPHP_Cache extends XPHP_Cache_Abstract
{
    /**
     * Lớp cache
     * @var string
     */
    public $class;
    /**
     * Thể hiện của lớp cache
     * @var XPHP_Cache_Abstract
     */
    private $cacheObject;
    
    /**
     * Khởi tạo
     * @param string $class Tên lớp cache | Mặc định load XPHP_Cache_Simple
     */
    public function __construct ($class = NULL)
    {
        //Load cấu hình
        XPHP_Config::load($this, "cacheloader");
        if($class !== NULL)
 	       $this->class = $class; 
        else
        {
        	if(function_exists('xcache_set'))
        		$this->class = "XPHP_Cache_XCache";
        	else if(function_exists('apc_add'))	
        		$this->class = "XPHP_Cache_APC";
        	else
        		$this->class = "XPHP_Cache_Simple";
        }      
        $this->load($this->class);
    }
    /**
     * Load thư viện cache
     * @param string $cacheClass Tên lớp giao tiếp với thư viện cache 
     */
    public function load ($class)
    {
        $this->cacheObject = new $class($this->getTime(), 
        $this->compresses, $this->getPrefix(), $this->getPath());
    }
    public function __isset ($key)
    {
        return isset($this->cacheObject->$key);
    }
    public function __unset ($key)
    {
        unset($this->cacheObject->$key);
    }
    public function __get ($key)
    {
        return $this->cacheObject->$key;
    }
    public function __set ($key, $value)
    {
        $this->cacheObject->$key = $value;
    }
}