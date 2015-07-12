<?php
class ControlPanelController extends XPHP_Controller
{
	public function _init()
	{
		$this->loadLayout('ControlPanel/XAdmin');
	}

    #[Authorize]
    public function indexAction()
    {
        return $this->view();
    }

    #[Authorize]
    #[DataTable]
    public function indexAjax()
    {
        $video = new Video();
        $dataSource = new XPHP_DataSource($video, array('id',
                                                        'avatar',
                                                        'title',
                                                        'created_date',
                                                        'sort',
                                                        'hit',
                                                        'rate_avg'));
        $dataSource->order_by('created_date', 'desc'); //Mặc định sắp xếp theo mới nhất
        return $dataSource;
    }
	
	#[Authorize]
	public function createAction()
	{	
		return $this->view(new Video());
	}
	
	#[Authorize]
	public function createPost(Video $model)
	{
            //Neu saveType = 0 thi khong redirect neu saveType = 1 thi redirect
            $url = $this->params['saveType'] == '1' ? $this->url->action('index') : NULL;
		if ($model->validate()) {
                        $model->category_id = 3;
			$model->created_date = date('Y-m-d H:i:s');
                        $model->author_id = $this->session->user->id;
			if ($model->insert())
				return $this->json(
						array('success' => true,
								'message' => 'Thêm video mới thành công',
                                                    'url'=>$url));
			else
				return $this->json(
						array('success' => false,
								'message' => 'Xảy ra lỗi khi thêm video'));
		} else
			return $this->json(
					array('success' => false,
							'message' => 'Thông tin nhập vào chưa hợp lệ'));
	}
	
	#[Authorize]
	public function editAction()
	{
		//Lay ra danh sach cac danh muc
		$category = new Category();
		$categories = $category->getCategories();
		$categoryOptions = array();
		foreach ($categories as $cat)
		{
			$categoryOptions[$cat->id] = $cat->name;
		}
		$this->view->categoryOptions = $categoryOptions;
		
                $role = new Role();
                $roles = $role->getRoles();
                $roleOptions = array();
                foreach ($roles as $r) {
                    $roleOptions[$r->id] = $r->name;
                }
                $this->view->roleOptions = $roleOptions;
                
                $teacherRole = $role->getByName('Teacher');    
                $user = new User();
                $users = $user->getUserByRole($teacherRole->id);
                $userOptions = array();
                foreach ($users as $u) {
                    $userOptions[$u->id] = $u->fullname;
                }
                $this->view->userOptions = $userOptions;
                
		$id = $this->params[0];
		$model = new Video($id);
		return $this->view($model);
	}
	
	#[Authorize]
	public function editPost(Video $model)
	{
            //Neu saveType = 0 thi khong redirect neu saveType = 1 thi redirect
            $url = $this->params['saveType'] == '1' ? $this->url->action('index') : NULL;
		if ($model->validate()) {
			if ($model->update())
				return $this->json(
						array('success' => true,
								'message' => 'Cập nhật video thành công',
                                                    'url'=>$url));
			else
				return $this->json(
						array('success' => false,
								'message' => 'Xảy ra lỗi khi cập nhật video'));
		} else
			return $this->json(
					array('success' => false,
							'message' => 'Thông tin nhập vào chưa hợp lệ'));
	}
	
	#[Authorize]
	public function deletePost(Video $model)
	{
		if ($model->delete()) {
			return $this->json(
					array('success' => true, 'message' => 'Xóa video thành công'));
		} else
			return $this->json(
					array('success' => false,
							'message' => 'Xảy ra lỗi khi xóa video'));
	}
}