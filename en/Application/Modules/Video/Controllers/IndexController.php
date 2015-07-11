<?php
class IndexController extends XPHP_Controller
{

	public function indexAction()
	{
		/*//Lay ra thong tin video moi nhat
		$video = new Video();

		$videos = $video->getLastestVideo(12);
		$this->view->lastestVideos = $videos;

            //Lay ra 8 video xem nhieu nhat
            $mostViewVideos = $video->getMostViewVideo(8);
                    $this->view->mostViewVideos = $mostViewVideos;

            //Lay ra 8 video binh chon nhieu nhat
            $mostFavVideo   = $video->getMostFavVideo(8);
            $this->view->mostFavVideo = $mostFavVideo;

            //Lay ra danh muc video
            $category = new Category();
            $categories = $category->getCategoryMultiLevel();
            $this->view->categories = $categories;*/
            $this->loadLayout('/default');
            $video = new Video();
            //Lay ra video moi nhat
            if($this->params['page'])
                $page = $this->params['page'];
            else
                $page = 1;
            $limit = 6;

            $offset = ($page-1)*$limit;

            $lastestVideos = $video->getLastestVideo($limit, $offset);

            $this->view->limit = $limit;
            $this->view->count = $count;
            $this->view->page = $page;

            $this->view->lastestVideos = $lastestVideos;
		return $this->view();
	}

    public function moreVideoAction() {
        $num = $this->params[0];
        if($num==1) $offset = 0;
        else $offset = ($num - 1)*4 - 1;

        $video = new Video();
        $moreVideos = $video->getLastestVideo(4, $offset);
        $this->view->moreVideos = $moreVideos;
        return $this->view();
    }

    public function playVideoAction() {
        $id = $this->params[0];

        $video = new Video();
        $playVideo = $video->getVideoById($id);

        $this->view->playVideo = $playVideo;
        return $this->view();
    }

    public function categoryAction()
    {
        $id = isset($this->params['id']) ? $this->params['id'] : $this->params[1];

        $limit = 20;
        $this->view->limit = $limit;

        $page = isset($this->params['page']) ? $this->params['page'] : 1;
        $this->view->page = $page;

        $offset = ($page - 1) * $limit;

        $videoCategory = new Category((int)$id);
        $categories = $videoCategory->getCategoryMultiLevel();
        $this->view->categories = $categories;
        $this->view->category = $videoCategory;

        //Lay ra thong tin video moi nhat
        $video = new Video();
        $count = $video->countVideoByCateogoryId((int)$id);
        $this->view->count = $count;

        $videos = $video->getVideoByCateogoryId((int)$id, $limit, $offset);
        $this->view->videos = $videos;

        return $this->view();
    }

    public function lastAction()
    {
        $limit = 20;
        $this->view->limit = $limit;

        $page = isset($this->params['page']) ? $this->params['page'] : 1;
        $this->view->page = $page;

        $offset = ($page - 1) * $limit;

        //Lay ra thong tin video moi nhat
        $video = new Video();
        $count = $video->countLastestVideo();
        $this->view->count = $count;

        $videos = $video->getLastestVideo($limit, $offset);
        $this->view->videos = $videos;

        $category = new Category();
        $categories = $category->getCategoryMultiLevel();
        $this->view->categories = $categories;

        return $this->view();
    }

    public function mostViewAction()
    {
        $limit = 20;
        $this->view->limit = $limit;

        $page = isset($this->params['page']) ? $this->params['page'] : 1;
        $this->view->page = $page;

        $offset = ($page - 1) * $limit;

        //Lay ra thong tin video moi nhat
        $video = new Video();
        $count = $video->getMostViewVideo($limit, $offset);
        $this->view->count = $count;

        $mostViewVideos = $video->getMostViewVideo($limit, $offset);
        $this->view->mostViewVideos = $mostViewVideos;

        $category = new Category();
        $categories = $category->getCategoryMultiLevel();
        $this->view->categories = $categories;

        return $this->view();
    }

