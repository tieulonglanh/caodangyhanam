<?php
#[Table('slide')]
#[PrimaryKey('id')]
class Slide extends XPHP_Model
{
	public $id;

	#[Label('Tên slide')]
	public $name;
	
	#[Label('Mô tả slide')]
	public $brief;
	
	#[Label('Tiêu đề')]
	public $title;
	
	#[Label('Chú thích')]
	public $description;
	
	#[Label('Ảnh')]
	public $image;
	
	#[Label('Liên kết')]
	public $link;
	
	#[Label('Chữ hiển thị')]
	public $html;
	
	#[Label('Sắp xếp')]
	public $sort;
	
	#[Label('Loại slide')]
	public $type;
	
	/**
	 * Lấy ra danh sách các slides theo type
	 * @param $type int 
	 * @return array
	 */
	public function getSlides($type=NULL)
	{
		if($type)
		{
			$this->db->where('type', $type);		
		}
		return $this->db->get()
						->result();
	}
}