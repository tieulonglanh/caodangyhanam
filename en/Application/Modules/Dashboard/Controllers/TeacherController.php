<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TeacherController
 *
 * @author Tieu Long Lanh
 */
class TeacherController extends XPHP_Controller{
    //put your code here
    public function _init()
	{
		//Mặc định load layout XAdmin trong ControlPanel cho toàn bộ các action
		$this->loadLayout("ControlPanel/XTeacher");
	}
	
    #[TeacherAuthorize]
    public function indexAction()
    {
        return $this->view();
    }
}

?>
