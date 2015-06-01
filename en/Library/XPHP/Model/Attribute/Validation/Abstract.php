<?php
/**
 * Lớp Abstract của Model Validation 
 * @author Mr.UBKey
 * @usage('property'=>true, 'inherited'=>true)
 */

require_once 'XPHP/Model/Attribute/Abstract.php';

abstract class XPHP_Model_Attribute_Validation_Abstract extends XPHP_Model_Attribute_Abstract
{
	/**
	 * Tên của Rule trong JQuery Validation
	 * @var string
	 */
	public $name;
	/**
	 * Thông báo lỗi
	 * @var string
	 */
	public $message;
	/**
	 * Tên thuộc tính
	 * @var string
	 */
	protected $property;	
	/**
	 * Thiết lập tên thuộc tính
	 * @param string $propertyName
	 */
	public function setProperty($propertyName)
	{
		$this->property = $propertyName;
	}
	
	abstract function onValidate();
}