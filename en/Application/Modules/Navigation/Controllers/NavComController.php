<?php
class NavComController extends XPHP_Controller
{
    public function controlPanelAction()
    {
        //Get navigation list assign to view
		$navModel = new NavigationControlPanel();
		$navList = $navModel->getNavList();
		$this->view->navList = $navList;
		return $this->view();
    }
    
    public function teacherAction()
    {
        $navModel = new NavigationTeacher();
	$navList = $navModel->getNavList();
	$this->view->navList = $navList;
        return $this->view();
    }
    
    public function studentAction()
    {
        $navModel = new NavigationStudent();
	$navList = $navModel->getNavList();
	$this->view->navList = $navList;
        return $this->view();
    }
}