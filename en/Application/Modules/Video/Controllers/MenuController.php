<?php
class MenuController extends XPHP_Controller
{
	public function categoryAction()
	{
        //Lấy ra cây menu đưa vào select menu cha
        $cat = new Category();
        $cats = $cat->getCategories();
        $this->view->cats = $cats;

        //Nếu truyền vào giá trị mặc định
        if(isset($this->params[0]))
        {
            $this->view->value = (int)$this->params[0];
        }
        else
        {
            $this->view->value = 0;
        }

        return $this->view();
	}

    public function videoAction()
    {
        //Lấy ra cây menu đưa vào select menu cha
        $artical  = new Video();
        $articals = $artical->getAllVideos();
        $this->view->articals = $articals;

        //Nếu truyền vào giá trị mặc định
        if(isset($this->params[0]))
        {
            $this->view->value = (int)$this->params[0];
        }
        else
        {
            $this->view->value = 0;
        }

        return $this->view();
    }

    public function indexAction()
    {
        return $this->view();
    }
}