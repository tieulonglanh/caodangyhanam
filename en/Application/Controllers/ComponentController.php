<?php
class ComponentController extends XPHP_Controller
{
	public function headerAction()
	{
		return $this->view();
	}
	
	public function footerAction()
	{
		return $this->view();
	}
}