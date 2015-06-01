<?php
class ManagerController extends XPHP_Controller
{
    /**
     * Các loại thanh điều hướng với key = type, value = tên
     * @var array
     */
    public $navigations;
    /**
     * Các kiểu của mục menu với key = kiểu menu(0 - nếu là Url),
     * value = array('controller' => tên controller, 'area' => tên area)
     * @var array
     */
    public $menuTypeController;

    public function _init()
    {
        $this->loadLayout('ControlPanel/XAdmin');
        //Lấy ra các loại navigation được định nghĩa trong cấu hình
        $navigations = XPHP_Config::getConfig("modules > navigation > navigation");
        if (!isset($navigations['@name']))
        {
            foreach ($navigations as $nav)
                $this->navigations[$nav['@type']] = $nav['@name'];
        }
        else
            $this->navigations[$navigations['@type']] = $navigations['@name'];
    }

    #[Authorize]
    public function indexAction()
    {
        $this->view->navigations = $this->navigations;
        return $this->view();
    }

    #[Authorize]
    public function listAction()
    {
        //Lấy ra menu type
        $type = (int)$this->params[0];
        //Lấy ra danh sách các menu
        $model = new Navigation();
        $menu = $model->getMenuMultiLevel(0, $type);
        $this->view->htmlMenuMultiLevel = $this->_createHtmlMenuMultiLevel(
            $menu);
        //Lấy ra các thông tin hiển thị
        $this->view->heading = $this->navigations[$type];
        $this->view->navType = $type;
        return $this->view();
    }

