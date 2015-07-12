<?php
/**
 * Lớp hỗ trợ cache ra file trong PHP
 * @author Mr.UBKey
 */

require_once 'XPHP/Cache/Abstract.php';

class XPHP_Cache_Simple extends XPHP_Cache_Abstract
{
    public function __construct($time=NULL, $compresses=NULL, $prefix=NULL, $path=NULL)
    {
    	//Load cấu hình trong file config với node cache > simple
		require_once 'XPHP/Config.php';
		XPHP_Config::load($this, 'cache > simple');

		//Lấy các giá trị được cấu hình từ config
		if($path !== NULL)
			$this->path = $path;
		else if(!$this->path)
			$this->path = "Cache/Simple";
		//Kiểm tra đương dẫn
		if(!is_dir($this->path))
		{
			require_once 'XPHP/Exception.php';
			throw new XPHP_Exception("Đường dẫn tới thư mục chứa Simple Cache không chính xác: " . $path);
		}	
		
		if($time !== NULL)
			$this->time = $this->convertCacheTime($time); 
		else if(!$this->time)
			$this->time = $this->convertCacheTime("10m");
			
		if ($prefix !== NULL)
			$this->prefix = $prefix;
		else if($this->prefix)
			$this->prefix = "xcs_";
			
		if ($compresses !== NULL)
			$this->compresses = $compresses;
		else if(!$this->compresses)
			$this->compresses = false;
    }
    
    /**
     * Xóa cache
     * @param string $key
     */
    public function __unset($key)
    {
    	$filename_cache = $this->getCacheFileName($key); //Tên file cache  
    	$filename_info = $this->getInfoFileName($key); //File chứa thông tin cache
    	//Xóa file cache và file info
    	if(is_file($filename_cache))
    		unlink($filename_cache);
    	if(is_file($filename_info))
    		unlink($filename_info);
    }
    
    /**
     * Kiểm tra sự tồn tại của cache
     * @param string $key
     */
	public function __isset($key)
	{
		$filename_cache = $this->getCacheFileName($key); //Tên file cache  
    	$filename_info = $this->getInfoFileName($key); //File chứa thông tin cache  
	  
	    if (file_exists($filename_cache) && file_exists($filename_info))  
	    {  
	        $cache_time = file_get_contents ($filename_info) + (int)$this->getTime(); //Lần update cuối cùng của file cache
	        $time = time(); //Thời gian hiện tại   
	  
	        $expiry_time = (int)$time; //Thời gian hết hạn của file cache  
	  
	        if ((int)$cache_time >= (int)$expiry_time) //So sánh thời gian cuối cùng cập nhật và thời gian hết hạn   
	        {  
	            return true;  
	        }  
	    }  
	  
	    return false;  
	}
	
	/**
	 * Lấy giá trị cache
	 * @param string $key
	 */
	public function __get($key)
	{
		$filename_cache = $this->getCacheFileName($key); //Tên file cache  
    	$filename_info = $this->getInfoFileName($key); //File chứa thông tin cache  
	  
	    if (file_exists($filename_cache) && file_exists($filename_info))  
	    {  
	        $cache_time = file_get_contents ($filename_info) + (int)$this->getTime(); //Lần update cuối cùng của file cache
	        $time = time(); //Thời gian hiện tại 
	  
	        $expiry_time = (int)$time; //Thời gian hết hạn của file cache
	  
	        if ((int)$cache_time >= (int)$expiry_time) //So sánh thời gian cuối cùng cập nhật và thời gian hết hạn 
	        {  
	            return unserialize(file_get_contents ($filename_cache));   //Lấy ra nội dung cache 
	        }  
	    }  
	  
	    return null;  
	}
	
	/**
	 * Gán giá trị cache
	 * @param string $key
	 * @param other $value
	 */
	public function __set($key, $value)
	{
 		$time = time(); //Lấy ra thời gian hiện tại
  
		if (! file_exists($this->getPath()))  
			mkdir($this->getPath());  
  
		$filename_cache = $this->getCacheFileName($key); //Tên file cache  
    	$filename_info = $this->getInfoFileName($key); //File chứa thông tin cache 
  
    	file_put_contents ($filename_cache ,  serialize($value)); //Lưu nội dung cache
    	file_put_contents ($filename_info , $time); //Lưu thời gian cache lần cuối cùng vào file  
	}
	
	/**
	 * Lấy ra tên file cache
	 */
	public function getCacheFileName($key)
	{
		return $this->getPath() . DIRECTORY_SEPARATOR . $this->getPrefix() . md5($key) . '.cache';
	}
	
	/**
	 * Lấy ra tên file info cache
	 */
	public function getInfoFileName($key)
	{
		return $this->getPath() . DIRECTORY_SEPARATOR . $this->getPrefix() . md5($key) . '.info';
	}
}