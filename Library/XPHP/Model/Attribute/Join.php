<?php
/**
 * Attribute Join 
 * @author Mr.UBKey
 * @copyright 2010 - 2011
 */

require_once 'XPHP/Model/Attribute/Abstract.php';

class XPHP_Model_Attribute_Join extends XPHP_Model_Attribute_Abstract
{
	/**
	 * Tên bảng
	 * @var string
	 */
    public $table;
    
    /**
     * Tên trường
     * @var string
     */
    public $field;
    
    /**
     * Mối quan hệ
     * @var string
     */
    public $relation;
    /**
     * Gán tham số truyền vào cho Attribute
     */
    public function init ($properties)
    {
        if (isset($properties['table']) || isset($properties[0]))
            $this->table = $properties['table'];
        if (isset($properties['field']))
            $this->field = $properties['field'];
        else 
            $this->field = 'id';
        if (isset($properties['relation']))
            $this->relation = $properties['relation'];
        else 
            $this->relation = 'one-one';
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