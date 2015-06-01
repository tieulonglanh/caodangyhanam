<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of XFFieldValue
 *
 * @author tieulonglanh
 */
#[Adapter('guitar_xf')]
#[Table('user_field_value')]
#[PrimaryKey('user_id')]
class XFFieldValue extends XPHP_Model
{
    public $user_id;

    public $field_id;

    public $field_value;
    
    public function getInfoByUserId($ids)
    {
        $result = $this->db->where_in('user_field_value.user_id', $ids)
                ->get()->result();
        return $result;
    }
    
}

?>
