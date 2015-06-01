<?php
/**
 * Lớp Base cơ sở của HTML
 * @author Mr.UBKey
 * @package XPHP
 * @version Beta
 * @copyright XWEB
 */
class XPHP_Html_Base
{
	/**
	 * Phân tích mảng thành chuỗi attribute của Html
	 * @param array $arrAttr name => value
	 */
	public static function htmlAttributes($arrAttr)
	{
		$attr = '';
		foreach($arrAttr as $name => $value)
			$attr .= "$name=\"$value\" ";
		return $attr;
	}
}