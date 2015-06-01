<?php
/**
 * Lớp Model của User
 * @author Mr.UBKey
 */
#[Table('user')]
#[PrimaryKey('id')]
class User extends XPHP_Model
{
	public $id;
	
	public $xf_user_id;
	
	public $fullname;

	public $username;
	
	public $password;
	
	public $email;
	
	public $gender;
	
	public $birthdate;
	
	public $phone;
	
	public $yahoo;
	
	public $avatar;

    public $images;

    public $content;
	
	public $status;
	
	public $register;
	
	public $lastupdate;
	
	public $role_id;
	
	/**
	 * Kiểm tra xem tài khoản có người dùng chưa?
	 * @param string $username
	 */
	public function usernameExists($username)
	{
		$result = $this->db->where('username', $username)
						   ->get()
						   ->result();
		return isset($result[0]);
	}

	/**
	 * Kiểm tra email đã được sử dụng hay chưa
	 * @param string $email
	 */
	public function emailExists($email)
	{
		$result = $this->db->where('email', $email)
						   ->get()
						   ->result();
		return isset($result[0]);
	}

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

	/**
	 * Lấy ra thông tin BlaID thông qua email
	 * @param string $email
	 */
	public function getBlaIDByEmail($email)
	{
		$result = $this->db->where('email', $email)
						   ->get()
						   ->result();
		return isset($result[0]) ? $result[0] : false;
	}

	public function getLastInsertId()
	{
		return $this->db->insert_id();
	}

	public function authorize()
	{
		//Username stored lowercase
		$this->email = strtolower($this->email);
		$password = XPHP_String::md5WithSaltEncode($this->password, $this->email);
		//Kiểm tra thông tin người dùng
		$result = $this->db->where('email', $this->email)
						   ->where('password', $password)
						   ->where('status IS NOT NULL')
						   ->limit(1)
						   ->get()
						   ->result();
		if(sizeof($result) == 1)
		{
			$this->_user =  $result[0];
		}
		return !empty($this->_user);
	}

	/**
	 * Đối tượng chứa thông tin thành viên đã đăng nhập thành công
	 * @var mixed
	 */
	private $_user;

	/**
	 * Lấy ra user đã được xác thực
	 */
	public function getAuthorizeUser($id=NULL)
	{
	    if($id === NULL)
	        $id = $this->_user->id;
		$result = $this->db->join('user_info', 'user.id = user_info.bla_id')
						   ->where('user.id', $id)
						   ->select('user.*')
						   ->select('user_info.*')
						   ->select('user.id AS id')
						   ->get()
						   ->result();
		return isset($result[0]) ? $result[0] : false;
	}

	/**
	 * Cập nhật trạng thái online hoặc offline của người dùng
	 * @param bool $bool
	 * @param int $user_id
	 */
	public function setOnline($bool, $user_id = NULL)
	{
	    if($bool){
	        $this->db->set('lastvisit', date('Y-m-d H:i:s'))
	        		 ->set('online', 1);
	    }
	    else{
	        $this->db->set('lastvisit', date('Y-m-d H:i:s'))
	        		 ->set('online', 0);
	    }
	    if($user_id === NULL)
	        $this->db->where('id', $this->id);
	    else
	        $this->db->where('id', $user_id);
	    return $this->db->update();
	}

	/**
	 * Cập nhật lại user facebook
	 * @param int $userFacebookId
	 */
	public function updateFacebookId($id, $userFacebookId)
	{
	    return $this->db->set('facebook', $userFacebookId)
	                    ->where('id', $id)
	                    ->update();
	}

	/**
	 * Cập nhật lại sub id
	 * @param string $subId
	 */
	public function updateSubId($id, $subId)
	{
	    return $this->db->set('sub_id', $subId)
                	    ->where('id', $id)
                	    ->update();
	}

    public function xfUserExists($xf_user_id)
    {
        $result = $this->db->where('xf_user_id', $xf_user_id)
                           ->get()
                           ->result();
        return isset($result[0]) ? $result[0] : false;
    }
}