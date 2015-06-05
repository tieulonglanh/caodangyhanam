<?php

class IndexController extends XPHP_Controller {

    public function indexAction() {
        $this->loadLayout('default');
        
        $artical = new Artical();
        $newestArtical = $artical->getArtical(8);
        
        $this->view->newestArtical = $newestArtical;
        
        $video = new Video();
        $latestVideos = $video->getNewVideos(3);
        $this->view->latestVideos = $latestVideos;
        
        return $this->view();
    }

    public function searchAction() {
        $this->loadLayout('default');
        
        return $this->view();
    }

    public function studentAction() {
        $this->loadLayout('default');

        return $this->view();
    }
}