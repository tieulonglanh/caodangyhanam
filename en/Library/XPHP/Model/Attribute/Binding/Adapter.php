<?php
/**
 * Attribute thiết lập adapter truy cập cơ sở dữ liệu
 * @author Mr.UBKey
 * @copyright 2010 - 2011
 */

require_once 'XPHP/Model/Attribute/Binding/Abstract.php';

class XPHP_Model_Attribute_Binding_Adapter extends XPHP_Model_Attribute_Binding_Abstract
{
	/**
	 * Tên adapter
	 * @var string
	 */
    private $_adapter;
    /**
     * Gán tham số truyền vào cho Attribute
     */
    public function init ($properties)
    {
        if (isset($properties[0]) && is_string($properties[0]))
            $this->_adapter = $properties[0];
        else {
            throw new XPHP_Attribute_Exception(
            'XPHP_Model_Attribute_Binding_Adapter bắt buộc tham số truyền vào kiểu chuỗi. 
            Tên Adapter sử dụng để kết nối tới CSDL');
        }
    }
    /**
     * Thực thi Attribute Model
     */
    public function onBinding ()
    {
    	//Lấy adapter từ trong registry với key prefix là Db_
		require_once 'XPHP/Registry.php';
		$adapter = XPHP_Registry::get("Db_{$this->_adapter}");
		$this->_model->setAdapter($adapter);
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