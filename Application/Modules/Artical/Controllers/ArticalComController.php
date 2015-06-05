<?php
class ArticalComController extends XPHP_Controller
{
	
    public function notifyAction() {
        
        $artical = new Artical();
        $articalCate = new ArticalCategory();
        $thongbaoCate = $articalCate->getCatBySeoUrl('thong-bao');
        
        $thongBao =  $artical->getLimitArticals(4, $thongbaoCate->id);
//        echo "<pre>";
//        var_dump($thongBao); die;
        
        $this->view->thongBao = $thongBao;
        return $this->view();
        
    }    
}