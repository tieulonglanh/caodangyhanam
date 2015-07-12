<?php
class MenuController extends XPHP_Controller
{
	public function pageAction()
	{
        $model = new Page();
        //Lấy ra danh sách danh mục theo dạng cây
        $pages = $model->getPages();
        //Assign sang view
        $this->view->pages = $pages;

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
}