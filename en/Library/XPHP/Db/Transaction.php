<?php
class XPHP_DB_Transaction
{
	private $DBFactory;
	
	protected $transaction = false; // is in transaction mode?
	
	public function __construct()
	{
		require_once 'XPHP/Registry.php';
		$this->DBFactory = XPHP_Registry::get("DefaultConnection");
	}
	
	//Set DBFactory
	public function setDBFactory($dbFactory)
	{
		$this->DBFactory = $dbFactory;
	}
	
	// FUNCTION begin_transaction
	//		begin a transaction
	//		set $transaction = true
	// ------------------------------------------------------------------------------------------
	public function Begin()
	{
		$this->transaction = true;
		$result = mysql_query('BEGIN', $this->DBFactory->connect);
		return $result;
	}
	// FUNCTION commit_transaction
	//		commit a transaction
	//		set $transaction = false
	// ------------------------------------------------------------------------------------------
	public function Commit()
	{
		$this->transaction = false;
		$result = mysql_query('COMMIT', $this->DBFactory->connect);
		return $result;
	}
	// FUNCTION rollback_transaction
	//		rollback a transaction
	//		set $transaction = false
	// ------------------------------------------------------------------------------------------
	public function Rollback()
	{
		$this->transaction = false;
		$result = mysql_query('ROLLBACK', $this->connect);
		return $result;
	}
}