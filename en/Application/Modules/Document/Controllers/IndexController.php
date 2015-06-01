<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class IndexController extends XPHP_Controller{
    //put your code here

    
    public function indexAction(){

        $this->loadLayout('/VNCPCDocument');
        if(isset($this->params['id'])){
            $catId = $this->params['id'];
            $this->view->catId = $catId;
        }
        $documentCategory = new XFResourceCategory();
            $result_document_cat = $documentCategory->getCategoryByParent(8);
            
            $this->view->result_document_cat = $result_document_cat;
        return $this->view();
    }
    
    public function categoryAction(){

        $this->loadLayout('/VNCPCDocument');
        $catId = $this->params['id'];
        $this->catId = $catId;
        $documentCategory = new XFResourceCategory();
        $result_document_cat = $documentCategory->getCategoryByParent(8);
            
        $this->view->result_document_cat = $result_document_cat;
        return $this->view();
    }
    

    public function moreDocumentAction() {
        if($_POST['page']) {

            $page = $_POST['page'];
            if($_POST['catId']){
                $catId = $_POST['catId'];
            }

            $cur_page = $page;
            $page -= 1;
            $per_page = 8;
            $previous_btn = true;
            $next_btn = true;
            $first_btn = true;
            $last_btn = true;
            $start = $page * $per_page;

            $document = new XFResource();
            $documentCategory = new XFResourceCategory();
            $result_document_cat = $documentCategory->getCategoryByParent(8);
            $ids = array();
            $document_cat_datas = array();
            foreach($result_document_cat as $rc){
                $ids[] = $rc->resource_category_id;
                $document_cat_datas[$rc->resource_category_id] = $rc;
            }
        
            
            if(!isset($catId)){
                $result_pag_data = $document->getResourceLimitOffset($per_page, $start, $ids);
                $docIds = array();
                foreach($result_pag_data as $rd){
                    $docIds[] = $rd->resource_id;
                }
                $resourceVersion = new XFResourceVersion();
                $result_resource_version = $resourceVersion->getResourceLimitOffset($docIds);
                $result_resource_version_datas = array();
                foreach($result_resource_version as $rv){
                    $result_resource_version_datas[$rv->resource_id] = $rv;
                }
                
                $attachment = new XFAttachment();
                $attachmentInfos = $attachment->getAttachmentResourceDataInfos($docIds);
                
                $aids = array();
                $difs = array();
                foreach($attachmentInfos as $ai){
                    $aids[] = $ai->data_id;
                    $difs[$ai->data_id] = $ai;
                }
                
                $attachmentData = new XFAttachmentData();
                $attacmentInfos = $attachmentData->getAttachmentDataInfos($aids);
                $dataInfos =  array();
                foreach($attacmentInfos as $ai){
                    $dataInfos[$difs[$ai->data_id]->content_id] = $ai;
                }
                
                $count = $document->getCountDocument($ids);
            }
            else{
                $result_pag_data = $document->getResourceLimitOffsetByCatId($per_page, $start, $catId);
                $docIds = array();
                foreach($result_pag_data as $rd){
                    $docIds[] = $rd->resource_id;
                }
                $resourceVersion = new XFResourceVersion();
                $result_resource_version = $resourceVersion->getResourceLimitOffset($docIds);
                $result_resource_version_datas = array();
                foreach($result_resource_version as $rv){
                    $result_resource_version_datas[$rv->resource_id] = $rv;
                }
                
                $attachment = new XFAttachment();
                $attachmentInfos = $attachment->getAttachmentResourceDataInfos($docIds);
                
                $aids = array();
                $difs = array();
                foreach($attachmentInfos as $ai){
                    $aids[] = $ai->data_id;
                    $difs[$ai->data_id] = $ai;
                }
                
                $attachmentData = new XFAttachmentData();
                $attacmentInfos = $attachmentData->getAttachmentDataInfos($aids);
                $dataInfos =  array();
                foreach($attacmentInfos as $ai){
                    $dataInfos[$difs[$ai->data_id]->content_id] = $ai;
                }
                
                $count = $document->getCountDocument(array($catId));
            }
            
            $this->view->result_resource_version = $result_resource_version_datas;
            $this->view->result_document_cat = $document_cat_datas;
            $this->view->result_pag_data = $result_pag_data;
            $this->view->result_data_infos = $dataInfos;
            /* --------------------------------------------- */
            $no_of_paginations = ceil($count / $per_page);

            /* ---------------Calculating the starting and endign values for the loop----------------------------------- */
            if ($cur_page >= 6) {
                $start_loop = $cur_page - 3;
                if ($no_of_paginations > $cur_page + 3)
                    $end_loop = $cur_page + 3;
                else if ($cur_page <= $no_of_paginations && $cur_page > $no_of_paginations - 5) {
                    $start_loop = $no_of_paginations - 5;
                    $end_loop = $no_of_paginations;
                } else {
                    $end_loop = $no_of_paginations;
                }
            } else {
                $start_loop = 1;
                if ($no_of_paginations > 6)
                    $end_loop = 6;
                else
                    $end_loop = $no_of_paginations;
            }
            /* ----------------------------------------------------------------------------------------------------------- */


            /* ----------------------------------------------------------------------------------------------------------- */
            $this->view->first_btn = $first_btn;
            $this->view->last_btn = $last_btn;
            $this->view->cur_page = $cur_page;
            $this->view->previous_btn = $previous_btn;
            $this->view->start_loop = $start_loop;
            $this->view->end_loop = $end_loop;
            $this->view->no_of_paginations = $no_of_paginations;
            //$this->view->type = $_POST['type'];
        }

        return $this->view();
    }

}

?>
