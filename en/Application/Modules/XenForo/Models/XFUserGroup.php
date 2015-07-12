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
#[Table('user_group')]
#[PrimaryKey('user_group_id')]
class XFUserGroup extends XPHP_Model
{
    public $user_group_id;

    public $title;

    public $display_style_priority;
    
    public $username_css;
    
    public $user_title;
    
    public $banner_css_class;
    
    public $banner_text;
    
    public function getGroups(){
        return $this->db->where('user_group.display_style_priority > ', 0)
                ->where('user_group.display_style_priority < ', 100)
                ->order_by('user_group.display_style_priority','desc')
                ->get()
                ->result();
    }
    
}

?>
