<?php
/**
 * Lớp hỗ trợ sử dụng jQuery
 * @author Mr.UBKey
 *
 */

require_once 'XPHP/Helper/jQuery/Assets.php';

class XPHP_Helper_jQuery
{
  	public $string;
  
  	public function __contruct($id = null)
  	{
    	if ( is_null($id) ) $this->string = '$';
    	else $this->string = '$('.$id.')';
    	return $this;
  	}
  
  	public function __invoke ($id = null)
  	{
    	return $this->__contruct($id);
  	}
  
  	public function __call($f, $p)
  	{
	    if(count($p) == 1 && is_array($p[0]))
      		$params = '{' . XPHP_Helper_jQuery_Assets::serializeAssocArray($p[0], '', ': ', '', ',') . '}';
    	else
      		$params = XPHP_Helper_jQuery_Assets::serializeArray($p, "'", ',');
    	$this->string .= ".$f(".$params.')';
    	return $this;
  	}
  
  	public function __toString()
  	{
    	$str = $this->string. ';';
    	$this->string = '';
		return $str;
	}
}