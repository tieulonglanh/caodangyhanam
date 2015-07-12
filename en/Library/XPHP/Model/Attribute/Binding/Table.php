<?php
/**
 * Attribute gán tên bảng vào Model
 * @author Mr.UBKey
 * @copyright 2010 - 2011
 */

require_once 'XPHP/Model/Attribute/Binding/Abstract.php';

class XPHP_Model_Attribute_Binding_Table extends XPHP_Model_Attribute_Binding_Abstract
{
	/**
	 * Tên bảng
	 * @var string
	 */
    private $_name;
    /**
     * Gán tham số truyền vào cho Attribute
     */
    public function init ($properties)
    {
        if (isset($properties[0]) && is_string($properties[0]))
            $this->_name = $properties[0];
        else {
            throw new XPHP_Attribute_Exception(
            'XPHP_Model_Attribute_Binding_Table bắt buộc tham số truyền vào là một chuỗi');
        }
    }
    /**
     * Thực thi Attribute Model
     */
    public function onBinding ()
    {
        $this->_model->_name = $this->_name;
        $this->_model->db->setTable($this->_name);
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