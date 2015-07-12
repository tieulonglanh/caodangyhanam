<?php
class XPHP_Helper_jQuery_Assets
{
	public static function low($str){return strtolower($str);}
	public static function up($str){return strtoupper($str); }
	public static function rep($search, $replace, $subject){return str_replace($search, $replace, $subject);}
	public static function len($str){ return strlen($str);}
	public static function pad($s,$padS,$len,$padType=STR_PAD_BOTH){return str_pad($s, self::len($s) + $len, $padS, $padType);}
	public static function stripTrailingChars($str,$count){return substr($str, 0, self::len($str)-$count);}
	
	public static function camelCaseGetParts($str, $count=0)
	{
		$parts			= 	array();
		$partIndex		=	1;
		$len			=	self::len($str);
		
		for($i=0;$i<$len;$i++)
		{
			$char	= $str[$i];
			if ($char === self::up($char))$partIndex++;
			if($partIndex-1 == $count-1 && $count != 0)
			{
				$parts[$partIndex-1] = substr($str, $i, self::len($str));
				return $parts;
			}
			$parts[$partIndex-1] .= $char;
		}
		return $parts;
		
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $array
	 * @param unknown_type $keyWrap
	 * @param unknown_type $betweenOp
	 * @param unknown_type $valueWrap
	 * @param unknown_type $sep
	 */
	public static function serializeAssocArray($array, $keyWrap,$betweenOp,$valueWrap,$sep)
	{
		$str	= "";
		foreach ($array as $k=>$v)
		{
		 if ( is_array($v))
	    $val = '{' . self::serializeAssocArray($v,$keyWrap,$betweenOp,$valueWrap,$sep) . '}';
		 else $val = $v;
			$str .= "{$keyWrap}{$k}{$keyWrap}{$betweenOp}{$valueWrap}{$val}{$valueWrap}{$sep}";
		}
		return self::stripTrailingChars($str,1);
	}

	public static function serializeArray($array, $valueWrap,$sep)
	{
		$str	= "";
		foreach ( $array as $k=>$v)
			$str .= "{$valueWrap}{$v}{$valueWrap}{$sep}";
		return self::stripTrailingChars($str,1);
	}
}