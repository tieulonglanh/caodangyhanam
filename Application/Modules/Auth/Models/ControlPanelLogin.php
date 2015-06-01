<?php
/**
 * Lớp Model của đăng nhập
 * @author Mr.UBKey
 */
#[Table('user')]
#[PrimaryKey('id')]
class ControlPanelLogin extends XPHP_Model
{
    #[Label('Tên đăng nhập')]
    #[Required(message = 'Tên đăng nhập không được để trống')]
    #[RangeLength(min = 5, max = 20, message = 'Tên đăng nhập có 5 ~ 20 kí tự')]
    public $username;
    
    #[Label('Mật khẩu')]
    #[Required(message = 'Mật khẩu không được để trống')]
    #[RangeLength(min = 6, max = 50, message = 'Tên đăng nhập có 6 ~ 50 kí tự')]
    public $password;
    
    #[Label('Ghi nhớ')]
    public $remember;

    public function authorize()
    {
        //Username stored lowercase
        $this->username = strtolower($this->username);
        //Encode password
        $password = XPHP_String::md5Encode($this->password . $this->username);
        //Kiểm tra thông tin người dùng
        $result    =    $this->db->where('username', $this->username)
                                 ->where('password', $password)
                                 ->where('status IS NOT NULL')
                 		         ->limit(1)
                 		         ->get()
                 		         ->result();
        if(count($result) == 1)
        {
            $this->_user =  $result[0];
            return true;
        }
        return false;
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
        $result = $this->db->where('user.id', $id)
                           ->select('user.*')
                           ->select('user.id AS id')
                           ->get()
                           ->result();
        $this->_user = isset($result[0]) ? $result[0] : false;
        return $this->_user;
    } 
}