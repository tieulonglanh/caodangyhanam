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
        if(isset($this->params[0]))
            $this->view->catDefault = $this->params[0];
        else
            $this->view->catDefault = false;
        //Lấy ra cây menu đưa vào select menu cha
        $cat = new ArticalCategory();
        $cats = $cat->getCategoryTree(0);
        $catOptions = array();
        $catOptions[0] = "Tất cả danh mục";
        foreach ($cats as $cItem)
        {
            $catOptions[$cItem->id] = $cItem->name;
        }
        $this->view->catOptions = $catOptions;

        return $this->view();
    }

    #[Authorize]
    #[DataTable]
    public function indexAjax()
    {
        $artical = new Artical();
        $dataSource = new XPHP_DataSource($artical, array('id',
                                                          'category_id',
                                                          'image',
                                                          'title',
            'view_count',
                                                          'created_date'));
        $dataSource->order_by('created_date', 'desc'); //Mặc định sắp xếp theo mới nhất
        return $dataSource;
    }

    #[Authorize]
    public function editAction()
    {
        //Lấy ra cây menu đưa vào select menu cha
        $cat = new ArticalCategory();
        $cats = $cat->getCategoryTree(0);
        $catOptions = array();
        $catOptions[0] = "Menu gốc";
        foreach ($cats as $cItem)
        {
            $catOptions[$cItem->id] = $cItem->name;
        }
        $this->view->catOptions = $catOptions;

        $id = $this->params[0];
        $model = new Artical($id);

        return $this->view($model);
    }

    #[Authorize]
    public function editPost(Artical $model)
    {
        if ($model->validate())
        {
            if ($model->update())
                return $this->json(
                    array('success' => true,
                          'message' => 'Cập nhật bài viết thành công',
                          'url'     => $this->url->action('index', array($model->category_id))));
            else
                return $this->json(
                    array('success' => false,
                          'message' => 'Xảy ra lỗi khi cập nhật bài viết'));
        } else
            return $this->json(
                array('success' => false,
                      'message' => 'Thông tin bài viết nhập vào chưa hợp lệ'));
    }

    #[Authorize]
    public function createAction()
    {
        //Lấy ra cây menu đưa vào select menu cha
        $cat = new ArticalCategory();
        $cats = $cat->getCategoryTree(0);
        $catOptions = array();
        $catOptions[0] = "Menu gốc";
        foreach ($cats as $cItem)
        {
            $catOptions[$cItem->id] = $cItem->name;
        }
        $this->view->catOptions = $catOptions;

        $model = new Artical();

        return $this->view($model);
    }

    #[Authorize]
    public function createPost(Artical $model)
    {
        if ($model->validate())
        {
            $model->created_date = date('Y-m-d H:i:s');
            if ($model->insert())
                return $this->json(
                    array('success' => true,
                          'message' => 'Thêm bài viết thành công',
                          'url'     => $this->url->action('index', array($model->category_id))));
            else
                return $this->json(
                    array('success' => false,
                          'message' => 'Xảy ra lỗi khi thêm bài viết'));
        } else
            return $this->json(
                array('success' => false,
                      'message' => 'Thông tin bài viết nhập vào chưa hợp lệ'));
    }

    #[Authorize]
    public function deletePost(Artical $model)
    {
        if($model->delete())
            return $this->json(
                array('success' => true,
                      'message' => 'Xóa bài viết thành công'));
        else
            return $this->json(
                array('success' => false,
                      'message' => 'Xảy ra lỗi khi xóa bài viết'));
    }
}
