<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of IndexController
 *
 * @author Long Nguyen
 */
class IndexController extends XPHP_Controller{
    //put your code here
    private $newsCenter_id              = 1;
    private $newsFromProject_id         = 2 ;
    private $newsAboutProduction_id     = 3;

    public function getNewsCenterId() {
        return $this->newsCenter_id;
    }

    public function getNewsFromProjectId() {
        return $this->newsFromProject_id;
    }

    public function getNewsAboutProductionId() {
        return $this->newsAboutProduction_id;
    }
    
    public function categoryAction(){
        $this->loadLayout('/PHPDay');
        $id = $this->params['id'];
        $page = new Page();
        $firstPage = $page->getNewestPage();
        $this->view->firstPage = $firstPage[0];          
        
        $articalCategory = new ArticalCategory();
        $category = $articalCategory->getCatByid($id);
        $this->view->catName = $category[0]->name;
        
        
        $artical = new Artical();
        $newestArtical = $artical->getArticalsByCategory($id);
        $this->view->articals = $newestArtical;
        
        if($category[0]->name == 'Tin tức'){
            $id = 5;
        }
                
        $news = $artical->getLimitArticals(6, $id);
        $this->view->news = $news;        
        
        return $this->view();
    }

    public function listAction() {
        $this->loadLayout('/default');
        if($this->params['page'])
            $page = $this->params['page'];
        else
            $page = 1;
        $limit = 6;
        
        $offset = ($page-1)*$limit;
        
        $id = $this->params['id'];
        $artical = new Artical();
        $articalCategory = new ArticalCategory();
        $category = $articalCategory->getCatByid($id);
        
        if($category->seo_url == 'trang-tin-noi-bo'){
            if(!isset($_SESSION['role_id'])||$_SESSION['role_id'] != 1){
                echo "<script>alert('Bạn không có quyền truy cập! Vui lòng đăng nhập để xem.'); window.location.href='/'</script>";
            }
        }
        if($id == 0){
            return false;
            $count = 0;
            //$articals = $artical->getArticals($limit, $offset);
        }else{
            $articals = $artical->getArticalsByCategory($limit, $id, $offset);
            $count = $artical->getCountArtical($id);
        }
        
        $this->view->limit = $limit;
        $this->view->count = $count;
        $this->view->page = $page;
        $this->view->articals = $articals;
        $this->view->category = $category;
        
        return $this->view();
    }

    public function detailAction() {
        $this->loadLayout('/VNCPCOther');
        if(isset($this->params['id']))
            $id = $this->params['id'];
        else
            $id = $this->params[0];
//        if(isset($this->params['category'])) $cat_id = $this->params['category'];
//        else $cat_id = 0;
        $artical = new Artical();
        $detail = $artical->getArticalById($id);
//        echo $detail->view_count;die;
        $artical->changeViewCount($id, $detail->view_count);

        $this->view->detail = $detail;
        $this->view->cat_id = $detail->category_id;
        return $this->view();
    }

    public function newAction() {
        $this->loadLayout('/default');
        $id = $this->params['id'];
        $artical = new Artical();
        $detail = $artical->getArticalById($id);
        if(!isset($detail->title)){
            echo "<script>alert('Bài viết hiện tại không cho phép xem!');  window.location.href='/'</script>";
        }
        
        $this->view->titleForLayout = $detail->title;
        $artical->changeViewCount($id, $detail->view_count);
        
        $articalCategory = new ArticalCategory();
        $category = $articalCategory->getCatByid($detail->category_id);
        
        if($category->seo_url == 'trang-tin-noi-bo'){
            if(!isset($_SESSION['role_id'])||$_SESSION['role_id'] != 1){
                echo "<script>alert('Bạn không có quyền truy cập! Vui lòng đăng nhập để xem.'); window.location.href='/'</script>";
            }
        }
        $this->view->category = $category;
        
        $this->view->detail = $detail;
        return $this->view();
    }

    public function moreNewsAction() {

        if($_POST['page']) {

            $page = $_POST['page'];
            $cat_id = $_POST['cat_id'];

            $cur_page = $page;
            $page -= 1;
            $per_page = 10;
            $previous_btn = true;
            $next_btn = true;
            $first_btn = true;
            $last_btn = true;
            $start = $page * $per_page;

            $artical = new Artical();




            if($cat_id == 0) {
                $count = $artical->getCountArtical();
                $result_pag_data = $artical->getArticals($per_page, $start);
            } else {
                $count = $artical->getCountArtical($cat_id);
                $result_pag_data = $artical->getArticalsByCategory($per_page,$cat_id,$start);
            }
            $this->view->result_pag_data = $result_pag_data;
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
            $this->view->first_btn = $first_btn;
            $this->view->last_btn = $last_btn;
            $this->view->cur_page = $cur_page;
            $this->view->previous_btn = $previous_btn;
            $this->view->start_loop = $start_loop;
            $this->view->end_loop = $end_loop;
            $this->view->no_of_paginations = $no_of_paginations;
            $this->view->cat_id = $cat_id;
        }
        return $this->view();
    }
    
    public function indexAction(){

        $this->loadLayout('/VNCPCOther');

        $artical = new Artical();
        $articalCategory = new ArticalCategory();

        $newsCenterId = $this->getNewsCenterId();
        $newsFromProjectId = $this->getNewsFromProjectId();
        $newsAboutProductionId = $this->getNewsAboutProductionId();

        $this->view->newestNews             = $artical->getArticals(3);
        $this->view->newsCenter             = $artical->getArticalsByCategory(1, $newsCenterId);
        $this->view->newsFromProject        = $artical->getArticalsByCategory(1, $newsFromProjectId);
        $this->view->newsAboutProduction    = $artical->getArticalsByCategory(3, $newsAboutProductionId);

        $this->view->newsCenterId = $newsCenterId;
        $this->view->newsCenterSeo = $articalCategory->getCatByid($newsCenterId)->seo_url;

        $this->view->newsFromProjectId = $newsFromProjectId;
        $this->view->newsFromProjectSeo = $articalCategory->getCatByid($newsFromProjectId)->seo_url;

        $this->view->newsAboutProductionId = $newsAboutProductionId;
        $this->view->newsAboutProductionSeo = $articalCategory->getCatByid($newsAboutProductionId)->seo_url;


        return $this->view();
    }

    public function testAction() {
        $this->loadLayout('/VNCPCOther');

        return $this->view();
    }

    public function loadAction() {
        if(isset($this->params[0])) $id=$this->params[0];

        $artical = new Artical();

        $this->view->detail = $artical->getArticalById($this->params[0]);
        return $this->view();
    }
    
}

?>
