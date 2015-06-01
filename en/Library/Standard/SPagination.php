<?php
require_once 'Standard/SPagination/spagination.php';

class Standard_SPagination extends XPHP_Widget_Abstract
{
	/**
	 * XPHP_DataSource
	 * @var XPHP_DataSource
	 */
	public $dataSource;
	
	/**
	 * Spagination
	 * @var Spagination
	 */
	public $spagination;
	
	public function init()
	{
		//Unset datasoure từ options
		if(isset($this->option['dataSource']))
			unset($this->option['dataSource']);
		$this->spagination = Spagination::create();
		//Gan cac cau hinh
		foreach ($this->option as $k => $v)
		{
			$this->spagination->$k($v);
		}
		//Kiem tra datasource
		if($this->dataSource)
		{
			$this->spagination->count($this->dataSource->count);
			$this->spagination->limit($this->dataSource->pageSize);
			$this->spagination->current_page($this->dataSource->page);
		}
	}
	
	public function render()
	{
		//Asset
		$asset = new XPHP_Asset();
		//Assets css
		if(! $asset->hasAsset("SPagination", 'css'))
		{
			$asset->name("SPagination")->loadCss(array("Library/Standard/SPagination/css/sp.css"));
			$asset->getCss();
		}
		echo $this->spagination->render();
	}
}