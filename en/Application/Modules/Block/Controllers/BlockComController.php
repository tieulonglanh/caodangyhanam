<?php
class BlockComController extends XPHP_Controller
{
	public function indexAction()
	{

		//Lấy ra tên block
		$name = $this->params[0];
		$block = new Block();
		$block->getByName($name);
		echo $block->html;
	}
}