<?php
/**
 * Lớp hỗ trợ XCache
 * @author Mr.UBKey 
 */

require_once 'XPHP/Cache/Abstract.php';

class XPHP_Cache_APC extends XPHP_Cache_Abstract
{
	public function __construct()
    {
    	//Load cấu hình trong file config với node cache > xcache
		//require_once 'XPHP/Config.php';
		//XPHP_Config::load($this, 'cache > xcache');
    }

	/**
	 * __set Gán giá trị cache
	 *
	 * @param mixed $name
	 * @param mixed $value
	 * @access public
	 * @return void
	 */
	public function __set($name, $value)
	{
		if(!empty($this->time))
		{
			if(apc_exists($name))
				apc_store($name, $value, $this->time);
			else
				apc_add($name, $value, $this->time);
		}
		else
		{
			if(apc_exists($name))
				apc_store($name, $value);
			else
				apc_add($name, $value);
		}
	}

	/**
	 * __get Lấy giá trị cache
	 *
	 * @param mixed $name
	 * @access public
	 * @return void
	 */
	public function __get($name)
	{
		return apc_fetch($name);
	}

	/**
	 * __isset Kiểm tra sự tồn tại của cache
	 *
	 * @param mixed $name
	 * @access public
	 * @return bool
	 */
	public function __isset($name)
	{
		return apc_exists($name);
	}

	/**
	 * __unset Xoá bỏ giá trị
	 *
	 * @param mixed $name
	 * @access public
	 * @return void
	 */
	public function __unset($name)
	{
		apc_delete($name);
	}
}