<?php
/**
 * @author Mr.UBKey
 * @package XPHP
 * @version Beta
 * @copyright XWEB
 */

require_once 'XPHP/Attribute.php';

class XPHP_Html_ValidationUnobtrusive
{
	/**
	 * Mảng lưu trữ các html attribute
	 * @var array
	 */
	private $_attributes;
	
	/**
	 * Model của View Result
	 * @var XPHP_Model
	 */
	private $_model;
	
	/**
	 * @param XPHP_View_Result $viewResult
	 */
	public function  __construct($viewResult)
	{
		$this->_model = &$viewResult->model;
		$this->_attributes = array();
	}
	
	/**
	 * Tên thuộc tính cần lấy các attribute
	 * @param string $property
	 */
	public function get($property)
	{
		//Data value
		$this->_attributes[$property]["data-val"] = "true";
		
		//Lấy ra các Model Attribute Validation của thuộc tính
		$attributes = XPHP_Attribute::ofProperty($this->_model, $property, 'XPHP_Model_Attribute_Validation_Abstract');
				
		foreach ($attributes as $ann)
		{
			$ruleName = $ann->name;
			$this->$ruleName($property, $ann);
		}
				
		return $this->_attributes[$property];
	}
	
	/**
	 * Phương thức kiểm tra tính bắt buộc
	 * @param string $propertyName Tên thuộc tính
	 * @param XPHP_Model_Attribute_Validation_Required $attribute
	 */
	public function required($propertyName, $attribute)
	{
		$this->_attributes[$propertyName]['data-val-required'] = $attribute->message;		
	}

	/**
	 * Phương thức kiểm tra độ dài tối thiểu của chuỗi
	 * @param string $propertyName Tên thuộc tính
	 * @param XPHP_Model_Attribute_Validation_Minlength $attribute
	 */
	public function minlength($propertyName, $attribute)
	{
		$this->_attributes[$propertyName]['data-val-length'] = $attribute->message;
		$this->_attributes[$propertyName]['data-val-length-min'] = $attribute->minlength;
	}

	/**
	 * Phương thức kiểm tra độ dài tối đa
	 * @param string $propertyName Tên thuộc tính
	 * @param XPHP_Model_Attribute_Validation_Maxlength $attribute
	 */
	public function maxlength($propertyName, $attribute)
	{
		$this->_attributes[$propertyName]['data-val-length'] = $attribute->message;
		$this->_attributes[$propertyName]['data-val-length-max'] = $attribute->maxlength;	
	}
	
	/**
	 * Phương thức kiểm tra khoảng độ dài
	 * @param string $propertyName Tên thuộc tính
	 * @param XPHP_Model_Attribute_Validation_RangeLength $attribute
	 */
	public function rangelength($propertyName, $attribute)
	{
		$this->_attributes[$propertyName]['data-val-length'] = $attribute->message;
		$this->_attributes[$propertyName]['data-val-length-min'] = $attribute->min;
		$this->_attributes[$propertyName]['data-val-length-max'] = $attribute->max;
	}
	
	/**
	 * Phương thức kiểm tra giá trị nhỏ nhất
	 * @param string $propertyName Tên thuộc tính
	 * @param XPHP_Model_Attribute_Validation_Min $attribute
	 */
	public function min($propertyName, $attribute)
	{
		$this->_attributes[$propertyName]['data-val-range'] = $attribute->message;
		$this->_attributes[$propertyName]['data-val-range-min'] = $attribute->min;
	}
	
	/**
	 * Phương thức kiểm tra giá trị lớn nhất
	 * @param string $propertyName Tên thuộc tính
	 * @param XPHP_Model_Attribute_Validation_Max $attribute
	 */
	public function max($propertyName, $attribute)
	{
		$this->_attributes[$propertyName]['data-val-range'] = $attribute->message;
		$this->_attributes[$propertyName]['data-val-range-max'] = $attribute->max;
	}
	
	/**
	 * Phương thức kiểm tra giá trị lớn nhất và nhỏ nhất
	 * @param string $propertyName Tên thuộc tính
	 * @param XPHP_Model_Attribute_Validation_Range $attribute
	 */
	public function range($propertyName, $attribute)
	{
		$this->_attributes[$propertyName]['data-val-range'] = $attribute->message;
		$this->_attributes[$propertyName]['data-val-range-min'] = $attribute->min;
		$this->_attributes[$propertyName]['data-val-range-max'] = $attribute->max;
	}
	
	/**
	 * Phương thức kiểm tra tính chính xác của email
	 * @param string $propertyName Tên thuộc tính
	 * @param XPHP_Model_Attribute_Validation_Email $attribute
	 */
	public function email($propertyName, $attribute)
	{
		$this->_attributes[$propertyName]['data-val-email'] = $attribute->message;
	}
	
	/**
	 * Phương thức kiểm tra tính chính xác của URL
	 * @param string $propertyName Tên thuộc tính
	 * @param XPHP_Model_Attribute_Validation_Url $attribute
	 */
	public function url($propertyName, $attribute)
	{
		$this->_attributes[$propertyName]['data-val-url'] = $attribute->message;
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
}