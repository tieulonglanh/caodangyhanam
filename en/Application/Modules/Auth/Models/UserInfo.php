<?php
/**
 * Lớp Model của User
 * @author Mr.UBKey
 */
#[Table('user_info')]
#[PrimaryKey('id')]
class UserInfo extends XPHP_Model
{
	public $id;
	
	public $bla_id;
	
	public $subscribe_expiretime;
	
	public $vip_expiretime;
	
	public $lastvisit;
	
	public $lastupdate;
	
	public $cover;
	
	public $received_mail;
	
	public $theme_id;
	
	public $role_id;
	
	/**
	 * Lấy ra thông tin BlaID thông qua id
	 * @param int $id
	 */
	public function getUserById($id)
	{
		$result = $this->db->where('id', $id)
						   ->get()
						   ->result();
		return isset($result[0]) ? $result[0] : false;
	}
	
	public function updateRole($role_id, $user_id=NULL)
	{
	    if($user_id === NULL)
	        $this->db->where('id', $this->id);
	    else
	        $this->db->where('id', $user_id);
	    $this->db->set('role_id', $role_id);
	    return $this->db->update();
	}
	
	public function getUserWithInfo($id)
	{
		$result = $this->db->join('user', 'user_info.bla_id = user.id')
						   ->where('user_info.id', $id)
				 		   ->select('user.*')
				 	 	   ->select('user_info.*')
				 	 	   ->select('user.id AS id')
				 		   ->get()
				 		   ->result();
		return isset($result[0]) ? $result[0] : false;
	}
}