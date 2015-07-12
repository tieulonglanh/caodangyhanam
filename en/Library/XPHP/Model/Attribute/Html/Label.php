<?php
/**
 * Attribute Model Html lưu trữ Label của thuộc tính.
 * @author Mr.UBKey
 *
 */

class XPHP_Model_Attribute_Html_Label extends XPHP_Model_Attribute_Html_Abstract
{
	/**
	 * Chuỗi Label
	 * @var string
	 */
	public $text;
	
    /**
     * Gán tham số truyền vào cho Attribute
     */
    public function init ($properties)
    {
    	if (isset($properties[0]) && is_string($properties[0]))
    		$this->text = $properties[0];
    	else
    		throw new XPHP_Attribute_Exception(
            'XPHP_Model_Attribute_Html_Label có một tham số truyền vào là tên hiển thị của thuộc tính kiểu chuỗi.');
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