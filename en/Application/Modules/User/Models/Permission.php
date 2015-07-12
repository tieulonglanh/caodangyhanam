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

	public function getPermissions()
	{
		return $this->db->order_by('module', 'asc')
						->get()	
						->result();
	}
	
	/**
	 * Lay ra danh sach cac permission cua role
	 * @param string $role_id
	 */
	public function getPermissionOfRole($role_id)
	{
		return $this->db->select('permission.*')
					->join('role_permission', 'permission.id = role_permission.permission_id')
					->join('role', 'role_permission.role_id = role.id')
					->where('role.id', $role_id)
					->get()
					->result();
	}
}