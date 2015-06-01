<?php
/**
 * Lớp hỗ trợ Adapter kết nối tới Mysql
 * @author Mr.UBKey
 */

require_once 'XPHP/Db/Adapter/Abstract.php';
require_once 'XPHP/Db/Exception.php';

class XPHP_Db_Adapter_Mysql extends XPHP_DB_Adapter_Abstract
{
	public $_count_string = 'SELECT COUNT(*) AS ';
	public $_random_keyword = ' RAND()'; // database specific random keyword
	
	/**
	 * Phương thức kết nối tới CSDL
	 */
	public function connect()
	{
		$this->connect = @mysql_connect($this->_frontAdapter->server, $this->_frontAdapter->username, $this->_frontAdapter->password, true);		
		if (mysql_errno() == 1203)
		{
			header('Error');
			exit;
		} 
		elseif (mysql_errno() == 1045)
		{
			die('Không thể kết nối tới CSDL MYSQL');	
		}
		
		if (!$dbselect = @mysql_select_db($this->_frontAdapter->dbname,$this->connect))
		{
			$this->connect = @mysql_select_db($this->_frontAdapter->dbname,$this->connect) or die("Không tìm thấy CSDL MYSQL có tên ".$this->_frontAdapter->dbname);
		}
		
		if($this->connect)
		{
			mysql_query("SET NAMES 'utf8';", $this->connect);
    		mysql_query("SET CHARACTER SET 'utf8';", $this->connect);
		}
		
		return $this->connect;
	}
	
	/**
	 * Phương thức kết nối Persistent tới CSDL
	 */
	public function pconnect()
	{
		$this->connect = @mysql_pconnect($this->_frontAdapter->server, $this->_frontAdapter->username, $this->_frontAdapter->password, true);		
		if (mysql_errno() == 1203)
		{
			header('Error');
			exit;
		} 
		elseif (mysql_errno() == 1045)
		{
			die('Không thể kết nối tới CSDL');	
		}
		
		if (!$dbselect = @mysql_select_db($this->_frontAdapter->dbname,$this->connect))
		{
			$this->connect = @mysql_select_db($this->_frontAdapter->dbname,$this->connect) or die("Không tìm thấy CSDL có tên ".$this->_frontAdapter->dbname);
		}
		
		if($this->connect)
		{
			mysql_query("SET NAMES 'utf8';", $this->connect);
    		mysql_query("SET CHARACTER SET 'utf8';", $this->connect);
		}
		
		return $this->connect;
	}
	
	/**
	 * Đóng kết nối tới CSDL
	 */
	public function close()
	{
		if ($this->connect)
			return @mysql_close($this->connect);
		else
			return false;
	}

		/**
	 * Execute the query
	 *
	 * @access	private called by the base class
	 * @param	string	an SQL query
	 * @return	resource
	 */	
	function _execute($sql)
	{
		$sql = $this->_prep_query($sql);
		return @mysql_query($sql, $this->connect);
	}
	
