<?php
class NavComponentController extends XPHP_Controller
{
    public function indexAction($args)
    {
        //Lấy ra menu type
        $type = (int)$args[0];
     
        //Lấy ra danh sách các menu
        $model = new Navigation();
        $menu = $model->getMenuMultiLevel(0, $type);
        
        return $this->view();
    }
    
    public function topAction()
    {
        $model = new Navigation();
        $menu = $model->getMenuMultiLevel(0, 2);
        
        $this->view->menu = $menu;
        return $this->view();
    }
    
    public function headerAction()
    {
        //Lấy ra danh sách các menu
        $model = new Navigation();
        $menu = $model->getMenuMultiLevel(0, 1);
        
//        $menu = $model->getAllMenu();

        $this->view->menu = $menu;
        return $this->view();
    }
    
    public function footerAction()
    {
    	//Lấy ra danh sách các menu
        $model = new Navigation();
        $menu = $model->getMenuMultiLevel(0, 3);
        
        $this->view->menu = $menu;
        return $this->view();
    }
    
    public function sidebarAction()
    {
    	//Lấy ra danh sách các menu
        $model = new Navigation();
        $menu = $model->getMenuMultiLevel(0, 3);
        
        $this->view->menu = $menu;
        return $this->view();
    }
}