    public function mostRateAction()
    {
        $limit = 20;
        $this->view->limit = $limit;

        $page = isset($this->params['page']) ? $this->params['page'] : 1;
        $this->view->page = $page;

        $offset = ($page - 1) * $limit;

        //Lay ra thong tin video yeu thich nhieu nhat
        $video = new Video();
        $count = $video->countMostFavVideo();
        $this->view->count = $count;
        $mostFavVideos = $video->getMostFavVideo($limit, $offset);
        $this->view->mostFavVideos = $mostFavVideos;

        $category = new Category();
        $categories = $category->getCategoryMultiLevel();
        $this->view->categories = $categories;

        return $this->view();
    }
	
	public function detailAction()
	{
            $this->loadLayout("/default");
		//Lay ra id cua video
		$id = isset($this->params['id']) ? $this->params['id'] : (isset($this->params[0]) ? $this->params[0] : null);
		if($id === null || $id == "")
			return $this->redirect('index', 'Index', '');
		//Lay ra thong tin cua Video
		$video = new Video($id);
        $v = $video->getVideoById($id);

        //Check role video
        if(!empty($v->role_id))
        {
            $v->role_id = explode(':', $v->role_id);
            if(isset($this->session->memberInfo))
            {
                //Lấy ra role của nguời dùng
                if(! in_array($this->session->memberInfo->role_id, $v->role_id))
                    return $this->redirect('denied', array('error' => '1'));
            }
            else
                return $this->redirect('index', 'Member', 'Auth', array($this->url->encode(XPHP_Url::getCurrentUrl())));
        }

        $this->view->video = $v;
		//Tang hit len 1
        if(isset($this->session->video_view))
        {
            $viewed = $this->session->video_view;
            if(!isset($viewed[$id]))
            {
                $video->hit++;
                $video->update();
                $viewed[$id] = true;
                $this->session->video_view = $viewed;
            }
        }
        else
        {
            $video->hit++;
            $video->update();
            $viewed = array($id => true);
            $this->session->video_view = $viewed;
        }

		//Lay ra thong tin danh muc
		$category = new Category($video->category_id);
        $categories = $category->getCategoryMultiLevel();
        $this->view->categories = $categories;
		$this->view->category = $category;

        //Lay ra danh sach cac video tuong tu
        $this->view->relativeVideo = $video->getRelativeVideo($category->id, $id, 4);

		//Gan params cat
		$this->view->paramCat = $category->id;

		return $this->view();
	}

    public function plusFavAction()
    {
        $id = (int)$this->params['id'];
        if($id)
        {
            $video = new Video($id);
            //Tang fav len 1
            if(isset($this->session->video_fav))
            {
                $fav = $this->session->video_fav;
                if(!isset($fav[$id]))
                {
                    $video->fav_count++;
                    $video->update();
                    $fav[$id] = true;
                    $this->session->video_fav = $fav;
                    return $this->json(array('success' => true, 'message' => 'Bạn đã chọn yêu thích video thành công!', 'count' => $video->fav_count));
                }
                else
                    return $this->json(array('success' => false, 'message' => 'Bạn đã chọn yêu thích video này!'));
            }
            else
            {
                $video->fav_count++;
                $video->update();
                $fav = array($id => true);
                $this->session->video_fav = $fav;
                return $this->json(array('success' => true, 'message' => 'Bạn đã chọn yêu thích video thành công!', 'count' => $video->fav_count));
            }
        }
        $this->unloadLayout();
    }

    public function deniedAction()
    {
        $error = $this->params[0];
        switch($error)
        {
            case '1':
                $error = "Bạn không có quyền xem video này. Hãy đăng ký làm học viên để xem video";
                break;
            default:
                break;
        }
        $this->view->error = $error;
        return $this->view();
    }
}