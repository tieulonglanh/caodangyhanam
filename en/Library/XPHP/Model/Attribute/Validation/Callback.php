<?php
/**
 * Attribute Model Validation kiểm tra bằng callback
 * @author Mr.UBKey
 *
 */
require_once 'XPHP/Model/Attribute/Validation/Abstract.php';

class XPHP_Model_Attribute_Validation_Callback extends XPHP_Model_Attribute_Validation_Abstract
{
	/**
	 * Tên callback
	 * @var string
	 */
	public $callback;
	
    /**
     * Gán tham số truyền vào cho Attribute
     */
    public function init ($properties)
    {
    	//Tên của validation
    	$this->name = "callback";
    	
    	if (isset($properties[0]) && is_string($properties[0]))
    		$this->callback = $properties[0];
    	else
    		throw new XPHP_Attribute_Exception(
            'XPHP_Model_Attribute_Validation_Callback có một tham số truyền vào là tên callback kiểu chuỗi');
    		
        if (isset($properties['message']) && is_string($properties['message']))
            $this->message = $properties['message'];
    }
    /**
     * (non-PHPdoc)
     * @see XPHP_Model_Attribute_Validation_Abstract::validate()
     */
	public function onValidate()
	{
		//Return
		$result = false;
		
		//Kiểm tra
		$property = $this->property;
		$callback = $this->callback;
		
		if(!method_exists($this->_model, $callback))
				throw new XPHP_Attribute_Exception(
            "Không tìm thấy {$callback}() trong " . get_class($this->_model) . ".");
				
		$callbackResult = $this->_model->$callback($this->_model->$property);
		
		//Kiểm tra các kiểu của result
		if(is_bool($callbackResult))
			$result = $callbackResult;
		else if(is_array($callbackResult))
		{
			if(!isset($callbackResult[0]) || !is_bool($callbackResult[0]))
				throw new XPHP_Attribute_Exception(
            "Kết quả trả về của {$callback}() không đúng định dạng.");
			else 
			{
				$result = $callbackResult[0];
			}
			//Trong callback có trả về thông báo lỗi
			if(isset($callbackResult['message']))
				$this->message = $callbackResult['message'];
		}
			
		//Nếu không truyền vào message sử dụng message mặc định
		if(empty($this->message))
			$this->message = "{$this->property} không đúng định dạng.";
			
		return $result;
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