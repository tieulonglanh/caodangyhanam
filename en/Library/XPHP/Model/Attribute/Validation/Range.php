<?php
/**
 * Attribute Model Validation kiểm tra giá trị lớn nhất và nhỏ nhất
 * @author Mr.UBKey
 *
 */
require_once 'XPHP/Model/Attribute/Validation/Abstract.php';

class XPHP_Model_Attribute_Validation_Range extends XPHP_Model_Attribute_Validation_Abstract
{
	/**
	 * Giá trị tối thiểu
	 * @var int
	 */
	public $min;
	/**
	 * Giá trị tối đa
	 * @var int
	 */
	public $max;
    /**
     * Gán tham số truyền vào cho Attribute
     */
    public function init ($properties)
    {
    	//Tên của validation
    	$this->name = "range";
    	
    	if(isset($properties['min']) && is_int($properties['min'])
    		&& isset($properties['max']) && is_int($properties['max'])
    	)
    	{
    		$this->min = $properties['min'];
    		$this->max = $properties['max'];
    	}	
    	else
    		throw new XPHP_Attribute_Exception(
            'XPHP_Model_Attribute_Validation_Range
             phải truyền vào min(int) và max(int)');
        if (isset($properties['message']) && is_string($properties['message']))
            $this->message = $properties['message'];
        //Nếu không truyền vào message sử dụng message mặc định
		else
			$this->message = "{$this->property} có giá trị nhỏ nhất {$this->min} và có lớn nhất {$this->max}.";
    }
    /**
     * (non-PHPdoc)
     * @see XPHP_Model_Attribute_Validation_Abstract::validate()
     */
	public function onValidate()
	{
		//Kiểm tra
		$property = $this->property;
		
		return !((int)$this->_model->$property > $this->max 
		|| (int)$this->_model->$property < $this->min);
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