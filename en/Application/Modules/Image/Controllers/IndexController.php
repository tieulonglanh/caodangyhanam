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

        $pagesize = 8;
        $this->view->limit = $pagesize;

        if (!isset($this->params['page']))
            $page = 1;
        else
            $page = $this->params['page'];
        $this->view->page = $page;
        $limit = $pagesize;
        $offset = ($page - 1) * $pagesize;
        
        $albumObj = new ImageCategory();
        $albums = $albumObj->getLimitOffsetImageAlbums($limit, $offset);
        $this->view->albums = $albums;        

        return $this->view();
    }
    
    public function detailAction()
    {        
        $this->loadLayout('/VNCPCLayout');
        
        if (isset($this->params['id']))
            $id = $this->params['id'];
        else
            $id = $this->params[0];
        
        
        $pagesize = 8;
        $this->view->limit = $pagesize;

        if (!isset($this->params['page']))
            $page = 1;
        else
            $page = $this->params['page'];
        $this->view->page = $page;
        $limit = $pagesize;
        $offset = ($page - 1) * $pagesize;
        
        $albumObj = new ImageCategory();
        $albumInfo = $albumObj->getAlbumById($id);
        $this->view->albumInfo = $albumInfo[0];
        
        $imageObj = new Image();
        $albumImages = $imageObj->getImagesByAlbum($id, $limit, $offset);
        $this->view->albumImages = $albumImages;
        
        $imageC = new ImageCategory($id);
        $imageC->view++;
        $imageC->update();
        
        return $this->view();
        
    }
    
    public function downloadAction()
    {
        $file = base64_decode($this->params[0]);  
        $name = base64_decode($this->params[1]);
        $id = base64_decode($this->params[2]);
        $imageD = new Image($id);
        $imageD->download++;
        $imageD->update();
        header("Content-type: application/octet-stream");
        header("Content-disposition: attachment;filename=$name.jpg");
        echo file_get_contents($file);
        die;
    }

}

?>
