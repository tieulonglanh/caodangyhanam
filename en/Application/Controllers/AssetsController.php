<?php
class AssetsController extends XPHP_Controller {
	public function jsAction() {
		//Tên
		$name = isset ( $this->params ['name'] ) ? $this->params ['name'] : $this->params [0];
		
		//Lấy tên cache
		$cacheName = "{$name}.js";
		
		//Khởi tạo lớp hỗ trợ cache assets
		$cache = new XPHP_Cache_Simple ( '1y', false, "ast.", 'Cache/Simple' );
		
		if (isset ( $cache->$cacheName ))
			$js = $cache->$cacheName;
		else
			$js = "";
		
		//Thiết lập contentType
		header ( 'Content-Type: text/javascript' );
		
		//In ra javascript
		print $js;
	}
	
	public function cssAction() {
		//Tên
		$name = isset ( $this->params ['name'] ) ? $this->params ['name'] : $this->params [0];
		
		//Lấy tên cache
		$cacheName = "{$name}.css";
		
		//Khởi tạo lớp hỗ trợ cache assets
		$cache = new XPHP_Cache_Simple ( '1y', false, "ast.", 'Cache/Simple' );
		
		if (isset ( $cache->$cacheName )) {
			$css = $cache->$cacheName;
		} else
			$css = "";
		
		//Thiết lập contentType
		header ( 'Content-Type: text/css' );
		
		//In ra css
		print $css;
	}
	
	public function applicationAction()
	{
		
		$js = "applicationUrl = '" . XPHP_Url::getApplicationUrl() . "';";
		
		//Thiết lập contentType
		header ( 'Content-Type: text/javascript' );
		
		//In ra javascript
		print $js;
	}
	
	public function css3Action()
	{
	    header( 'Content-type: text/x-component' );
	    include('Content/Bla/css/PIE.htc');
	    die;
	}
	
	public function sessionAction()
	{
		header ( 'Content-Type: text/javascript' );
		$user = array('username' 	=> $this->session->member->username, 
					  'id'			=> (int)$this->session->member->bla_id,
					  'phpsession'  => session_id());
		echo 'user = ' . json_encode($user);
		
	}
	
	public function configAction()
	{
		$config = XPHP_Config::get('distributed');
		header ( 'Content-Type: text/javascript' );
		$user = array('static' 			=> $config->static,
					  'service'			=> $config->service);
		echo 'config = ' . json_encode($user);
	}

    public function captchaAction()
    {
        $captcha = new Standard_SimpleCaptcha();
        $captcha->width = 175;
        $captcha->height = 50;
        echo $captcha->CreateImage();
    }
}