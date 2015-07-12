<?php
/**
 * Lớp xử lý section của layout
 * @author Mr.UBKey
 */
class XPHP_Layout_Section
{
	/**
	 * Mảng chứa sesion và dữ liệu của section
	 * array('section' => data)
	 * @var array
	 */
	public $section;
	
	/**
	 * Tên section
	 * @var string
	 */
	public $name;
	
	/**
	 * Mở ra một section
	 * @param string $name
	 */
	public function begin($name)
	{
        //Nếu một section mà chưa đóng, throw ra lỗi
        if ($this->name) 
        	trigger_error('Phải đóng phiên end() trước khi bắt đầu begin() section mới.', E_USER_ERROR); 

        $this->name = $name;
        	
        ob_start(); 
        
        return true; 
	}
	
	/**
	 * Kết thúc một section
	 */
	public function end()
	{
        //Nếu chưa begin đưa ra thông báo lỗi
        if (!$this->name)
        	trigger_error('Phải sử dụng begin() để mở section trước khi end().' , E_USER_ERROR); 

        //Tạo section
        $this->section[$this->name] = ob_get_contents();
        
        //Xóa trắng bộ đệm
        ob_end_clean();
         
        //Xóa thuộc tính tên file để có thể begin section khác 
        $this->name = null; 
	}
	
	/**
	 * Lấy ra section
	 * @param string $name
	 */
	public function __get($name)
	{
		if(isset($this->section[$name]))
			return $this->section[$name];
		else
			return "";
	}
}