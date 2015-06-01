<?php
/**
 * Lớp abstract của XPHP_Db_Adapter_XXXXX
 * @author Mr.UBKey
 */

require_once 'XPHP/Db/ActiveRecord.php';

abstract class XPHP_Db_Adapter_Abstract
{
	/**
	 * Adapter điều khiển, giao tiếp các cấu hình kết nối tới CSDL
	 * @var XPHP_Db_Adapter
	 */
	protected  $_frontAdapter;
	
	/**
	 * Lớp hỗ trợ truy vấn csdl
	 * @var XPHP_Db_ActiveRecord
	 */
	public $db;
	
	/**
	 * Kết nối tới CSDL
	 * @var connect link
	 */
	protected $connect;
	
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
		$this->db = new XPHP_Db_ActiveRecord($this);
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
	 * Chạy câu lệnh sql
	 */
	public abstract function query($sql);

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
	
	/**
	 * 
	 * The character used for escaping
	 * @var char
	 */
	public	$_escape_char = '`';

	/**
	 * 
	 * clause and character used for LIKE escape sequences - not used in MySQL
	 * @var char
	 */
	public $_like_escape_str = '';
	/**
	 * 
	 * clause and character used for LIKE escape sequences - not used in MySQL
	 * @var char
	 */
	public $_like_escape_chr = '';

	/**
	 * Whether to use the MySQL "delete hack" which allows the number
	 * of affected rows to be shown. Uses a preg_replace when enabled,
	 * adding a bit more processing to all queries.
	 */	
	public $delete_hack = TRUE;
	
	/**
	 * The syntax to count rows is slightly different across different
	 * database engines, so this string appears in each driver and is
	 * used for the count_all() and count_all_results() functions.
	 */
	public $_count_string = 'SELECT COUNT(*) AS ';
	public $_random_keyword = ' RAND()'; // database specific random keyword
	
	public $trans_enabled	= TRUE;
	public $trans_strict	= TRUE;
	public $_trans_depth	= 0;
	public $_trans_status	= TRUE;
	public $_trans_failure;
	public $db_debug		= FALSE;
	
	/**
	 * Error message
	 */
	public abstract function _error_message();
	
	/**
	 * Error number
	 */
	public abstract function _error_number();
}