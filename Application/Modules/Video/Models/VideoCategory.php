<?php
#[Table('video_category')]
#[PrimaryKey('id')]
class VideoCategory extends XPHP_Model
{
	public $id;
	
	#[Label('Tên')]
	#[Required(message = 'Tên là bắt buộc nhập')]
	public $name;
	
	#[Label('Sắp xếp')]
	public $sort;
	
	public function getCategories()
	{
		return $this->db->order_by('sort', 'asc')
						->get()
						->result();
	}
}