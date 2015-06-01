<?php
#[Table('role_permission')]
#[PrimaryKey('id')]
class RolePermission extends XPHP_Model
{
	public $id;
	
	public $permission_id;
	
	public $role_id;
	
	/**
	 * Xoa toan bo danh sach cac permission cua role
	 * @param int $role_id
	 */
	public function deleteAllPermissionOfRole($role_id)
	{
		return $this->db->where('role_id', $role_id)
						->delete();
	}
}