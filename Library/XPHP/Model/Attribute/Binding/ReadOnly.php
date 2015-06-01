<?php
/**
 * Attribute đặt cấp độ truy cập CSDL của Model là ReadOnly
 * @author Mr.UBKey
 * @copyright 2010 - 2011
 */

require_once 'XPHP/Model/Attribute/Binding/Abstract.php';

class XPHP_Model_Attribute_Binding_ReadOnly extends XPHP_Model_Attribute_Binding_Abstract
{
	/**
	 * Đánh dấu ReadOnly
	 * @var boolean
	 */
    private $_readOnly;
    /**
     * Gán tham số truyền vào cho Attribute
     */
    public function init ($properties)
    {
        if (isset($properties[0]) && is_bool($properties[0]))
            $this->_readOnly = $properties[0];
        else {
            throw new XPHP_Attribute_Exception(
            'XPHP_Model_Attribute_Binding_ReadOnly bắt buộc tham số truyền vào kiểu boolean (true | false)');
        }
    }
    /**
     * Thực thi Attribute Model
     */
    public function onBinding ()
    {
        $this->_model->_readOnly = $this->_readOnly;
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