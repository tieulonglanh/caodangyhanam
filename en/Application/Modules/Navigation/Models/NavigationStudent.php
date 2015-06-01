<?php
/**
 * Lớp model của controlpanel navigation
 */
#[Table('navigation_st')]
#[PrimaryKey('id')]
class NavigationStudent extends XPHP_Model
{
    public function getNavList ()
    {
        return $this->_getNavListTree(0);
    }
    private function _getNavListTree ($parentId)
    {
        $navs = $this->db->where('parent_id', $parentId)
			             ->order_by("sort", "asc")
			             ->get()
			             ->result();
        foreach ($navs as $nav) {
            if ($this->_countSubNav($nav->id) > 0) {
                $nav->subs = $this->_getNavListTree($nav->id);
            }
        }
        return $navs;
    }
    private function _countSubNav ($id)
    {
        $result = $this->db->where('parent_id', $id)
				           ->get()
				           ->result();
        return count($result);
    }
}