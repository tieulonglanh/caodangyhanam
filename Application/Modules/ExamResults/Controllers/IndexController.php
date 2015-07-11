<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class IndexController extends XPHP_Controller{
    //put your code here

    
    public function indexAction(){

        $this->loadLayout('/default');
        
        $student_id = $this->params['masv'];
        $fullname = $this->params['hoten'];
        $subject_id = $this->params['mon'];
        if($student_id){
            $examResults = new ExamResults();
            $info = $examResults->getExamResultByCon($student_id, $fullname, $subject_id);
    //        var_dump($info); die;
            $this->view->info = $info;
            $this->view->fullname = $fullname;
            $this->view->student_id = $student_id;
            $this->view->subject_id = $subject_id;
        }else{
            $this->view->info = array();
        }
        return $this->view();
    }
}

?>
