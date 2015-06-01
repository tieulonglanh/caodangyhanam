<?php
class VideoComponentController extends XPHP_Controller
{
    public function indexAction()
    {
        return $this->view();
    }

    public function indexFrameAction()
    {
        //Lay ra danh sach 3 video moi nhat
        $model = new Video();
        $videos = $model->getLastestVideo(3);
        //Lay ra video play
        if ($this->hasParams(0)) {
            $play = $this->params[0];
            $this->view->videoPlay = $videos[$play];
            //Tang hit len 1
            $video = $model->getInstanceFrom($videos[$play]);
            $video->hit++;
            $video->update();
            unset($videos[$play]);
        } else {
            $this->view->videoPlay = $videos[0];
            //Tang hit len 1
            $video = $model->getInstanceFrom($videos[0]);
            $video->hit++;
            $video->update();
            unset($videos[0]);
        }
        $this->view->videos = $videos;
        return $this->view();
    }

    public function listAction()
    {
        $video = new Video();
        //Lay ra danh muc cac video
        $category = new VideoCategory();
        $this->view->categories = $category->getCategories();

        //Lay ra danh sach video cua danh muc neu khong co danh muc lay ra tat ca
        $cat = isset($this->params[0]) ? $this->params[0]
            : (isset($this->params['cat']) && $this->params['cat'] != "" ? $this->params['cat'] : null);
        $this->view->currentCat = $cat;
        $videoList = $video->getVideos($cat);
        $this->view->videoList = $videoList;

        return $this->view();
    }

    public function rightBarVideoAction()
    {
        $video = new Video();
        $videoInfo = $video->getVideos(4, 6);
        $this->view->videoInfo = $videoInfo;
        return $this->view();
    }

    public function teacherVideoAction()
    {
        $id = (int)$this->params['id'];
        $video = new Video();
        $videos = $video->getVideoByLeaderId($id);
        $this->view->videos = $videos;
        return $this->view();
    }
}