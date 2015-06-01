<?php
class Standard_List extends XPHP_Widget_Abstract
{
	/**
	 * XPHP_DataSource
	 * @var XPHP_DataSource
	 */
	public $dataSource;
	
	/**
	 * Dữ liệu hiển thị
	 * @var array
	 */
	public $data;
	
	public function init()
	{
		if($this->dataSource)
			$this->data = $this->dataSource->getData();
	}
	
	public function render()
	{
		$html = "";
		foreach ($this->data as $index => $row)
		{
			if(is_object($row))
				$row = get_object_vars($row);
			extract($row);
			$phpCode = 'return "' . htmlentities($this->content) . '";';
			$html .= eval($phpCode);
		}
		
		echo html_entity_decode($html);
	}
}