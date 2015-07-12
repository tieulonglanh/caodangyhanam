<?php
/**
 * Lớp model của navigation
 */
#[Table('navigation')]
#[PrimaryKey('id')]
class Navigation extends XPHP_Model
{
    public $id;
    #[Label('Tiêu đề')]
    #[Required(message = 'Tiêu đề là bắt buộc nhập')]
    #[MaxLength(100, message = 'Tiêu đề có tối đa 100 kí tự')]
    public $title;
    #[Label('Mô tả')]
    public $description;
    #[Label('Danh mục cha')]
    public $parent_id;
    #[Label('Sắp xếp')]
    #[Required(message = 'Sắp xếp là bắt buộc nhập')]
    public $sort;
    #[Label('Đường dẫn')]
    public $url;
    #[Label('Phân loại')]
    public $type;
    #[Label('Giao điểm')]
    public $node;
    #[Label('Điểm liên kết')]
    public $value;
    public $lang_id;
    /**
     * Lấy ra cây menu từ danh mục gốc
     * @return array
     */
    public function getMenuTree ($parent_id = 0, $type)
    {
        return $this->_getMenuTree($parent_id, $type);
    }
    /**
     * Danh mục category sắp xếp hiển thị theo dạng cây
     * @var string
     */
    private $_menuList = array();
    /**
     * Đánh dấu cấp độ của danh mục
     * @var int
     */
    private $_menuLevel = 1;
    /**
     * Phương thức đệ quy lấy ra danh mục tin tức dạng cây
     * @param int $parent_id
     */
    private function _getMenuTree ($parent_id, $type)
    {
        //Lấy ra toàn bộ danh mục menu con của root
        $menu = $this->db->where('parent_id', $parent_id)
                         ->where('type', $type)
                         ->order_by('sort', 'asc')
                         ->get()
                         ->result();
        //Dấu gạch biểu thị cấp độ
        $prefix = "";
        for ($i = 0; $i < $this->_menuLevel; $i ++) {
            $prefix .= " ------ ";
        }
        if (count($menu) > 0) {
            foreach ($menu as $menuItem) {
                $menuItem->title = $prefix . $menuItem->title;
                $this->_menuList[] = $menuItem;
                if ($this->countSubMenu($menuItem->id)) {
                    $this->_menuLevel ++;
                    $this->_getMenuTree($menuItem->id, $type);
                    $this->_menuLevel --;
                }
            }
        }
        return $this->_menuList;
    }
    /**
     * Phương thức lấy ra số danh mục con của một danh mục
     * @param int $parent_id
     */
    public function countSubMenu ($parent_id)
    {
        //Lấy ra toàn bộ danh mục sản phẩm con của danh mục parent
        $menu = $this->db->where('parent_id', $parent_id)
                         ->get()
                         ->result();
        return count($menu);
    }
    /**
     * Lấy ra menu đa cấp
     * @return array
     */
    public function getMenuMultiLevel ($parent_id = 0, $type)
    {
        return $this->_getMenuMultiLevel($parent_id, $type);
    }
    /**
     * Phương thức đệ quy lấy ra menu đa cấp
     * @param int $parent_id
     */
    private function _getMenuMultiLevel ($parent_id, $type)
    {
        //Lấy ra toàn bộ menu item của danh mục parent
        $menu = $this->db->where('parent_id', $parent_id)
            ->where('type', $type)
            ->order_by('sort', 'asc')
            ->get()
            ->result();
        if (count($menu) > 0) {
            for ($i = 0; $i < count($menu); $i ++) {
                if ($this->countSubMenu($menu[$i]->id)) {
                    $menu[$i]->subs = $this->_getMenuMultiLevel($menu[$i]->id, 
                    $type);
                }
            }
        }
        return $menu;
    }

    public function getAllMenu() {
        $menu = $this->db
            ->order_by('sort', 'asc')
            ->get()->result();
        return $menu;
    }
}