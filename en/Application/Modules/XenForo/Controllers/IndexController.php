<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of IndexController
 *
 * @author tieulonglanh
 */
class IndexController extends XPHP_Controller{
    //put your code here
    public function staffAction()
    {
        $this->loadLayout('/VNCPCMember');
        
        $xenUserGroupObj = new XFUserGroup();
        $groups = $xenUserGroupObj->getGroups();
        $this->view->groups = $groups;
        
        
        $userObj = new XFUser();
        $groupUsers = array();
        $ids = array();
        foreach($groups as $g){
            $groupUsers[$g->user_group_id] = $userObj->getUsersByGroup($g->user_group_id);
            foreach($groupUsers[$g->user_group_id] as $user){
                $ids[] = $user->user_id;
            }
        }
        $this->view->groupUsers = $groupUsers;
        
        $userFieldValueObj = new XFFieldValue();
        $userInfos = $userFieldValueObj->getInfoByUserId($ids);
        $mixs = array();
        foreach ($ids as $id){
            $mix = array();
            foreach($userInfos as $ui){
                if($ui->user_id == $id){
                    $mix[$ui->field_id] = $ui->field_value;
                }
            }
            $mixs[$id] = $mix;
        }
        $this->view->userInfos = $mixs;
        
        //print_r($mixs); die;
        
        return $this->view();
    }
}

?>
