<?php
#[Table('role')]
#[PrimaryKey('id')]
class Role extends XPHP_Model
{
    public $id;
    
    #[Label('Tên')]
    #[Required(message = 'Tên là bắt buộc nhập')]
    #[Command(update = false)]
    public $name;
    
    #[Label('Tên hiển thị')]
    #[Required(message = 'Tên hiển thị là bắt buộc nhập')]
    public $title;
    
    #[Label('Mô tả')]
    public $description;
    
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
     * Lay ra danh sach tat ca cac role
     */
    public function getRoles()
    {
    	return $this->db->get()
    					->result();
    }
    
    /**
     * Kiem tra xem co ten role nay hay khong
     */
    public function hasName($name)
    {
    	$result =  $this->db->where('name', $name)
					    	->get()
					    	->result();
    	return isset($result[0]);
    }
    
    public function getByName($name)
    {
        $result =  $this->db->where('name', $name)
					    	->get()
					    	->result();
    	return $result[0];
    }
}