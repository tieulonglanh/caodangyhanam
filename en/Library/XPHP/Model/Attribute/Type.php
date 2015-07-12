<?php
/**
 * XPHP_Model_Attribute_Abstract
 * @see XPHP_Model_Attribute_Abstract
 */
require_once 'XPHP/Model/Attribute/Abstract.php';
/**
 * XPHP_Model_Attribute_Type
 * @author Mr.UBKey
 */
#[Usage(property = true, inherited = true)]
class XPHP_Model_Attribute_Type extends XPHP_Model_Attribute_Abstract
{
    /**
     * Kiểu dữ liệu
     * @var string
     */
    public $type;
    
    /**
     * Gán tham số truyền vào cho Attribute
     */
    public function init($properties)
    {
        if (isset($properties[0]) && is_string($properties[0]))
    		$this->type = $properties[0];
    	else
    		throw new XPHP_Attribute_Exception(
            'XPHP_Model_Attribute_Type có một tham số truyền vào là kiểu dữ liệu.');
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