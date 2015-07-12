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
#[Table('attachment_data')]
#[PrimaryKey('data_id')]
class XFAttachmentData extends XPHP_Model{
    //put your code here
    
    
    public function getAttachmentDataInfos($ids)
    {
        return $this->db->where_in('data_id',$ids)
                ->get()->result();
    }
    
}

?>
