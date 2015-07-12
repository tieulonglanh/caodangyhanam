<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of XFResource
 *
 * @author tieulonglanh
 */
#[Adapter('guitar_xf')]
#[Table('resource')]
#[PrimaryKey('resource_id')]
class XFResource extends XPHP_Model{
    //put your code here
    
    public function getResourceLimitOffsetByCatId($limit, $offset, $cid)
    {
        return $this->db->where('resource_category_id', $cid)
                ->limit($limit)
                ->offset($offset)
                ->order_by('resource_id','desc')
                ->get()->result();
    }
    
    public function getResourceLimitOffset($limit, $offset, $ids)
    {
        return $this->db->where_in('resource_category_id',$ids)
                ->limit($limit)
                ->offset($offset)
                ->order_by('resource_id','desc')
                ->get()->result();
    }
    
    public function getCountDocument($ids) {
        return $this->db->where_in('resource_category_id',$ids)->count_all_results();
    }
}

?>
