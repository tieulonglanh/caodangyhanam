<?php
/**
 * Lớp Abstract của Model Attribute
 * @author Mr.UBKey
 * 
 */

require_once 'XPHP/Attribute/Abstract.php';

#[Usage(class = true, method = true, property = true, inherited = true)]
abstract class XPHP_Model_Attribute_Abstract extends XPHP_Attribute_Abstract
{
	/**
	 * Model
	 * @var XPHP_Model
	 */
	protected $_model;
	
	public function setModel(&$model)
	{
		$this->_model = $model;
	}
	
	public function freeModel()
	{
	    $this->_model = null;
	}
}