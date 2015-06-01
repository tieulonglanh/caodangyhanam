<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of StudentController
 *
 * @author Tieu Long Lanh
 */
class StudentController extends XPHP_Controller{
    //put your code here
    public function _init()
	{
		//Mặc định load layout XAdmin trong ControlPanel cho toàn bộ các action
		$this->loadLayout("ControlPanel/XStudent");
	}
	
    #[StudentAuthorize]
    public function indexAction()
    {
        return $this->view();
    }
}

?>
