<?php
class IndexController extends XPHP_Controller
{
	public function _init()
	{
		//Load layout mặc định cho các Action trong Controller
		$this->loadLayout('XAdmin');
	}
	
	public function indexAction()
	{
		if (!isset($this->session->user->id))
			return $this->redirect('login', 'ControlPanel', 'Auth');
		else
			return $this->view();
	}
}