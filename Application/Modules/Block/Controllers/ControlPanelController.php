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
		//Lay ra toan bo danh sach cac block
        $block = new Block();
        $dataSource = new XPHP_DataSource($block, array('id',
                                                        'name',
                                                        'brief'));
        return $dataSource;
	}
	
	#[Authorize]
	public function createAction()
	{
		return $this->view(new Block());
	}
	
	#[Authorize]
	public function createPost(Block $model)
	{
		if ($model->validate()) {
			//Kiểm tra xem có tồn tại block hay không ?
			if(!$model->blockExists())
			{
				//$model->html = htmlentities($model->html);
				if ($model->insert())
					return $this->json(
							array('success' => true,
							      'message' => 'Thêm block thành công',
                                  'url'     => $this->url->action('index')));
				else
					return $this->json(
							array('success' => false,
									'message' => 'Xảy ra lỗi khi thêm block ' . $model->db->getErrorMessage()));
			}
			else
				return $this->json(
						array('success' => false,
								'message' => 'Tên block này đã tồn tại bạn nên chọn tên khác!'));
		} else
			return $this->json(
					array('success' => false,
							'message' => 'Thông tin block nhập vào chưa hợp lệ'));
	}
	
	#[Authorize]
	public function editAction()
	{
		$id = $this->params[0];
		return $this->view(new Block($id));
	}
	
	#[Authorize]
	public function editPost(Block $model)
	{
		if ($model->validate()) {
                    if(isset($model->status))
                $model->status = 1;
			//$model->html = htmlentities($model->html);
			if ($model->update())
				return $this->json(
						array('success' => true,
								'message' => 'Cập nhật block thành công'));
			else
				return $this->json(
						array('success' => false,
								'message' => 'Xảy ra lỗi khi cập nhật block'));
		} else
			return $this->json(
					array('success' => false,
							'message' => 'Thông tin mục block nhập vào chưa hợp lệ'));
	}
}