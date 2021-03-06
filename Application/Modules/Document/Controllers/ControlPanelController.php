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
        $page = new Document();
        $dataSource = new XPHP_DataSource($page, array('id',
                                                       'file',
                                                       'title',
                                                       'created_date',));
        $dataSource->order_by('created_date', 'desc'); //Mặc định sắp xếp theo mới nhất
        return $dataSource;
    }

    #[Authorize]
    public function createAction()
    {
        $cat = new DocumentCategory();
        $cats = $cat->getCategoryTree(0);
        $catOptions = array();
        $catOptions[0] = "Tất cả danh mục";
        foreach ($cats as $cItem)
        {
            $catOptions[$cItem->id] = $cItem->name;
        }
        $this->view->catOptions = $catOptions;
        return $this->view(new Document());
    }

    #[Authorize]
    public function createPost(Document $model)
    {
        //Neu saveType = 0 thi khong redirect neu saveType = 1 thi redirect
        $url = $this->params['saveType'] == '1' ? $this->url->action('index') : NULL;
        if ($model->validate()) {
            $model->created_date = date('Y-m-d H:i:s');
            if ($model->insert())
                return $this->json(
                    array('success' => true,
                          'message' => 'Thêm trang mới thành công',
                          'url'     => $url));
            else
                return $this->json(
                    array('success' => false,
                          'message' => 'Xảy ra lỗi khi thêm trang mới'));
        } else
            return $this->json(
                array('success' => false,
                      'message' => 'Thông tin nhập vào chưa hợp lệ'));
    }

    #[Authorize]
    public function importAction()
    {
    }

    #[Authorize]
    public function editAction()
    {
        $model = new Document($this->params[0]);
        
        $cat = new DocumentCategory();
        $cats = $cat->getCategoryTree(0);
        $catOptions = array();
        $catOptions[0] = "Tất cả danh mục";
        foreach ($cats as $cItem)
        {
            $catOptions[$cItem->id] = $cItem->name;
        }
        $this->view->catOptions = $catOptions;
        
        return $this->view($model);
    }

    #[Authorize]
    public function editPost(Document $mode)
    {
        //Neu saveType = 0 thi khong redirect neu saveType = 1 thi redirect
            $url = $this->params['saveType'] == '1' ? $this->url->action('index') : NULL;
        if ($this->model->validate()) {
            if ($this->model->update())
                return $this->json(
                    array('success' => true,
                          'message' => 'Cập nhật trang nội dung thành công',
                          'url'     => $url));
            else
                return $this->json(
                    array('success' => false,
                          'message' => 'Xảy ra lỗi khi cập nhật trang nội dung'));
        } else
            return $this->json(
                array('success' => false,
                      'message' => 'Các thông tin nhập vào chưa hợp lệ'));
    }

    #[Authorize]
    public function deleteAction(Document $model)
    {
        if ($this->model->delete()) {
            return $this->json(
                array('success' => true, 'message' => 'Xóa trang thành công'));
        } else
            return $this->json(
                array('success' => false,
                      'message' => 'Xảy ra lỗi khi cố xóa trang'));
    }

    #[Authorize]
    public function changeStatusAction()
    {
        $id = (int)$this->params['id'];
        $status = (int)$this->params['stt'];
        $page = new Page();
        if($page->changeStatus($id, $status))
            return $this->json(array('success' => true));
        else
            return $this->json(array('success' => false, "message" => "Đổi trạng thái lỗi"));
    }
}