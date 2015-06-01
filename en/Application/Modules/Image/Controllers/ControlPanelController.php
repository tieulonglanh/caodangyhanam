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
        $artical = new ImageCategory();
        $dataSource = new XPHP_DataSource($artical, array('id',
                                                          'image',
                                                          'name',
                                                          'created_date'));
        $dataSource->order_by('created_date', 'desc'); //Mặc định sắp xếp theo mới nhất
        return $dataSource;
    }
    
    #[Authorize]
    public function viewAction()
    {
        //Lay ra danh sach cac danh muc video
        $category = new ImageCategory($this->params[0]);
        //$categories = $category->getLecture();
        //$categoryOptions = array('0' => "Tất cả bài học");
        //foreach ($categories as $cat) {
            $categoryOptions[$category->id] = $category->name;
        //}
        $this->view->categoryOptions = $categoryOptions;
        $this->view->catId = $this->params[0];
        return $this->view();
    }

    #[Authorize]
    #[DataTable]
    public function viewAjax()
    {
        $artical = new Image();
        $dataSource = new XPHP_DataSource($artical, array('id',
                                                          'file',
            'name',
                                                          'cat_id',                                                          
                                                          ));
        return $dataSource;
    }
    
    #[Authorize]
    public function createImageAction()
    {
        $category = new ImageCategory($this->params[0]);
        $categoryOptions[$category->id] = $category->name;
        $this->view->categoryOptions = $categoryOptions;
        //Lấy ra cây menu đưa vào select menu cha
        $model = new Image();
        return $this->view($model);
    }

    #[Authorize]
    public function createImagePost(Image $model)
    {
        if ($model->validate())
        {
            if ($model->insert())
                return $this->json(
                    array('success' => true,
                          'message' => 'Thêm danh mục thành công',
                          'url'     => $this->url->action('view', array($model->cat_id))));
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
    public function createAction()
    {
        //Lấy ra cây menu đưa vào select menu cha
        $model = new ImageCategory();
        return $this->view($model);
    }

    #[Authorize]
    public function createPost(ImageCategory $model)
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
        $model = new ImageCategory($id);
        return $this->view($model);
    }

    #[Authorize]
    public function editPost(ImageCategory $model)
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
    public function deletePost(ImageCategory $model)
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
    
    #[Authorize]
    public function deleteImagePost(Image $model)
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
