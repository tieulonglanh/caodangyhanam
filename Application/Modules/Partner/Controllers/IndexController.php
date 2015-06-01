<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ComponentController
 *
 * @author longnh
 */
class IndexController extends XPHP_Controller {

    public function categoryAction() {
                
        $this->loadLayout('/VNCPCLayout');

        $pagesize = 4;
        $this->view->limit = $pagesize;

        if (!isset($this->params['page']))
            $page = 1;
        else
            $page = $this->params['page'];
        
        $this->view->page = $page;
        $limit = $pagesize;
        $offset = ($page - 1) * $pagesize;
        
        $albumObj = new Partner();
        $albums = $albumObj->getLimitOffsetImageAlbums($limit, $offset);
        $this->view->albums = $albums;    

        return $this->view();
    }
    
}

?>
