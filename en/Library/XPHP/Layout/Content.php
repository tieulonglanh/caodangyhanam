<?php
/**
 * Lớp xử lý content của layout
 * @author Mr.UBKey
 *
 */
class XPHP_Layout_Content
{
	/**
	 * Nội dung được lấy từ buffer
	 * @var string
	 */
	private $content;
	
	private $status;
	
	public function begin()
	{
		if($this->content)
			trigger_error('Chỉ có thể begin() một lần duy nhất.', E_USER_ERROR);
		
		//Mở output buffer
		ob_start();
		
		$this->status = true;
	}
	
	public function end()
	{
		if(!$this->status)
			trigger_error('Cần begin() trước khi end().', E_USER_ERROR);
			
		//Gán nội dung
        $this->content = ob_get_contents();
        
        //Xóa trắng bộ đệm
        ob_end_clean();
	}
	
	public function __toString()
	{
		if($this->content === NULl)
			$this->content = "";
			
		return $this->content;
	}
}