<?php
class SlideComController extends XPHP_Controller
{
    public function homeAction()
    {
        //Lay ra danh sach cac slide cua trang chu
        $slide = new Slide();
        $slides = $slide->getSlides(1);
        $this->view->slides = $slides;
        return $this->view();
    }

	public function techAction()
	{
		//Lay ra danh sach cac slide cua trang chu
		$slide = new Slide();
		$slides = $slide->getSlides(1);
		$this->view->slides = $slides;
		return $this->view();
	}
}