<?php
/**
 * Lớp hỗ trợ Adapter kết nối tới MongoDB
 * @author Mr.UBKey
 */

require_once 'XPHP/Db/Adapter/NoSQLAbstract.php';
require_once 'XPHP/Db/Exception.php';

class XPHP_Db_Adapter_MongoDB extends XPHP_Db_Adapter_NoSQLAbstract
{
    /**
     * @var string
     */
    public $querySafe = 'safe';
    
	public function connect()
	{
		if ( ! class_exists('Mongo'))
		{
			throw new XPHP_Db_Exception("MongoDB PECL extension đã không được cài đặt hoặc kích hoạt", 500);
		}
		$options = array();
		if ($this->_frontAdapter->persist === TRUE)
		{
			$options['persist'] = isset($this->_frontAdapter->persistKey) && !empty($this->_frontAdapter->persistKey) ? $this->_frontAdapter->persistKey : 'mongo_persist';
		}
		try
		{
			$connection_string = $this->getConnectionString();
			$this->connect = new Mongo($connection_string, $options);
			$this->connect = $this->connect->{$this->_frontAdapter->dbname};
			return ($this);
		}
		catch (MongoConnectionException $e)
		{
			if($this->db_debug)
				throw new XPHP_Db_Exception("Không thể kết nối tới MongoDB", 500);
			else
				throw new XPHP_Db_Exception("Không thể kết nối tới MongoDB: {$e->getMessage()}", 500);
		}
	}
	
	public function close()
	{
		
	}
	
	public function insert_id()
	{
		
	}
	
	public function drop_db()
	{
		try
		{
			$this->_adapter->connect->drop();
			return true;
		}
		catch (Exception $e)
		{
			$this->setError("Không thể drop database `{$this->_frontAdapter->dbname}`: {$e->getMessage()}", 500);
			return false;
		}
	}
	
	public function drop_collection($collection)
	{
		if (empty($collection))
		{
			$this->setError("Lỗi không thể drop collection `{$collection}`", 500);
			return false;
		}
		else
		{
			try
			{
				$this->connection->{$collection}->drop();
				return true;
			}
			catch (Exception $e)
			{
				$this->setError("Lỗi không thể drop collection `{$collection}` : {$e->getMessage()}", 500);
				return false;
			}
		}
	}
	
	/**
	 * Error message
	 */
	public function _error_message()
	{
		
	}
	
	/**
	 * Error number
	 */
	public function _error_number()
	{
		
	}
	
	
	/**
	 * Lấy ra chuỗi kết nối
	 */
	private function getConnectionString() 
	{
		$connection_string = "mongodb://";
		if (empty($this->_frontAdapter->server))
			return new XPHP_Db_Exception("Server phải được thiết lập để kết nối tới MongoDB", 500);
		if ( ! empty($this->_frontAdapter->username) && ! empty($this->_frontAdapter->password))
			$connection_string .= "{$this->_frontAdapter->username}:{$this->_frontAdapter->password}@";
		if (isset($this->_frontAdapter->port) && ! empty($this->_frontAdapter->port))
			$connection_string .= "{$this->_frontAdapter->server}:{$this->_frontAdapter->port}";
		else
			$connection_string .= "{$this->_frontAdapter->server}";
		return $connection_string;
	}
}