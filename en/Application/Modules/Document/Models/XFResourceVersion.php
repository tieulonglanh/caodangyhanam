<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of XFResourceVersion
 *
 * @author tieulonglanh
 */
#[Adapter('guitar_xf')]
#[Table('resource_version')]
#[PrimaryKey('resource_version_id')]
class XFResourceVersion extends XPHP_Model{
    //put your code here
    public function getResourceLimitOffset($ids)
    {
        return $this->db->where_in('resource_id',$ids)
                ->order_by('resource_id','desc')
                ->get()->result();
    }
    
    public function getResourceVersionByResourceIds($id){
        return $this->db->where_in('resource_id',$id)
                ->get()->result();
    }
}

?>
