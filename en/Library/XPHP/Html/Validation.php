<?php
/**
 * @author Mr.UBKey
 * @package XPHP
 * @version Beta
 * @copyright XWEB
 */

require_once 'XPHP/Attribute.php';

class XPHP_Html_Validation
{
	/**
	 * Mảng chứa các thông báo lỗi
	 * @var array
	 */
	private $_messages = array();
	
	/**
	 * Mảng chứa các quy tắc bắt lỗi
	 * @var array
	 */
	private $_rules = array();
	
	/**
	 * Tên form cần valid
	 * @var string
	 */
	private $_formName;
	
	/**
	 * @param XPHP_Model $model
	 */
	public function __construct($model)
	{
		require_once 'XPHP/Reflection.php';
		
		//Lấy toàn bộ danh sách các thuộc tính của Model
		$ref_model = new XPHP_Reflection("XPHP_Model");
		$properties_model = array();
		foreach ($ref_model->getProperties() as $property)
		{
			$properties_model[] = $property->getName();
		}
		//Lấy danh sách các property của lớp thừa kế XPHP_Model
		$ref = new XPHP_Reflection($model);
		foreach ($ref->getProperties() as $property)
		{
			$property_name = $property->getName();
			if(!in_array($property_name, $properties_model))
			{
				//Lấy ra các Model Attribute Validation của thuộc tính
				$attributes = XPHP_Attribute::ofProperty($model, $property_name, 'XPHP_Model_Attribute_Validation_Abstract');
				
				foreach ($attributes as $attr)
				{
					$ruleName = $attr->name;
					$this->$ruleName($property_name, $attr);
				}
			}
		}
	}
	
	/**
	 * Phương thức kiểm tra tính bắt buộc
	 * @param string $propertyName Tên thuộc tính
	 * @param XPHP_Model_Attribute_Validation_Required $attribute
	 */
	public function required($propertyName, $attribute)
	{
		$this->_rules[$propertyName]['required'] = true;		
		$this->_messages[$propertyName]['required'] = $attribute->message; 
	}

	/**
	 * Phương thức kiểm tra độ dài tối thiểu của chuỗi
	 * @param string $propertyName Tên thuộc tính
	 * @param XPHP_Model_Attribute_Validation_Minlength $attribute
	 */
	public function minlength($propertyName, $attribute)
	{
		$this->_rules[$propertyName]['minlength'] = $attribute->minlength;
		$this->_messages[$propertyName]['minlength'] = $attribute->message;
	}

	/**
	 * Phương thức kiểm tra độ dài tối đa
	 * @param string $propertyName Tên thuộc tính
	 * @param XPHP_Model_Attribute_Validation_Maxlength $attribute
	 */
	public function maxlength($propertyName, $attribute)
	{
		$this->_rules[$propertyName]['maxlength'] = $attribute->maxlength;
		$this->_messages[$propertyName]['maxlength'] = $attribute->message;		
	}
	
	/**
	 * Phương thức kiểm tra khoảng độ dài
	 * @param string $propertyName Tên thuộc tính
	 * @param XPHP_Model_Attribute_Validation_RangeLength $attribute
	 */
	public function rangelength($propertyName, $attribute)
	{
		$minlength = (int)$attribute->min;
		$maxlength = (int)$attribute->max;

		$this->_rules[$propertyName]['rangelength'] = array($minlength, $maxlength);
		$this->_messages[$propertyName]['rangelength'] = $attribute->message;
	}
	
	/**
	 * Phương thức kiểm tra giá trị nhỏ nhất
	 * @param string $propertyName Tên thuộc tính
	 * @param XPHP_Model_Attribute_Validation_Min $attribute
	 */
	public function min($propertyName, $attribute)
	{
		$this->_rules[$propertyName]['min'] = $attribute->min;
		$this->_messages[$propertyName]['min'] = $attribute->message;

	}
	
	/**
	 * Phương thức kiểm tra giá trị lớn nhất
	 * @param string $propertyName Tên thuộc tính
	 * @param XPHP_Model_Attribute_Validation_Max $attribute
	 */
	public function max($propertyName, $attribute)
	{
		$this->_rules[$propertyName]['max'] = $attribute->max;
		$this->_messages[$propertyName]['max'] = $attribute->message;
	}
	
	/**
	 * Phương thức kiểm tra giá trị lớn nhất và nhỏ nhất
	 * @param string $propertyName Tên thuộc tính
	 * @param XPHP_Model_Attribute_Validation_Range $attribute
	 */
	public function range($propertyName, $attribute)
	{
		$min = (int)$attribute->min;
		$max = (int)$attribute->max;
		
		$this->_rules[$propertyName]['range'] = array($min, $max);
		$this->_messages[$propertyName]['range'] = $attribute->message;
	}
	
	/**
	 * Phương thức kiểm tra tính chính xác của email
	 * @param string $propertyName Tên thuộc tính
	 * @param XPHP_Model_Attribute_Validation_Email $attribute
	 */
	public function email($propertyName, $attribute)
	{
		$this->_rules[$propertyName]['email'] = true;
		$this->_messages[$propertyName]['email'] = $attribute->message;
	}
	
	/**
	 * Phương thức kiểm tra tính chính xác của URL
	 * @param string $propertyName Tên thuộc tính
	 * @param XPHP_Model_Attribute_Validation_Url $attribute
	 */
	public function url($propertyName, $attribute)
	{
		$this->_rules[$propertyName]['url'] = true;
		$this->_messages[$propertyName]['url'] = $attribute->message;
	}
	
	public function date()
	{}
	
	public function dateISO()
	{}
	
	public function dateDE()
	{}
	
	public function number()
	{}
	
	public function numberDE()
	{}
	
	public function digits()
	{}
	
	public function creditcard()
	{}
	
	public function accept()
	{}
	
	public function equalTo()
	{}
	
	public function getScript()
	{
		$jsonRules = json_encode($this->_rules);
		$jsonMessages = json_encode($this->_messages);
		$script  = "<script type='text/javascript'>" . "\n";
		$script .= "//<![CDATA[" . "\n";
		$script .= "$(document).ready(function(){ $('form";
		if(!empty($this->_formName))
			$script .= "#".$this->_formName;
		$script .= "').validate({rules: $jsonRules, messages: $jsonMessages}) });" . "\n";
		$script .= "//]]>" . "\n";
		$script .= "</script>" . "\n";
		
		return $script;
	}
	
	public function setFormName($formName)
	{
		$this->_formName = $formName;
	}
}