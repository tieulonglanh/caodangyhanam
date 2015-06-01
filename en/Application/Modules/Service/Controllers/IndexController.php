<?php
class IndexController extends XPHP_Controller
{

    public function indexAction()
    {
        $this->loadLayout('/VNCPCOther');
        $page = new Service();
        $articals = $page->getPages();

        $page->changeViewCount($articals[0]->id, $articals[0]->view_count);

        $this->view->articals = $articals;
        return $this->view();
    }

    public function loadAction() {
        $page = new Service();
        $artical = $page->getActical($this->params[0]);
        $page->changeViewCount($artical->id, $artical->view_count);
        $this->view->artical = $artical;
        return $this->view();
    }
}