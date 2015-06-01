<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of XFResourceCategory
 *
 * @author tieulonglanh
 */
#[Adapter('guitar_xf')]
#[Table('resource_category')]
#[PrimaryKey('resource_category_id')]
class XFResourceCategory extends XPHP_Model{
    //put your code here
    
    
    public function getCategoryByParent($pid)
    {
        return $this->db->where('resource_category.parent_category_id',$pid)
                ->order_by('resource_category.display_order','desc')
                ->get()->result();
    }
    
}

?>
