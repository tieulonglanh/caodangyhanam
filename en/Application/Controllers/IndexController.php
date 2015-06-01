<?php

class IndexController extends XPHP_Controller {

    public function indexAction() {
        $this->loadLayout('VNCPC');

        $projectModel = new Artical();
        $projects = $projectModel->getArtical(8);

        $projectArray = array();
        $point = 0;
        for($i=0; $i<count($projects); $i++) {
            if($i % 2 ==0)  {
                $projectArray[($i/2)][$point] = $projects[$i];
                $point = 1;
            } else {
                $projectArray[($i-1)/2][$point] = $projects[$i];
                $point = 0;
            }
        }

        $this->view->projects = $projectArray;

        return $this->view();
    }

    public function searchAction() {
        $this->loadLayout('VNCPCLayout');
        
        return $this->view();
    }

    public function contactAction() {
        $this->loadLayout('PHPDay');

        if (isset($this->params['submit'])) {
            
        }

        $artical = new Artical();
        $news = $artical->getLimitArticals(6, 2);
        $this->view->news = $news;

        return $this->view();
    }
    
    public function aboutAction() {
        $this->loadLayout('VNCPCOther');
        
        return $this->view();
    }
    
    public function newsAction() {
        $this->loadLayout('VNCPCOther');
        
        return $this->view();
    }

}