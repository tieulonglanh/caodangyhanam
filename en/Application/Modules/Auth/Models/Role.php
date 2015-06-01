<?php
#[Table('role')]
#[PrimaryKey('id')]
class Role extends XPHP_Model
{
    public $id;
    
    public $name;
    
    public $title;
    
    public $description;
    
    public $parent_role_id;
    
    /**
     * Lấy ra role của người dùng
     * @param int $id
     */
    public function getRole($id)
    {
        $result =  $this->db->where('id', $id)
                            ->get()
                            ->result();
        return isset($result[0]) ? $result[0] : false;
    }
    
    /**
     * Kiểm tra xem có role id này không ?
     * @param int $id
     */
    public function hasRole($id)
    {
        if($id === null)
            return false;
        $result =  $this->db->where('id', $id)
                            ->get()
                            ->result();
        return isset($result[0]);
    }
    
    /**
     * Lấy ra danh sách tất cả các role cha
     * @param int $role_id
     * @return array
     */
    public function getParentRole($role_id)
    {
    	$this->_getParentRole($role_id);
    	return $this->_parentRole;
    }
    
    private $_parentRole = array();
    
    private function _getParentRole($role_id)
    {
    	$result =  $this->db->where('id', $role_id)
					    	->get()
					    	->result();
    	if(isset($result[0]))
    	{
    		if(!empty($result[0]->parent_role_id))
    		{
    			$this->_parentRole[] = $result[0]->parent_role_id;
    			$this->_getParentRole($result[0]->parent_role_id);
    		}
    	}
    }
}