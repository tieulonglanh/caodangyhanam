<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mc721
 * Date: 11/3/12
 * Time: 11:55 PM
 * To change this template use File | Settings | File Templates.
 */
#[Table('navigation_node')]
class NavigationNode extends XPHP_Model
{
    public function getNodes()
    {
        return $this->db->get()
                        ->result();
    }

    /**
     * Lấy ra cây menu từ danh mục gốc
     * @return array
     */
    public function getNodeTree ($parent_id = 0)
    {
        return $this->_getNodeTree($parent_id);
    }
    /**
     * Node sắp xếp hiển thị theo dạng cây
     * @var string
     */
    private $_nodeList = array();
    /**
     * Đánh dấu cấp độ của node
     * @var int
     */
    private $_nodeLevel = 1;
    /**
     * Phương thức đệ quy lấy ra node dạng cây
     * @param int $parent_id
     */
    private function _getNodeTree ($parent_id)
    {
        //Lấy ra toàn bộ danh mục menu con của root
        $nodes = $this->db->where('parent_id', $parent_id)
                          ->order_by('id', 'asc')
                          ->get()
                          ->result();
        //Dấu gạch biểu thị cấp độ
        $prefix = "";
        for ($i = 0; $i < $this->_nodeLevel; $i ++) {
            $prefix .= " ------ ";
        }
        if (count($nodes) > 0) {
            foreach ($nodes as $n) {
                $n->title = $prefix . $n->title;
                $this->_nodeList[] = $n;
                if ($this->countSubNode($n->id)) {
                    $this->_nodeLevel ++;
                    $this->_getNodeTree($n->id);
                    $this->_nodeLevel --;
                }
            }
        }
        return $this->_nodeList;
    }

    /**
     * Lấy ra node đa cấp
     * @return array
     */
    public function getNodeMultiLevel ($parent_id = 0)
    {
        return $this->_getNodeMultiLevel($parent_id);
    }
    /**
     * Phương thức đệ quy lấy ra menu đa cấp
     * @param int $parent_id
     */
    private function _getNodeMultiLevel ($parent_id)
    {
        //Lấy ra toàn bộ menu item của danh mục parent
        $menu = $this->db->where('parent_id', $parent_id)
                         ->order_by('id', 'asc')
                         ->get()
                         ->result();
        if (count($menu) > 0)
        {
            for ($i = 0; $i < count($menu); $i ++)
            {
                if ($this->countSubMenu($menu[$i]->id))
                {
                    $menu[$i]->subs = $this->_getNodeMultiLevel($menu[$i]->id);
                }
            }
        }
        return $menu;
    }

    /**
     * Phương thức lấy ra số danh mục con của một danh mục
     * @param int $parent_id
     */
    public function countSubMenu ($parent_id)
    {
        //Lấy ra toàn bộ danh mục sản phẩm con của danh mục parent
        $node = $this->db->where('parent_id', $parent_id)
                         ->get()
                         ->result();
        return count($node);
    }
}
