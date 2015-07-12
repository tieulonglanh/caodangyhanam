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
        $ct = new Contact();
        $dataSource = new XPHP_DataSource($ct, array(  'id',
                                                       'name',
                                                       'email',
                                                       'title',
                                                       'created_date',
                                                       'phone'));
        $dataSource->order_by('created_date', 'desc'); //Mặc định sắp xếp theo mới nhất
        return $dataSource;
    }

    #[Authorize]
    public function deleteAction(Contact $model)
    {
        if ($model->delete()) {
            return $this->json(
                array('success' => true, 'message' => 'Xóa ticket thành công'));
        } else
            return $this->json(
                array('success' => false,
                      'message' => 'Xảy ra lỗi khi cố xóa ticket'));
    }

    public function showAction()
    {
        $id = $this->params['id'];
        $ct = new Contact($id);
        $ct->status = 1;
        $ct->update();
        $this->view->contact = $ct;

        $this->unloadLayout();
        return $this->view();
    }

    /*
	public function _init()
	{
		$this->loadLayout('ControlPanel/NewAdmin');
	}
	
	#[Authorize(roles = 'Administrator')]
	public function indexAction()
	{
		//Lấy danh sách thông tin liên hệ
		$modelContact = new Contacts();
		$arrContact = $modelContact->db->order_by('create_date', 'DESC')
											->get()
											->result();
		$deleteUrl = $this->url->action("delete");
		for ($i = 0; $i < count($arrContact); $i ++) {
			if ($arrContact[$i]->status == 1)
				$arrContact[$i]->status = 'Đã đọc';
			else
				$arrContact[$i]->status = 'Chưa đọc';
        	$arrContact[$i]->action = "<a href='{$deleteUrl}' class='button delete' item='{$arrContact[$i]->full_name}' title='Xóa' 
            					data='{$arrContact[$i]->id}' rel='delete'>
            						<img src='".$this->url->content('Content/NewAdmin/images/icons/fugue/cross-circle.png')."'/>
            					</a>";
		}
		$this->view->arrContact = $arrContact;
		return $this->view();
	}
	
	public function changeStatusAction()
	{
		$model = new Contacts();
		$comment = $model->db->where('id', $this->params['id'])
							->get()->result();
		$comment = $comment[0];
		if ($comment->status == 1) {
			$status = 0; $text = 'Chưa duyệt';
		}
		else 
		{
			$status = 1; $text = 'Đã duyệt';
		}
		$data['status'] = $status;
		$model->db->where('id', $this->params['id'])->update($data);
		return $this->json(array('success' => true, 'text' => $text));
	}
	
	#[Authorize(roles = 'Administrator')]
	public function deletePost(Contacts $model = null)
	{
		if ($this->model->delete())
            return $this->json(
            array('success' => true, 
            'message' => 'Xóa thành công'));
        else
            return $this->json(
            array('success' => false, 
            'message' => 'Xảy ra lỗi khi cố gắng xóa'));
	}
    */
}