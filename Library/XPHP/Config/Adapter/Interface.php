<?php
/**
 * Interface của các lớp XPHP_Config_Adapter_XXXXX 
 * Hỗ trợ XPHP_Config_Adapter đọc các dạng fle
 * @author Mr.UBKey
 *
 */
interface  XPHP_Config_Adapter_Interface
{
	public static function parse($file);
}