<?php
/**
 * Lớp hỗ trợ cache phía frontend
 * @author Mr.UBKey
 */

require_once 'XPHP/Cache/Abstract.php';

class XPHP_Cache_Frontend extends XPHP_Cache_Abstract
{   
	/**
	 * Đường dẫn đầy đủ tới file cache
	 * @var string
	 */   
	private $file;
	
	public function __construct($time=NULL, $compresses=NULL, $prefix=NULL, $path=NULL)
    {
    	//Load cấu hình trong file config với node cache > frontend
		require_once 'XPHP/Config.php';
		XPHP_Config::load($this, 'cache > frontend');
		
		//Lấy các giá trị được cấu hình từ config
		if($path !== NULL)
			$this->path = $path;
		else if(!$this->path)
			$this->path = "Cache/Frontend";
		//Kiểm tra đương dẫn
		if(!is_dir($this->path))
		{
			require_once 'XPHP/Exception.php';
			throw new XPHP_Exception("Đường dẫn tới thư mục chứa Frontend Cache không chính xác: " . $path);
		}	
		
		if($time !== NULL)
			$this->time = $this->convertCacheTime($time); 
		else if(!$this->time)
			$this->time = $this->convertCacheTime("10m");
			
		if ($prefix !== NULL)
			$this->prefix = $prefix;
		else if(!$this->prefix)
			$this->prefix = "xcf_";
			
		if ($compresses !== NULL)
			$this->compresses = $compresses;
		else if(!$this->compresses)
			$this->compresses = false;
    }
    
    /**
     * Mở ra một cache
     * @param string $cache_id Định danh cache
     * @param int $expire
     */
    public function begin($cache_id, $expire = NULL) 
    { 
        //Nếu một begin file cache mà chưa đóng, throw ra lỗi
        if ($this->file) 
        	trigger_error('Bạn phải đóng phiên end() trước khi bắt đầu begin() cache mới.', E_USER_ERROR); 
     	
        $this->file = $this->getFileName($cache_id);

        //Kiểm tra nếu có tham số truyền vào
        //Nếu chuỗi xử lý lấy ra time và nếu là số gán vào time
        if(is_integer($expire)) 
        	$this->setTime($expire); 
        else if(is_string($expire))
        	$this->setTime($this->convertCacheTime($expire));
         
        //Nếu có file cache 
        if (is_readable($this->file)) 
        { 
            if (!$this->cacheExpired()) 
            { 
                //Hiển thị nội dung file cache 
                echo file_get_contents($this->file); 
                
                return false; 
            } 
        } 
         
        ob_start(); 
        
        return true; 
    } 
     
    /**
     * Kết thúc một cache
     */
    public function end() 
    {    
        //Nếu chưa begin đưa ra thông báo lỗi
        if (!$this->file)
        	trigger_error('Bạn phải sử dụng begin() để mở buffer trước khi end().' , E_USER_ERROR); 

        //Tạo file cache 
        if ($this->cacheExpired()) { 
            $file = fopen($this->file, 'c'); 
            if (flock($file, LOCK_EX)) { 
                ftruncate($file, 0); 
                fwrite($file, ob_get_flush()); 
            } else { 
                ob_flush(); 
            } 
            fclose($file); 
        } 
         
        //Xóa thuộc tính tên file để có thể begin cache khác 
        $this->file = null; 
    } 
    
    /**
     * Kiểm tra xem cache hết hạn hay chưa
     * Nếu không có file xem như là cache hết hạn
     */
    private function cacheExpired() 
    {
    	if(is_file($this->file))
	        return time() - filemtime($this->file) > $this->getTime(); 
	    else
	    	return true;
    }
    
    /**
     * Lấy ra tên file cache hiện tại
     * @param string $cache_id
     */
    public function getFileName($cache_id)
    {
    	return $this->getPath() . DIRECTORY_SEPARATOR . $this->getPrefix() . md5($cache_id);
    }

    /**
     * Xóa file cache
     * @param string $cache_id
     */
    public function remove($cache_id)
    {
    	unlink($this->getFileName($cache_id));
    }
} 