<?php
/**
 * Lớp hỗ trợ Output Cache
 * @author Mr.UBKey
 */

require_once 'XPHP/Cache/Abstract.php';

class XPHP_Cache_Output extends XPHP_Cache_Abstract
{
	/**
	 * Thể hiện của XPHP_Cache_Ouput
	 * @var XPHP_Cache_Ouput
	 */
	public static $instance;
	
	/**
	 * Router của hệ thống
	 * Được set mặc định
	 * @var XPHP_Router
	 */
	private $_router;
	
	/**
	 * Gọi phương thức display() Dừng xử lý nếu tìm thấy file cache và valid
	 * Mở output buffering và tạo file cache
	 * ob_start('$this->cache');
	 * @param string $time Thời gian cache VD: 10s, 10m, 10h
	 */
	public function __construct($time=NULL, $compresses=NULL, $prefix=NULL, $path=NULL)
	{
		//Load cấu hình trong file config với node cache > output
		require_once 'XPHP/Config.php';
		XPHP_Config::load($this, 'cache > output');
		
		//Lấy các giá trị được cấu hình từ config
		if($path !== NULL)
			$this->path = $path;
		else if(!$this->path)
			$this->path = "Cache/Output";
		//Kiểm tra đương dẫn
		if(!is_dir($this->path))
		{
			require_once 'XPHP/Exeption.php';
			throw new XPHP_Exception("Đường dẫn tới thư mục chứa Output Cache không chính xác: " . $path);
		}	
		
		if($time !== NULL)
			$this->time = $this->convertCacheTime($time); 
		else if(!$this->time)
			$this->time = $this->convertCacheTime("10m");
			
		if ($prefix !== NULL)
			$this->prefix = $prefix;
		else if(!$this->prefix)
			$this->prefix = "xco_";
			
		if ($compresses !== NULL)
			$this->compresses = $compresses;
		else if(!$this->compresses)
			$this->compresses = false;
	}

	/**
	 * Lấy ra tên file cache
	 */
	public function getFileName()
	{
		return $this->getPath() . DIRECTORY_SEPARATOR . $this->getPrefix() . $this->getUri();
	}

	/**
	 * Hiển thị nội dung trong file cache nếu tồn tại cache và chưa hết thời gian quy định
	 * @param bool $exit Thoát chương trình sau khi hiển thị output cache (Mặc định thoát)
	 * @return bool Có cache hay không
	 */
	public function displayCache($exit = true)
	{
		$file = $this->getFileName();

		//Kiểm tra xem file cache có tồn tại hay không?
		if(!file_exists($file)) return false;
				
		//Kiểm tra file cache có quá thời gian không?
		if(filemtime($file) < time() - $this->getTime()) 
			return false;

		//Sau khi kiểm tra thì hiển thị thông tin file cache với dạng nén gz
		echo file_get_contents($file);//gzuncompress(file_get_contents($file));

		//Dừng chương trình
		if($exit)
    		exit;
        else
            return true;
	}

	/**
	 * Ghi dữ liệu cache vào file
	 * @param string $content
	 */
	public function startCache($content)
	{
		$file = $this->getFileName();
		if(false !== ($f = @fopen($file, 'w'))) 
		{
			fwrite($f, gzcompress($content));	
			fclose($f);
		}
    	return $content;
	}
	
	public static function cache($content)
	{
		//Chuyển thư mục xử lý của script về thư mục gốc của site khi ob_start gọi callback
		chdir(dirname($_SERVER['SCRIPT_FILENAME']));
		//Lấy ra thể hiện của lớp
		$ocInstance = XPHP_Cache_Output::getInstance();
		//Lấy ra đường dẫn và tên file cache
		$file = $ocInstance->getFileName();
		//Mở và ghi file cache
		if(false !== ($f = @fopen($file, 'w'))) 
		{
			@fwrite($f, $content);//gzcompress($content)); 
			@fclose($f);
		}
		//Trả về nội dung cho buffer
    	return $content;
	}
	
	public function remove($path=null)
	{
		$file = $this->getFileName() . md5($path);
		if(file_exists($file)) unlink($file) or die("Không thể xóa file cache: $file!");
	}
	
	/**
	 * Gán router hệ thống
	 * @var XPHP_Router $router
	 */
	public function setRouter($router)
	{
		$this->_router = $router;
	}
	
	public function getUri()
	{
		if($this->_router)
		{
			$uri = "";
			if($this->_router->module)
				$uri = $this->_router->module . "_";
			$uri .= $this->_router->controller . "_";
			$uri .= $this->_router->action;
			//Lấy ra các args
    	    $uri .= implode("_", $this->_router->args);
		}
		else
			$uri = trim($_SERVER['REQUEST_URI'], '/');
		return $uri;
	}
	
	/**
	 * Lấy ra thể hiện của lớp OuputCache
	 * @return XPHP_Cache_Output
	 */
	public static function getInstance($time=NULL, $compresses=NULL, $prefix=NULL, $path=NULL)
	{
		if(self::$instance != null)
			return self::$instance;
		else
		{
			self::$instance = new XPHP_Cache_Output($time=NULL, $compresses=NULL, $prefix=NULL, $path=NULL);
			return self::$instance;
		}
		
	}
	
	/**
	 * Gán thể hiện của lớp XPHP_Cache_Output
	 * @param XPHP_Cache_Output $instance
	 */
	public static function setInstance(XPHP_Cache_Output $instance)
	{
		self::$instance = $instance;
	}
	
}