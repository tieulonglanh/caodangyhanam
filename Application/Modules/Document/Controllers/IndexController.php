<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class IndexController extends XPHP_Controller{
    //put your code here

    
    public function indexAction(){
        
        $this->loadLayout('/default');
        if($this->params['page'])
            $page = $this->params['page'];
        else
            $page = 1;
        $limit = 35;
        
        $category_id = $this->params['id'];
        
        $offset = ($page-1)*$limit;
        
        $artical = new Document();
        
        $cat = new DocumentCategory();
        $cats = $cat->getCategoryTree(0);
        
        $catOptions = array();
        $catOptions[0] = "Chọn loại văn bản";
        foreach ($cats as $cItem)
        {
            $catOptions[$cItem->id] = $cItem->name;
        }
        $this->view->catOptions = $catOptions;
        
        if($category_id){
            $articals = $artical->getDocument($limit, $offset, $category_id);
            $count = $artical->getCountDocument($category_id);
        }else {
            $articals = $artical->getDocument($limit, $offset);
            $count = $artical->getCountDocument();
        }
        
        
        $this->view->category_id = $category_id;
        $this->view->limit = $limit;
        $this->view->count = $count;
        $this->view->page = $page;
        $this->view->articals = $articals;
        return $this->view();
    }
    
    public function viewAction() {
        $this->loadLayout('/default');
        $id = $this->params[0];
        $document = new Document();
        $detail = $document->getDocumentById($id);
        $arr = explode('.', $detail->file);
        $fileType = end($arr);
        $this->view->fileType = $fileType;
        $this->view->url = $detail->file;
        return $this->view();
    }
    
}

?>