	function query($sql)
	{
		//Chạy query
		$sql = $this->_prep_query($sql);
		$rs = mysql_query($sql, $this->connect);
		
		//Trả về đối tượng kiểu result
		require_once 'XPHP/Db/Adapter/Mysql/Result.php';
		
		$result 		= new XPHP_Db_Adapter_Mysql_Result();
		$result->conn_id	= $this->connect;
		$result->result_id	= $rs;
		
		return $result;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Prep the query
	 *
	 * If needed, each database adapter can prep the query string
	 *
	 * @access	private called by execute()
	 * @param	string	an SQL query
	 * @return	string
	 */	
	function _prep_query($sql)
	{
		// "DELETE FROM TABLE" returns 0 affected rows This hack modifies
		// the query so that it returns the number of affected rows
		if ($this->delete_hack === TRUE)
		{
			if (preg_match('/^\s*DELETE\s+FROM\s+(\S+)\s*$/i', $sql))
			{
				$sql = preg_replace('/^\s*DELETE\s+FROM\s+(\S+)\s*$/', "DELETE FROM \\1 WHERE 1=1", $sql);
			}
		}
		
		return $sql;
	}

	// --------------------------------------------------------------------

	/**
	 * Begin Transaction
	 *
	 * @access	public
	 * @return	bool		
	 */	
	function trans_begin($test_mode = FALSE)
	{
		if ( ! $this->trans_enabled)
		{
			return TRUE;
		}
		
		// When transactions are nested we only begin/commit/rollback the outermost ones
		if ($this->_trans_depth > 0)
		{
			return TRUE;
		}

		// Reset the transaction failure flag.
		// If the $test_mode flag is set to TRUE transactions will be rolled back
		// even if the queries produce a successful result.
		$this->_trans_failure = ($test_mode === TRUE) ? TRUE : FALSE;
		
		$this->query('SET AUTOCOMMIT=0');
		$this->query('START TRANSACTION'); // can also be BEGIN or BEGIN WORK
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Commit Transaction
	 *
	 * @access	public
	 * @return	bool		
	 */	
	function trans_commit()
	{
		if ( ! $this->trans_enabled)
		{
			return TRUE;
		}

		// When transactions are nested we only begin/commit/rollback the outermost ones
		if ($this->_trans_depth > 0)
		{
			return TRUE;
		}

		$this->query('COMMIT');
		$this->query('SET AUTOCOMMIT=1');
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Rollback Transaction
	 *
	 * @access	public
	 * @return	bool		
	 */	
	function trans_rollback()
	{
		if ( ! $this->trans_enabled)
		{
			return TRUE;
		}

		// When transactions are nested we only begin/commit/rollback the outermost ones
		if ($this->_trans_depth > 0)
		{
			return TRUE;
		}

		$this->query('ROLLBACK');
		$this->query('SET AUTOCOMMIT=1');
		return TRUE;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Escape String
	 *
	 * @access	public
	 * @param	string
	 * @param	bool	whether or not the string will be used in a LIKE condition
	 * @return	string
	 */
	function escape_str($str, $like = FALSE)	
	{	
		if (is_array($str))
		{
			foreach($str as $key => $val)
	   		{
				$str[$key] = $this->escape_str($val, $like);
	   		}
   		
	   		return $str;
	   	}

		if (function_exists('mysql_real_escape_string') AND is_resource($this->connect))
		{
			$str = mysql_real_escape_string($str, $this->connect);
		}
		elseif (function_exists('mysql_escape_string'))
		{
			$str = mysql_escape_string($str);
		}
		else
		{
			$str = addslashes($str);
		}
		
		// escape LIKE condition wildcards
		if ($like === TRUE)
		{
			$str = str_replace(array('%', '_'), array('\\%', '\\_'), $str);
		}
		
		return $str;
	}
		
	// --------------------------------------------------------------------

	/**
	 * Affected Rows
	 *
	 * @access	public
	 * @return	integer
	 */
	function affected_rows()
	{
		return @mysql_affected_rows($this->connect);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Insert ID
	 *
	 * @access	public
	 * @return	integer
	 */
	function insert_id()
	{
		return @mysql_insert_id($this->connect);
	}

	// --------------------------------------------------------------------

	/**
	 * "Count All" query
	 *
	 * Generates a platform-specific query string that counts all records in
	 * the specified database
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function count_all($table = '')
	{
		if ($table == '')
		{
			return 0;
		}
	
		$query = $this->query($this->_count_string . $this->_protect_identifiers('numrows') . " FROM " . $this->_protect_identifiers($table, TRUE, NULL, FALSE));

		if ($query->num_rows() == 0)
		{
			return 0;
		}

		$row = $query->row();
		return (int) $row->numrows;
	}

	// --------------------------------------------------------------------

	/**
	 * List table query
	 *
	 * Generates a platform-specific query string so that the table names can be fetched
	 *
	 * @access	private
	 * @param	boolean
	 * @return	string
	 */
	function _list_tables($prefix_limit = FALSE)
	{
		$sql = "SHOW TABLES FROM ".$this->_escape_char.$this->_frontAdapter->dbname.$this->_escape_char;	

		if ($prefix_limit !== FALSE AND $this->_frontAdapter->dbprefix != '')
		{
			$sql .= " LIKE '".$this->escape_like_str($this->_frontAdapter->dbprefix)."%'";
		}

		return $sql;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Show column query
	 *
	 * Generates a platform-specific query string so that the column names can be fetched
	 *
	 * @access	public
	 * @param	string	the table name
	 * @return	string
	 */
	function _list_columns($table = '')
	{
		return "SHOW COLUMNS FROM ".$table;
	}

	// --------------------------------------------------------------------

	/**
	 * Field data query
	 *
	 * Generates a platform-specific query so that the column data can be retrieved
	 *
	 * @access	public
	 * @param	string	the table name
	 * @return	object
	 */
	function _field_data($table)
	{
		return "SELECT * FROM ".$table." LIMIT 1";
	}

	// --------------------------------------------------------------------

	/**
	 * The error message string
	 *
	 * @access	private
	 * @return	string
	 */
	function _error_message()
	{
		return mysql_error($this->connect);
	}
	
	// --------------------------------------------------------------------

	/**
	 * The error message number
	 *
	 * @access	private
	 * @return	integer
	 */
	function _error_number()
	{
		return mysql_errno($this->connect);
	}

	// --------------------------------------------------------------------



	/**
	 * From Tables
	 *
	 * This function implicitly groups FROM tables so there is no confusion
	 * about operator precedence in harmony with SQL standards
	 *
	 * @access	public
	 * @param	type
	 * @return	type
	 */
	function _from_tables($tables)
	{
		if ( ! is_array($tables))
		{
			$tables = array($tables);
		}
		
		return '('.implode(', ', $tables).')';
	}

	// --------------------------------------------------------------------
	
	/**
	 * Insert statement
	 *
	 * Generates a platform-specific insert string from the supplied data
	 *
	 * @access	public
	 * @param	string	the table name
	 * @param	array	the insert keys
	 * @param	array	the insert values
	 * @return	string
	 */
	function _insert($table, $keys, $values)
	{	
		return "INSERT INTO ".$table." (".implode(', ', $keys).") VALUES (".implode(', ', $values).")";
	}
	
	// --------------------------------------------------------------------

	/**
	 * Update statement
	 *
	 * Generates a platform-specific update string from the supplied data
	 *
	 * @access	public
	 * @param	string	the table name
	 * @param	array	the update data
	 * @param	array	the where clause
	 * @param	array	the orderby clause
	 * @param	array	the limit clause
	 * @return	string
	 */
	function _update($table, $values, $where, $orderby = array(), $limit = FALSE)
	{
		foreach($values as $key => $val)
		{
			$valstr[] = $key." = ".$val;
		}
		
		$limit = ( ! $limit) ? '' : ' LIMIT '.$limit;
		
		$orderby = (count($orderby) >= 1)?' ORDER BY '.implode(", ", $orderby):'';
	
		$sql = "UPDATE ".$table." SET ".implode(', ', $valstr);

		$sql .= ($where != '' AND count($where) >=1) ? " WHERE ".implode(" ", $where) : '';

		$sql .= $orderby.$limit;
		
		return $sql;
	}

	// --------------------------------------------------------------------

	/**
	 * Truncate statement
	 *
	 * Generates a platform-specific truncate string from the supplied data
	 * If the database does not support the truncate() command
	 * This function maps to "DELETE FROM table"
	 *
	 * @access	public
	 * @param	string	the table name
	 * @return	string
	 */	
	function _truncate($table)
	{
		return "TRUNCATE ".$table;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Delete statement
	 *
	 * Generates a platform-specific delete string from the supplied data
	 *
	 * @access	public
	 * @param	string	the table name
	 * @param	array	the where clause
	 * @param	string	the limit clause
	 * @return	string
	 */	
	function _delete($table, $where = array(), $like = array(), $limit = FALSE)
	{
		$conditions = '';

		if (count($where) > 0 OR count($like) > 0)
		{
			$conditions = "\nWHERE ";
			$conditions .= implode("\n", $where);

			if (count($where) > 0 && count($like) > 0)
			{
				$conditions .= " AND ";
			}
			$conditions .= implode("\n", $like);
		}

		$limit = ( ! $limit) ? '' : ' LIMIT '.$limit;
	
		return "DELETE FROM ".$table.$conditions.$limit;
	}

	// --------------------------------------------------------------------

	/**
	 * Limit string
	 *
	 * Generates a platform-specific LIMIT clause
	 *
	 * @access	public
	 * @param	string	the sql query string
	 * @param	integer	the number of rows to limit the query to
	 * @param	integer	the offset value
	 * @return	string
	 */
	function _limit($sql, $limit, $offset)
	{	
		if ($offset == 0)
		{
			$offset = '';
		}
		else
		{
			$offset .= ", ";
		}
		
		return $sql."LIMIT ".$offset.$limit;
	}
	
	/**
	 * PLUS
	 */
	
	/**
	 * Simple Query
	 * This is a simplified version of the query() function.  Internally
	 * we only use it when running transaction commands since they do
	 * not require all the features of the main query() function.
	 *
	 * @access	public
	 * @param	string	the sql query
	 * @return	mixed		
	 */	
	function simple_query($sql)
	{
		return $this->_execute($sql);
	}
	/*---------------------------------------------------------------------*/
	/**
	 * Escape LIKE String
	 *
	 * Calls the individual driver for platform
	 * specific escaping for LIKE conditions
	 *
	 * @access	public
	 * @param	string
	 * @return	mixed
	 */
	function escape_like_str($str)
	{
		return $this->escape_str($str, TRUE);
	}
}