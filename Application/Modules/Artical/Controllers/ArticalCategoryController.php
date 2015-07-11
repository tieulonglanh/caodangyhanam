<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mc721
 * Date: 11/17/12
 * Time: 1:35 AM
 * To change this template use File | Settings | File Templates.
 */
class ArticalCategoryController extends XPHP_Controller
{
    public function _init()
    {
        $this->loadLayout('ControlPanel/XAdmin');
    }

    #[Authorize]
    public function indexAction()
    {
        //Lấy ra danh sách các danh mục đa cấp
        $model = new ArticalCategory();
        $cats = $model->getCategoryMultiLevel(0);
        $this->view->htmlCatMultiLevel = $this->_createHtmlCatMultiLevel($cats);
        return $this->view();
    }

    #[Authorize]
    public function indexPost()
    {
        //Lưu sort của category
        $order = array();
        //List order category
        $list = $this->params['list'];
        foreach ($list as $id => $parent_id)
        {
            $model = new ArticalCategory($id);
            if (!isset($order[$parent_id]))
            {
                //Thứ tự
                $sort = $order[$parent_id] = 1;
            }
            else
            {
                //Tăng order lên 1
                $order[$parent_id] = $order[$parent_id] + 1;
                //Thứ tự
                $sort = $order[$parent_id];
            }
            $model->sort = $sort;
            //Nếu parent_id == root chuyển thành 0
            if ($parent_id == "root")
                $model->parent_id = 0;
            else
                $model->parent_id = $parent_id;
            //Cập nhật
            $model->update();
        }
        return $this->json(
            array('success' => true,
                  'message' => 'Cập nhật thứ tự danh mục thành công'));
    }

    #[Authorize]
    public function createAction()
    {
        //Lấy ra cây menu đưa vào select menu cha
        $model = new ArticalCategory();
        $cats = $model->getCategoryTree(0);
        $catOptions = array();
        $catOptions[0] = "Danh mục gốc";
        foreach ($cats as $cItem)
        {
            $catOptions[$cItem->id] = $cItem->name;
        }
        $this->view->catOptions = $catOptions;
        return $this->view($model);
    }

    #[Authorize]
    public function createPost(ArticalCategory $model)
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
        $model = new ArticalCategory($id);
        $cats = $model->getCategoryTree(0);
        $catOptions = array();
        $catOptions[0] = "Danh mục gốc";
        foreach ($cats as $cItem)
        {
            $catOptions[$cItem->id] = $cItem->name;
        }
        $this->view->catOptions = $catOptions;
        return $this->view($model);
    }

    #[Authorize]
    public function editPost(ArticalCategory $model)
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
    public function deletePost(ArticalCategory $model)
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

    /**
     * Lấy ra html hiển thị danh mục đa cấp
     * @param array $cats ArticalCategory Multilevel
     */
    private function _createHtmlCatMultiLevel($cats)
    {
        $html = "<ol class='rectangle-list sortable'>";
        foreach ($cats as $cItem) {
            $editUrl = $this->url->action("edit", array($cItem->id));
            $deleteUrl = $this->url->action("delete");
            $html .= "<li id='list_{$cItem->id}'><a target='blank' href='/trang-tin/{$cItem->seo_url}.{$cItem->id}.html'>{$cItem->name}</a>
                          <span class='edit tip' style='cursor: pointer;' title='Sửa' data-id='{$cItem->id}' data-name='{$cItem->name}' data-type='1'><img src='/Content/XMin/images/icon/icon_edit.png' /></span>
                          <span class='delete tip'  style='cursor: pointer;' title='Xóa' data-id='{$cItem->id}' data-name='{$cItem->name}'><img src='/Content/XMin/images/icon/icon_delete.png' /></span>";
            if (!empty($cItem->subs)) {
                $html .= $this->_recursiveCatMultiLevel($cItem->subs);
            }
            $html .= "</li>";
        }
        $html .= "</ol>";
        return $html;
    }

    /**
     * Hàm đệ quy lấy ra danh mục đa cấp
     * @param array $cats ArticalCategory Multilevel
     */
    private function _recursiveCatMultiLevel($cats)
    {
        $html = "<ol class='rectangle-list sortable'>";
        foreach ($cats as $cItem) {
            $editUrl = $this->url->action("edit", array($cItem->id));
            $deleteUrl = $this->url->action("delete");
            $html .= "<li id='list_{$cItem->id}'><a target='blank' href='/trang-tin/{$cItem->seo_url}.{$cItem->id}.html'>{$cItem->name}</a>
                          <span class='edit tip' title='Sửa' data-id='{$cItem->id}' data-name='{$cItem->name}' data-type='1'><img src='/Content/XMin/images/icon/icon_edit.png' /></span>
                          <span class='delete tip' title='Xóa' data-id='{$cItem->id}' data-name='{$cItem->name}'><img src='/Content/XMin/images/icon/icon_delete.png' /></span>
                      ";
            if (!empty($cItem->subs)) {
                $html .= $this->_recursiveCatMultiLevel($cItem->subs);
            }
            $html .= "</li>";
        }
        $html .= "</ol>";
        return $html;
    }
}
