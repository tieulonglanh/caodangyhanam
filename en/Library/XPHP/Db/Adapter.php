<?php
/**
 * Lớp điều khiển kết nối giữa ứng dụng và database
 * Adapter sẽ load driver để kết nối tới database 
 * @author Mr.UBKey
 *
 */
class XPHP_Db_Adapter
{
	/**
	 * Server
	 * @var string
	 */
	public $server;
	/**
	 * Port
	 * @var int
	 */
	public $port;
	/**
	 * Tên CSDL
	 * @var string
	 */
	public $dbname;
	/**
	 * Tên user truy cập CSDL
	 * @var string
	 */
	public $username;
	/**
	 * Mật khẩu user truy cập CSDL
	 * @var string
	 */
	public $password;
	
	/**
	 * Tên adapter hỗ trợ truy xuất CSDL
	 * @var string
	 */
	public $adapter;
	
	/**
	 * Tên adapter. 
	 * Cần dùng nếu bạn muốn kết nối model tới csdl phân tán
	 * @var string
	 */
	public $name;
	
	/**
	 * Đặt làm adapter mặc định kết nối tới csdl
	 * @var boolean
	 */
	public $default;
	
	/**
	 * Prefix của table
	 * @var string
	 */
	public $dbprefix;
	
	/**
	 * Kết nối liên tục
	 * @var bool
	 */
	public $persist = true;
	
	/**
	 * @var string
	 */
	public $persistKey;
	
	/**
	 * Bật chế độ debug cho Db
	 * @var boolean
	 */
	public $debug = false;
	
	/**
	 * Khởi tạo một adapter điều khiển kết nối tới CSDL
	 * @param array $config
	 * array(	
	 * 		name => string
	 * 		default => boolean
	 * 		server => string
	 * 		dbname => string
	 *		username => string
	 *		password => string
	 *		adapter => string Nếu không truyền giá trị mặc định là mysql
	 *		dbprefix => string
	 * )
	 */
	public function __construct($config=array())
	{
		if(isset($config['name']))
			$this->name = $config['name'];
		if(isset($config['default']))
			$this->default = $config['default'];
		if(isset($config['server']))
			$this->server = $config['server'];
		if(isset($config['dbname']))
			$this->dbname = $config['dbname'];
		if(isset($config['username']))
			$this->username = $config['username'];
		if(isset($config['password']))
			$this->password = $config['password'];
		if(isset($config['persist']))
			$this->persist = $config['persist'];
		if(isset($config['persistKey']))
			$this->persistKey = $config['persistKey'];
		if(isset($config['querySafe']))
			$this->querySafe = $config['querySafe'];
		if(isset($config['adapter']) && !empty($config['adapter']))
			$this->adapter = $config['adapter'];
		else
			$this->adapter = 'mysql';
		if(isset($config['dbprefix']))
			$this->dbprefix = $config['dbprefix'];
		else
			$this->dbprefix = "";
	}
	
	/**
	 * Load cấu hình từ file config cho adapter và trả về adapter kết nối csdl
	 * @param string $config VD: database, database > xweb_db
	 * @return XPHP_Db_Adapter_Abstract
	 */
	public function loadConfig($config)
	{
		//Load cấu hình trong file config với node rewrite
		require_once 'XPHP/Config.php';
		XPHP_Config::load($this, $config);
		
		//Gọi đến phương thức khởi tạo adapter kết nối csdl
		$dbadapter = $this->callAdapter();
		
		require_once 'XPHP/Registry.php';
		
		//Kiểm tra nếu là default thì gán làm defaultbdadaper
		if($this->default)
			XPHP_Registry::set("Db_default", $dbadapter);
		
		//Đưa adapter vào registry để có thể gán từ model
		XPHP_Registry::set("Db_".$this->name, $dbadapter);
		
		//Trả về adapter kết nối csdl
		return $dbadapter;
	}
	
	private function callAdapter()
	{
		$class = "";
		switch ($this->adapter)
		{
			case 'mysql':
				$class = "XPHP_Db_Adapter_Mysql";
				break;
			case 'mongodb':
			    $class = "XPHP_Db_Adapter_MongoDB";
			    break;
			default:
				$class = "XPHP_Db_Adapter_Mysql";
				break;
		}
		return new $class($this);
	}
	
	public function setDefaultAdapter()
	{
		require_once 'XPHP/Registry.php';
		XPHP_Registry::set("DefaultDbAdapter", $this);
	}
}