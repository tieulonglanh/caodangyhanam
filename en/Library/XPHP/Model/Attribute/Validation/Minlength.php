<?php
/**
 * Attribute Model Validation bắt kí tự tối thiểu của chuỗi
 * @author Mr.UBKey
 *
 */
require_once 'XPHP/Model/Attribute/Validation/Abstract.php';

class XPHP_Model_Attribute_Validation_Minlength extends XPHP_Model_Attribute_Validation_Abstract
{
	/**
	 * Chiều dài tối thiểu của chuỗi
	 * @var int
	 */
	public $minlength;
    /**
     * Gán tham số truyền vào cho Attribute
     */
    public function init ($properties)
    {
    	//Tên của validation
    	$this->name = "minlength";
    	
    	if(isset($properties[0]) && is_int($properties[0]))
    		$this->minlength = $properties[0];
    	else
    		throw new XPHP_Attribute_Exception(
            'XPHP_Model_Attribute_Validation_Minlength có một tham số truyền vào kiểu int');
        if (isset($properties['message']) && is_string($properties['message']))
            $this->message = $properties['message'];
        //Nếu không truyền vào message sử dụng message mặc định
		else
			$this->message = "{$this->property} có tối thiểu {$this->minlength} kí tự.";
    }
    /**
     * (non-PHPdoc)
     * @see XPHP_Model_Attribute_Validation_Abstract::validate()
     */
	public function onValidate()
	{
		//Kiểm tra
		$property = $this->property;
		return !(strlen($this->_model->$property) < $this->minlength);
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