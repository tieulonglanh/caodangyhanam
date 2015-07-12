<?php
/**
 * Lớp hỗ trợ XPHP_Config_Adapter load tệp tin cấu hình xml
 * @author Mr.UBKey
 */

require_once 'XPHP/Config/Adapter/Interface.php';

class XPHP_Config_Adapter_Xml implements XPHP_Config_Adapter_Interface
{
	public static function parse($file)
	{
		$xml = new XPHP_Xml($file);
		return $xml->getArray();
	}
}