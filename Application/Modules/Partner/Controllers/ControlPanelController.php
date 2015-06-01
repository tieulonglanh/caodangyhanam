<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mc721
 * Date: 11/17/12
 * Time: 1:35 AM
 * To change this template use File | Settings | File Templates.
 */
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
        $artical = new Partner();
        $dataSource = new XPHP_DataSource($artical, array('id',
                                                          'file',
                                                          'name',
                                                          'created_date'));
        $dataSource->order_by('sort', 'desc'); 
        $dataSource->order_by('created_date', 'desc');//Mặc định sắp xếp theo mới nhất
        return $dataSource;
    }
    

    #[Authorize]
    public function createAction()
    {
        //Lấy ra cây menu đưa vào select menu cha
        $model = new Partner();
        return $this->view($model);
    }

    #[Authorize]
    public function createPost(Partner $model)
    {
        if ($model->validate())
        {
            $model->created_date = date('Y-m-d H:i:s');
            if ($model->insert())
                return $this->json(
                    array('success' => true,
                          'message' => 'Thêm danh mục thành công',
                          'url'     => $this->url->action('index')));
            else
                return $this->json(
                    array('success' => false,
                          'message' => 'Xảy ra lỗi khi thêm danh mục'));
        } else
            return $this->json(
                array('success' => false,
                      'message' => 'Thông tin danh mục nhập vào chưa hợp lệ'));
    }

    #[Authorize]
    public function editAction()
    {
        $id = (int)$this->params[0];
        //Lấy ra cây menu đưa vào select menu cha
        $model = new Partner($id);
        return $this->view($model);
    }

    #[Authorize]
    public function editPost(Partner $model)
    {
        if ($model->validate())
        {
            if ($model->update())
                return $this->json(
                    array('success' => true,
                          'message' => 'Cập nhật danh mục thành công',
                          'url'     => $this->url->action('index')));
            else
                return $this->json(
                    array('success' => false,
                          'message' => 'Xảy ra lỗi khi cập nhật danh mục'));
        } else
            return $this->json(
                array('success' => false,
                      'message' => 'Thông tin danh mục nhập vào chưa hợp lệ'));
    }
    
    #[Authorize]
    public function deletePost(Partner $model)
    {
        if($model->delete())
            return $this->json(
                array('success' => true,
                      'message' => 'Cập nhật danh mục thành công'));
        else
            return $this->json(
                array('success' => false,
                      'message' => 'Xảy ra lỗi khi xóa danh mục'));
    }   
    
}
