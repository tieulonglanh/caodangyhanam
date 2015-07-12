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
#[Table('attachment')]
#[PrimaryKey('attachment_id')]
class XFAttachment extends XPHP_Model{
    //put your code here
    
    
    public function getAttachmentResourceDataInfos($ids)
    {
        return $this->db->where('content_type','resource_version')
                ->where_in('content_id',$ids)                
                ->get()->result();
    }
    
}

?>
