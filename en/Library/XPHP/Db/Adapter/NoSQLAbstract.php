<?php
/**
 * Lớp abstract của XPHP_Db_Adapter_XXXXX
 * @author Mr.UBKey
 */

require_once 'XPHP/Db/ActiveRecord.php';

abstract class XPHP_Db_Adapter_NoSQLAbstract
{
	/**
	 * Adapter điều khiển, giao tiếp các cấu hình kết nối tới CSDL
	 * @var XPHP_Db_Adapter
	 */
	protected  $_frontAdapter;
	
	/**
	 * Lớp hỗ trợ truy vấn csdl
	 * @var XPHP_Db_NoSQLActiveRecord
	 */
	public $db;
	
	/**
	 * Kết nối tới CSDL
	 * @var connect link
	 */
	public $connect;
	
	/**
	 * @var string
	 */
	public $querySafe;
	
	/**
	 * Khởi tạo adapter kết nối tới csdl mysql
	 * @param XPHP_Db_Adapter $frontAdapter
	 */
	public function __construct($frontAdapter)
	{
		$this->_frontAdapter = $frontAdapter;
		
		//Thiết lập chế độ debug
		$this->db_debug = $frontAdapter->debug;
		
		//Kết nối
		$this->connect();
		
		//Khởi tạo lớp ActiveRecord hỗ trợ truy xuất csdl
		$this->db = new XPHP_Db_NoSQLActiveRecord($this);
	}
	/**
	 * Phương thức kết nối tới CSDL
	 */
	public abstract function connect();
	
	/**
	 * Đóng kết nối tới CSDL
	 */
	public abstract function close();
	
    /**
     * Lấy ra id của bản ghi vừa insert vào
     * @return void
     */
    public abstract function insert_id();

	/**
	 * Lấy thông tin cấu hình
	 * @param string $key
	 */
	public function getConfig($key)
	{
		if(isset($this->_frontAdapter->$key))
			return $this->_frontAdapter->$key;
			
		return null;
	}

	public function getConnect()
	{
		return $this->connect();
	}
	
	public $db_debug		= FALSE;
	
	/**
	 * Thông báo lỗi
	 * @var array
	 */
	protected $error_message;
	
	/**
	 * Mã lỗi
	 * @var array
	 */
	protected $error_number;
	
	/**
	 * Gán thông báo lỗi vào hệ thống
	 * @param string $message
	 * @param int $number
	 */
	public function setError($message, $number)
	{
		$this->error_message[] = $message;
		$this->error_number[] = $number;
	}
	
	/**
	 * Error message
	 */
	public abstract function _error_message();
	
	/**
	 * Error number
	 */
	public abstract function _error_number();
	
	/**
	 * Drop database
	 */
	public abstract function drop_db();
	
	/**
	 * Drop table
	 */
	public abstract function drop_collection($collection);
}