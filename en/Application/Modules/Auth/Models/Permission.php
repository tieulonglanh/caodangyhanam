<?php
#[Table('permission')]
#[PrimaryKey('id')]
class Permission extends XPHP_Model
{
    public $id;
    
    public $name;
    
    public $module;
    
    public $controller;
    
    public $action;
    
    /**
     * Lấy ra danh sách các quyền của role
     * @param int $roleId
     */
    public function getPermissionOfRole($role_id)
    {
    	$role = new Role();
    	$roles = $role->getParentRole($role_id);
    	$roles = array_merge(array($role_id), $roles);
        return $this->db->select('permission.*')
                        ->join('role_permission', 'permission.id = role_permission.permission_id')
                        ->join('role', 'role_permission.role_id = role.id')
                        ->group_by('permission.id')
                        ->where_in('role_permission.role_id', $roles)
                        ->get()
                        ->result();
    }
}