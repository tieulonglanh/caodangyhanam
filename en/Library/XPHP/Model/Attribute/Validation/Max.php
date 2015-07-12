<?php
/**
 * Attribute Model Validation bắt giá trị lớn nhất kiểu số
 * @author Mr.UBKey
 *
 */
require_once 'XPHP/Model/Attribute/Validation/Abstract.php';

class XPHP_Model_Attribute_Validation_Max extends XPHP_Model_Attribute_Validation_Abstract
{
	/**
	 * Giá trị lớn nhất
	 * @var int
	 */
	public $max;
    /**
     * Gán tham số truyền vào cho Attribute
     */
    public function init ($properties)
    {
    	//Tên của validation
    	$this->name = "max";
    	
    	if(isset($properties[0]) && is_int($properties[0]))
    		$this->max = $properties[0];
    	else
    		throw new XPHP_Attribute_Exception(
            'XPHP_Model_Attribute_Validation_Max có một tham số truyền vào kiểu int');
        if (isset($properties['message']) && is_string($properties['message']))
            $this->message = $properties['message'];
        //Nếu không truyền vào message sử dụng message mặc định
		else
			$this->message = "{$this->property} có giá trị lớn nhất {$this->max}.";
    }
    /**
     * (non-PHPdoc)
     * @see XPHP_Model_Attribute_Validation_Abstract::validate()
     */
	public function onValidate()
	{
		//Kiểm tra
		$property = $this->property;
		return !((int)$this->_model->$property > $this->max);
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