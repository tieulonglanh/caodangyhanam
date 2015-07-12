<?php
/**
 * Lớp Abstract của Model Binding gắn kết Model với CSDL
 * @author Mr.UBKey
 */

/**
 * @see XPHP_Model_Attribute_Abstract
 */
require_once 'XPHP/Model/Attribute/Abstract.php';

abstract class XPHP_Model_Attribute_Binding_Abstract extends XPHP_Model_Attribute_Abstract
{
	abstract function onBinding();
}