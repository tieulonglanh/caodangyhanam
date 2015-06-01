<?php
class ControlPanelController extends XPHP_Controller
{
	public function _init()
	{
		//Mặc định load layout XAdmin trong ControlPanel cho toàn bộ các action
		$this->loadLayout("ControlPanel/XAdmin");
	}
	
    #[Authorize]
    public function indexAction()
    {
        return $this->view();
    }
}