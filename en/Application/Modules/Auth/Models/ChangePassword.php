<?php
#[Table('user')]
#[PrimaryKey('id')]
class ChangePassword extends XPHP_Model
{
    public $username;
    
    public $token;
    
    #[Label('Mật khẩu mới')]
    #[Required(message = 'Mật khẩu mới không được để trống')]
    #[RangeLength(min = 6, max = 50, message = 'Mật khẩu mới có từ 6 ~ 50 kí tự')]
    public $newPassword;
    
    #[Label('Gõ lại mật khẩu')]
    #[Required(message = 'Gõ lại mật khẩu không được để trống')]
    #[RangeLength(min = 6, max = 50, message = 'Gõ lại mật khẩu có từ 6 ~ 50 kí tự')]
    public $retypeNewPassword;

    public function change()
    {
        $this->db->set('password', XPHP_String::md5Encode($this->newPassword))
                 ->where('username', $this->username);
        return $this->db->update();                    
    }
}