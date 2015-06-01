<?php
/**
 * Attribute cho phép sử dụng command
 * @author Mr.UBKey
 * @copyright 2010 - 2011
 */

#[Usage(property = true, inherited = true)]
class XPHP_Model_Attribute_Binding_Command
{
    /**
     * Cho phép sử dụng cả insert, update
     * @var boolean
     */
    public $command;

    /**
     * Sử dụng khi insert
     * @var boolean
     */
    public $insert;

    /**
     * Sử dụng khi update
     * @var boolean
     */
    public $update;

    /**
     * Gán tham số truyền vào cho Attribute
     */
    public function init($properties)
    {
        if (isset($properties[0]) && is_bool($properties[0]))
            $this->command = $properties[0];
        if (isset($properties['insert']) && is_bool($properties['insert']))
            $this->insert = $properties['insert'];
        if (isset($properties['update']) && is_bool($properties['update']))
            $this->update = $properties['update'];
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
