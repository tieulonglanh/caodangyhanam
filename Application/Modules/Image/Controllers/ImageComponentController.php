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
class ImageComponentController extends XPHP_Controller{
    //put your code here
    public function indexAction()
    {
        $albumObj = new ImageCategory();
        $albums = $albumObj->getLimitOffsetImageAlbums(8, 0);
        $this->view->albums = $albums;
        
        return $this->view();
    }
}

?>
