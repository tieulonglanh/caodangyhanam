<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Daniel
 * Date: 6/13/13
 * Time: 9:39 PM
 * To change this template use File | Settings | File Templates.
 */

class IndexController extends XPHP_Controller
{
    public function IndexAction()
    {
        $this->loadLayout('/VNCPCOther');

        return $this->view();
    }

    public function detailAction() {
        $this->loadLayout('/VNCPCOther');

        $id = $this->params['id'];

        $project = new Project();
        $detail = $project->getProjectById($id);
        $project->changeViewCount($id, $detail->view_count);

        $this->view->detail = $detail;
        return $this->view();
    }

    public function moreDetailAction() {
        if($_POST['page']) {

            $page = $_POST['page'];

            $cur_page = $page;
            $page -= 1;
            $per_page = 8;
            $previous_btn = true;
            $next_btn = true;
            $first_btn = true;
            $last_btn = true;
            $start = $page * $per_page;

            $project = new Project();

            $count = $project->getCountProject();
            $result_pag_data = $project->getProject($per_page, $start);


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
            //$this->view->type = $_POST['type'];
        }
        return $this->view();
    }

    public function loadAction() {
        //if(isset($this->params[0])) $id=$this->params[0];

        $project = new Project();

        $this->view->detail = $project->getProjectById($this->params[0]);
        return $this->view();
    }

    public function moreProjectAction() {

        if($_POST['page']) {

            $page = $_POST['page'];

            $cur_page = $page;
            $page -= 1;
            $per_page = 8;
            $previous_btn = true;
            $next_btn = true;
            $first_btn = true;
            $last_btn = true;
            $start = $page * $per_page;

            $project = new Project();

            $count = $project->getCountProject();
            $result_pag_data = $project->getProject($per_page, $start);


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
            $this->view->type = $_POST['type'];
        }
        return $this->view();
    }
}