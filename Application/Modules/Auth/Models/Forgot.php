<?php
#[Table('user_forgot')]
#[PrimaryKey('id')]
class Forgot extends XPHP_Model
{
    public $id;
    
    #[Label('Địa chỉ email')]
    #[Required(message='Mời bạn nhập vào địa chỉ email')]
	#[Email(message='Địa chỉ email chưa chính xác')]
    public $email;
    
    public $username;
    
    public $token;
    
    public $expiretime;
    
    public $created_date;
    
    public function getForgotByToken()
    {
        $result = $this->db->where('token', $this->token)
                           ->get()
                           ->result();
        return isset($result[0]) ? $result[0] : false;
    }
    
    public function deleteByToken()
    {
        return $this->db->where('token', $this->token)
                        ->delete();
    }
}