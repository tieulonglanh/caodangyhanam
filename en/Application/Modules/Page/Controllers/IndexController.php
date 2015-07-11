<?php
class IndexController extends XPHP_Controller
{

    public function indexAction()
    {
        $this->loadLayout('/default');
        $page = new Page();
        $articals = $page->getPages();


        $this->view->articals = $articals;
        return $this->view();
    }

    public function detailAction() {
        $this->loadLayout('/default');
        $page = new Page();
        $artical = $page->getActical($this->params['id']);
        
        $page->changeViewCount($artical->id, $artical->view_count);
        $this->view->detail = $artical;
        return $this->view();
    }
}