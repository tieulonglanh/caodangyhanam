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
        $page = new Project();
        $dataSource = new XPHP_DataSource($page, array('id',
                                                       'image',
                                                       'title',
                                                       'created_date',
                                                       'seo_url'  =>  'return  XPHP_Url::getActionUrl(array("action"     => "detail",
                                                                                                            "controller" => "Index",
                                                                                                            "module"     => "Project",
                                                                                                            "args"       => array("title" => $seo_url,
                                                                                                                                  "id"      => $id)));',
                                                       'view_count'));
        $dataSource->order_by('created_date', 'desc'); //Mặc định sắp xếp theo mới nhất
        return $dataSource;
    }

    #[Authorize]
    public function createAction()
    {
        return $this->view(new Project());
    }

    #[Authorize]
    public function createPost(Project $model)
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
        $model = new Project($this->params[0]);
        return $this->view($model);
    }

    #[Authorize]
    public function editPost(Project $mode)
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
    public function deleteAction(Page $model)
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