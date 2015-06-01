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
        $model = new Service();
        $pages = $model->getPages();
        //Trả về view
        $this->view->pages = $pages;
        return $this->view();
    }

    #[Authorize]
    #[DataTable]
    public function indexAjax()
    {
        $page = new Service();
        $dataSource = new XPHP_DataSource($page, array('id',
                                                       'avatar',
                                                       'title',
                                                       'created_date',
                                                       'seo_url'  =>  'return  XPHP_Url::getActionUrl(array("action"     => "index",
                                                                                                            "controller" => "Index",
                                                                                                            "module"     => "Service",
                                                                                                            "args"       => array("seo_url" => $seo_url,
                                                                                                                                  "id"      => $id)));',
                                                       'view_count'));
        $dataSource->order_by('created_date', 'desc'); //Mặc định sắp xếp theo mới nhất
        return $dataSource;
    }

    #[Authorize]
    public function createAction()
    {
        return $this->view(new Service());
    }

    #[Authorize]
    public function createPost(Service $model)
    {
        //Neu saveType = 0 thi khong redirect neu saveType = 1 thi redirect
            $url = $this->params['saveType'] == '1' ? $this->url->action('index') : NULL;
        if ($model->validate()) {
            if(isset($model->status))
                $model->status = 1;
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
        $model = new Service($this->params[0]);
        return $this->view($model);
    }

    #[Authorize]
    public function editPost(Service $mode)
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
    public function deleteAction(Service $model)
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
        $page = new Service();
        if($page->changeStatus($id, $status))
            return $this->json(array('success' => true));
        else
            return $this->json(array('success' => false, "message" => "Đổi trạng thái lỗi"));
    }
}