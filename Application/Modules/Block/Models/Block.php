<?php
#[Table('block')]
#[PrimaryKey('id')]
class Block extends XPHP_Model
{
	public $id;
	
	#[Label('Tên')]	
	public $name;
	
	#[Label('Mô tả')]
	public $brief;
	
	#[Label('Nội dung')]
	public $html;

    #[Label('Trạng thái')]
    public $status;
	
	/**
	 * Kiểm tra có tồn tại blockhay không
	 * @param string $name
	 */
	public function blockExists($name=NULL)
	{
		if($name)
			$this->db->where('name', $name);
		else 
			$this->db->where('name', $this->name);
		
		$result = $this->db->get()
						   ->result();
		return isset($result[0]);
	}
	
	public function getByName($name)
	{
		$result = $this->db->where('name', $name)
                            ->where('status', 1)
						   ->get()
						   ->result();
		if(isset($result[0]))
		{
			$block = $result[0];
			$this->id = $block->id;
			$this->name = $block->name;
			$this->brief = $block->brief;
			$this->html = $block->html;
		}
	}
}