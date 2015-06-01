<?php
class ControlPanelController extends XPHP_Controller
{
	/**
	 * Danh sách các loại slide lấy trong cấu hình
	 * @var array
	 */
	public $slides;
	
	public function _init()
	{
		//Lấy ra các loại navigation được định nghĩa trong cấu hình
		$slides = XPHP_Config::getConfig(
				"modules > slides > slide");
		if (!isset($slides['@name'])) {
			foreach ($slides as $slide) {
				$this->slides[$slide['@type']] = $slide['@name'];
			}
		} else {
			$this->slides[$slides['@type']] = $slides['@name'];
		}
		
		//Load layout
		$this->loadLayout('ControlPanel/XAdmin');
	}
	
	#[Authorize]
	public function indexAction()
	{
		//Lay ra toan bo danh sach cac kieu slide dua vao hien thi
		$this->view->slides = $this->slides;
		return $this->view();
	}
	
	#[Authorize]
	public function listAction()
	{
		//Lay ra type
		$type = $this->params[0];
		$this->view->heading = $this->slides[$type];
		$this->view->type = $type;

		return $this->view();
	}
	
	#[DataTable]
	#[Authorize]
	public function listAjax()
	{
		//Lay ra toan bo danh sach cac content
        $slide = new Slide();
		$dataSource = new XPHP_DataSource($slide, array('name',
                                                        'brief',
                                                        'sort',
                                                        'image',
                                                        'id'));
        $dataSource->where('type', (int)$this->params[0]);
		return $dataSource;
	}
	
	#[Authorize]
	public function createAction()
	{
		//Lay ra loai slide
		$type = $this->params[0];
		//Model
		$model = new Slide();
		$model->type = $type;
		//Heading
		$this->view->heading = $this->slides[$type];
		$this->view->type = $type;
		return $this->view($model);
	}
	
	#[Authorize]
	public function createPost(Slide $model)
	{
        //Neu saveType = 0 thi khong redirect neu saveType = 1 thi redirect
        $url = $this->params['saveType'] == '1' ? $this->url->action('list', array($model->type)) : NULL;

        if ($model->validate()) {
        	$model->html = htmlentities($model->html);
            if ($model->insert())
                return $this->json(
                    array('success' => true,
                          'message' => 'Thêm slide thành công',
                          'url'     => $url));
            else
                return $this->json(
                    array('success' => false,
                          'message' => 'Xảy ra lỗi khi thêm slide ' . $model->db->getErrorMessage()));
        } else
            return $this->json(
                array('success' => false,
                      'message' => 'Thông tin slide nhập vào chưa hợp lệ'));
	}
	
	#[Authorize]
	public function editAction()
	{
		$model = new Slide($this->params[0]);
		$model->html = html_entity_decode($model->html);
		//Lay ra type
		$this->view->heading = $this->slides[$model->type];
		$this->view->type = $model->type;
		
		return $this->view($model);
	}
	
	#[Authorize]
	public function editPost(Slide $model)
	{
            //Neu saveType = 0 thi khong redirect neu saveType = 1 thi redirect
            $url = $this->params['saveType'] == '1' ? $this->url->action('list', array($model->type)) : NULL;
		if ($model->validate()) {
			$model->html = htmlentities($model->html);
			if ($model->update())
				return $this->json(
						array('success' => true,
						      'message' => 'Cập nhật slide thành công',
                              'url'     => $url));
			else
				return $this->json(
						array('success' => false,
							  'message' => 'Xảy ra lỗi khi cập nhật slide'));
		} else
			return $this->json(
					array('success' => false,
						  'message' => 'Thông tin mục slide nhập vào chưa hợp lệ'));
	}
	
	#[Authorize]
	public function deletePost(Slide $model)
	{
		if ($model->delete())
			return $this->json(
					array('success' => true, 'message' => 'Xóa slide thành công'));
		else
			return $this->json(
					array('success' => false,
						  'message' => 'Xảy ra lỗi khi cố gắng xóa slide'));
	}
}