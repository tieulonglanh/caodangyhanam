<?php
/**
 * Lớp hỗ trợ Controller
 * @author Mr.UBKey
 *
 */
class XPHP_Controller_Helper
{
	/**
	 * Router của XPHP_Controller
	 * @var XPHP_Router
	 */
	public $router;
	
	/**
	 * Hỗ trợ Session
	 * @var XPHP_Session
	 */
	public $session;
	
	/**
	 * Hỗ trợ cookie
	 * @var XPHP_Cookie
	 */
	public $cookie;
	
	public function __construct($router)
	{
		//Gán giá trị vào router
		$this->router = $router;
		//Khởi tạo lớp hỗ trợ Session
		$this->session = XPHP_Session::getInstance();
		//Khởi tạo lớp hỗ trợ Cookie
		$this->cookie = new XPHP_Cookie();
	}
}