    #[Authorize]
    public function listPost()
    {
        //Lưu sort của category
        $order = array();
        //List order category
        $list = $this->params['list'];
        foreach ($list as $id => $parent_id) {
            $model = new Navigation($id);
            if (!isset($order[$parent_id])) {
                //Thứ tự
                $sort = $order[$parent_id] = 1;
            } else {
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
            array('success' => true, 'message' => 'Cập nhật thứ tự menu thành công'));
    }

    #[Authorize]
    public function createAction()
    {
        //Lấy ra menu type
        $type = (int)$this->params[0];
        //Lấy ra cây menu đưa vào select menu cha
        $model = new Navigation();
        $menu = $model->getMenuTree(0, $type);
        $menuOptions = array();
        $menuOptions[0] = "Menu gốc";
        foreach ($menu as $menuItem) {
            $menuOptions[$menuItem->id] = $menuItem->title;
        }
        $this->view->menuOptions = $menuOptions;
        //Lấy ra các thông tin hiển thị
        $this->view->heading = $this->navigations[$type];
        $this->view->navType = $type;
        $navigationNode = new NavigationNode();
        $nodes = $navigationNode->getNodeMultiLevel(0);
        $this->view->nodesOptions = $nodes;
        return $this->view(new Navigation());
    }

    #[Authorize]
    public function createPost(Navigation $model)
    {
        //Neu saveType = 0 thi khong redirect neu saveType = 1 thi redirect
            $url = $this->params['saveType'] == '1' ? $this->url->action('list', array($model->type)) : NULL;
        if ($model->validate()) {
            $model->type = (int)$this->params[0];
            if ($model->insert())
                return $this->json(
                    array('success' => true,
                         'message' => 'Thêm mục menu thành công',
                         'url'     => $url));
            else
                return $this->json(
                    array('success' => false,
                         'message' => 'Xảy ra lỗi khi thêm mục menu'));
        } else
            return $this->json(
                array('success' => false,
                     'message' => 'Thông tin mục menu nhập vào chưa hợp lệ'));
    }

    #[Authorize]
    public function editAction()
    {
        //Model
        $model = new Navigation($this->params[0]);
        //Nav type
        $type = (int)$model->type;
        //Lấy ra cây menu đưa vào select menu cha
        $navModel = new Navigation();
        $menu = $navModel->getMenuTree(0, $type);
        $menuOptions = array();
        $menuOptions[0] = "Menu gốc";
        foreach ($menu as $menuItem) {
            $menuOptions[$menuItem->id] = $menuItem->title;
        }
        $this->view->menuOptions = $menuOptions;

        //Lấy ra các thông tin hiển thị
        $this->view->heading = $this->navigations[$type];
        $this->view->navType = $type;
        $navigationNode = new NavigationNode();
        $nodes = $navigationNode->getNodeMultiLevel(0);
        $this->view->nodesOptions = $nodes;

        return $this->view($model);
    }

    #[Authorize]
    public function editPost(Navigation $model)
    {
        //Neu saveType = 0 thi khong redirect neu saveType = 1 thi redirect
            $url = $this->params['saveType'] == '1' ? $this->url->action('list', array($model->type)) : NULL;
        if ($model->validate()) {
            if ($model->update())
                return $this->json(
                    array('success' => true,
                         'message' => 'Cập nhật menu thành công',
                         'url'     => $url));
            else
                return $this->json(
                    array('success' => false,
                         'message' => 'Xảy ra lỗi khi cập nhật mục menu'));
        } else
            return $this->json(
                array('success' => false,
                     'message' => 'Thông tin mục menu nhập vào chưa hợp lệ'));
    }

    #[Authorize]
    public function deletePost(Navigation $model)
    {
        if ($this->model->delete())
            return $this->json(
                array('success' => true,
                      'message' => 'Xóa menu thành công'));
        else
            return $this->json(
                array('success' => false,
                     'message' => 'Xảy ra lỗi khi cố gắng xóa menu'));
    }

    #[Authorize]
    public function urlNodeAction()
    {
        if(isset($this->params['data']))
            $value = urldecode($this->params['data']);
        else
            $value = "";
        $this->view->value = $value;
        $this->layout = null;
        return $this->view();
    }

    /**
     * Lấy ra html hiển thị menu đa cấp
     * @param array $menu Menu Multilevel
     */
    private function _createHtmlMenuMultiLevel($menu)
    {
        $html = "<ol class='rectangle-list sortable'>";
        foreach ($menu as $menuItem)
        {
            $html .= "<li id='list_{$menuItem->id}'><a>{$menuItem->title}
                          <span class='edit tip' title='Sửa' data-id='{$menuItem->id}' data-name='{$menuItem->title}' data-type='1'><img src='/Content/XMin/images/icon/icon_edit.png' /></span>
                          <span class='delete tip' title='Xóa' data-id='{$menuItem->id}' data-name='{$menuItem->title}'><img src='/Content/XMin/images/icon/icon_delete.png' /></span>
                       </a>";
            if (!empty($menuItem->subs)) {
                $html .= $this->_recursiveMenuMultiLevel($menuItem->subs);
            }
            $html .= "</li>";
        }
        $html .= "</ol>";
        return $html;
    }

    /**
     * Hàm đệ quy lấy ra danh mục đa cấp
     * @param array $categories Menu Multilevel
     */
    private function _recursiveMenuMultiLevel($menu)
    {
        $html = "<ol class='rectangle-list sortable'>";
        foreach ($menu as $menuItem)
        {
            $html .= "<li id='list_{$menuItem->id}'><a>{$menuItem->title}
                          <span class='edit tip' title='Sửa' data-id='{$menuItem->id}' data-name='{$menuItem->title}' data-type='1'><img src='/Content/XMin/images/icon/icon_edit.png' /></span>
                          <span class='delete tip' title='Xóa' data-id='{$menuItem->id}' data-name='{$menuItem->title}'><img src='/Content/XMin/images/icon/icon_delete.png' /></span>
                      </a>";
            if (!empty($menuItem->subs)) {
                $html .= $this->_recursiveMenuMultiLevel($menuItem->subs);
            }
            $html .= "</li>";
        }
        $html .= "</ol>";
        return $html;
    }
}