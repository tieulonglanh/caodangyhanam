<?php
class XPHP_DB_Table_Model
{
	/**
	 * Lớp hỗ trợ truy vấn csdl
	 * @var XPHP_Db_ActiveRecord
	 */
	public $db;

	/**
	 * Tên bảng hiện tại
	 * @var string
	 */
	public $_name;
	
	/**
	 * Tên khóa chính
	 * @var string
	 */
	public $_primaryKey;
	
	/**
	 * Chỉ đọc
	 * @var boolean
	 */
	public $_readOnly = false;
	
	/**
	 * Adapter điều khiển kết nối tới CSDL
	 * @var XPHP_Db_Adapter_Abstract
	 */
	protected $_adapter;
	
	protected $result;				// Kết quả trả về của câu query hiện tại
	protected $cache_tables = array();
	protected $exists_db = array();
	protected $list_query;
	
	// Debug
	protected $db_queries ;// Số của query hoàn thành

	protected $num_queries = 0;		// Số row của query đã hoàn thành
	
	/**
	 * Gán adapter
	 * @param $dbadapter XPHP_Db_Adapter
	 */
	public function setAdapter($dbadapter)
	{
		$this->_adapter = $dbadapter;
		//Gán lớp hỗ trợ truy vấn csdl
		$this->db = clone $this->_adapter->db;
	}
	
	/**
	 * Lấy ra đối tượng ActiveRecord
	 * @see XPHP_Db_ActiveRecord
	 * @return XPHP_Db_ActiveRecord
	 */
	public function getActiveRecord()
	{
	    return $this->db;
	}
	
	// function query
	// Run an sql command
	// Parameters:
	//		$query:		the command to run
	// ------------------------------------------------------------------------------------------
	// 		$info: 	Thong tin cua query khi duoc goi ( Module, vi tri, file chua loi goi)
	function query($query,$info=false)
	{
		// Clear old query result
		unset($this->result);

		if (!empty($query))
		{
			//echo $query.'<br>';
			if(!($this->result = mysql_query($query, $this->_adapter->getConnect())))
			{
				echo mysql_error($this->_adapter->getConnect());
				echo '<br>Lỗi truy vấn:<font color=red>'.$query.'</font><br>';
			}
			
			$this->num_queries++;
			
			if(isset($this->db_queries[$this->_adapter->getConfig('dbname')]))
				$this->db_queries[$this->_adapter->getConfig('dbname')]++;
			else 
			{
				$this->db_queries[$this->_adapter->getConfig('dbname')] = 0;
				$this->list_query[$this->_adapter->getConfig('dbname')] = "";
			}
			
			if($info)
			{
				$this->list_query[$this->_adapter->getConfig('dbname')].="<br><br>&nbsp;<font color=red>".$this->db_queries[$this->_adapter->getConfig('dbname')]." </font>:&nbsp; <b>".$query."</b>";
				$this->list_query[$this->_adapter->getConfig('dbname')].="<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font color=green><b>".$info."</b></font>";
				
			}
			else 
			{
				$this->list_query[$this->_adapter->getConfig('dbname')].="<br><br>&nbsp;<font color=red>".$this->db_queries[$this->_adapter->getConfig('dbname')]." </font>:&nbsp; <b><font color=#FF33FF>".$query."</font></b>";
			}

		}
		return $this->result;
	}
	
	//Mot so phuong thuc dung de dem so rows trong bang CSDL
	function count($condition=false)
	{
		if ($condition)
		{
			$condition = ' where '.$condition;
		}
		
		if($this->query('select id from `'.$this->_name.'`'.$condition))
		{
			return $this->num_rows();			
		}

	}	
	
	function count_rows($condition=false, $info=false)
	{
		if ($condition)
		{
			$condition = ' where '.$condition;
		}
		
		//$trans_num[1]['rows']
				
		if($this->query('SELECT count(id) as rows FROM '.$this->_name.' '.$condition, $info))
		{
			$row_nums=$this->fetchall();
			return $row_nums[1]['rows'];			
		}
		
	}

	function count_rows_multi($condition=false, $table='', $info=false)
	{
		if($table=='')
			return $this->count_rows($this->_name, $condition=false, $info=false);
		else 
		{
			if ($condition)
			{
				$condition = ' where '.$condition;
			}
			
			//$trans_num[1]['rows']
					
			if($this->query('SELECT count('.$table.'.id) as rows FROM '.$this->_name.' '.$condition, $info))
			{
				$row_nums=$this->fetchall();
				return $row_nums[1]['rows'];			
			}
		}
	}

	//Lay ra mot ban ghi trong bang $table thoa man dieu kien $condition
	//Neu bang duoc cache thi lay tu cache, neu khong query tu CSDL
	function select($condition, $info=false)
	{		
		if(is_numeric($condition))
		{
			return $this->exists_id($this->_name,$condition, $info);			
		}
		else
		{
			return $this->exists('select * from `'.$this->_name.'` where '.$condition.' limit 0,1', $info);
		}
	}
	function select_one($field, $condition, $info=false)// 
	{
		return $this->exists('select '. $field.' from `'.$this->_name.'` where '.$condition, $info);
	}

	//Lay ra tat ca cac ban ghi trong bang $table thoa man dieu kien $condition sap xep theo thu tu $order
	//Neu bang duoc cache thi lay tu cache, neu khong query tu CSDL
	function select_all($condition=false, $order = false, $info=false)
	{
		
		if($order)
		{
			$order = ' order by '.$order;
		}
		if($condition)
		{
			$condition = ' where '.$condition;
		}
		$this->query('select * from `'.$this->_name.'` '.$condition.' '.$order, $info);
		return $this->fetch_all();
		
	}
	function select_field($list_field,$condition=false, $order = false, $info=false)
	{
		
		
		if($order)
		{
			$order = ' order by '.$order;
		}
		if($condition)
		{
			$condition = ' where '.$condition;
		}
		$this->query('select '.$list_field.' from `'.$this->_name.'` '.$condition.' '.$order, $info);
		return $this->fetchall();
		
	}

	/**
     * Insert model vào csdl
     * @param $values Mảng chứa dữ liệu cần đưa vào $key => $value
     * @return bool|int
     */
	function insert($values)
	{
        $result = $this->db->insert($values, $this->_name);
        if($result)
        {
            if(method_exists($this->db, 'insert_id'))
                return $this->db->insert_id();
            else
                return $result;
        }
        else
            return false;
	}
	function exists($query,$info=false)
	{
		$this->query($query, $info);
		if($this->num_rows()>=1)
		{
			return $this->fetch();
		}
		return false;
	}

    /**
     * Xóa
     * @param $id
     * @return bool|int
     */
	function delete($id)
	{
        //Set where
        $this->db->where($this->_primaryKey, $id);
        //Return delete
        return $this->db->delete($this->_name);
	}
	function exists_id($id, $info=false)
	{
		if(!isset($this->exists_db[$this->_name][$id]))
		{
			$this->exists_db[$this->_name][$id]=$this->exists('select * from `'.$this->_name.'` where id="'.$id.'" limit 0,1', $info);
		}
		return $this->exists_db[$this->_name][$id];
	}
	//Tra ve false neu khong xoa duoc
	//$table: Bang can xoa 1 ban ghi
	//$id: Ma so ban ghi can xoa
	function delete_id($id, $info=false)
	{
		return $this->delete($this->_name, 'id='.$id, $info);
	}

    /**
     * Update model vào csdl
     * @param $values Mảng chứa dữ liệu cần đưa vào $key => $value
     * @return bool|int
     */
	function update($values, $id)
	{
        //Set where
        $this->db->where($this->_primaryKey, $id);
        //Update data
        return $this->db->update($values, null, null, $this->_name);
        
	}
	
	// FUNCTION numrows
	//		return the number of rows of a $query
	// PARAMATERS:
	//		$query_id:	the query to be count number of query
	// ------------------------------------------------------------------------------------------
	function num_rows($query_id = 0)
	{
		if (!$query_id)
		{
			$query_id = $this->result;
		}

		if ($query_id)
		{
			$result = mysql_num_rows($query_id);

			return $result;
		}
		else
		{
			return false;
		}
	}
	function get_all_field()
	{
//		$fields = mysql_list_fields('cdt401_estore', $table_name, $this->connect_id);
		$fields = mysql_list_fields($this->_adapter->getConfig('dbname'), $this->_name, $this->_adapter->getConnect());
		$columns = mysql_num_fields($fields);
		$arr = array();
		for ($i = 0; $i < $columns; $i++) {
		    $arr[$i] = mysql_field_name($fields, $i);
		}
		return $arr;
	}
	// FUNCTION affected_rows
	//		return number of rows affected by the last query
	// ------------------------------------------------------------------------------------------

	function affected_rows()
	{

		if ($this->_adapter->getConnect())
		{
			$result = mysql_affected_rows($this->_adapter->getConnect());
			return $result;
		}
		else
		{
			return false;
		}
	}

	// FUNCTION num_fields
	//		return the number of fields in the result of a query
	// PARAMETERS:
	//		$query_id:	the $query to be counted
	// ------------------------------------------------------------------------------------------
	function num_fields($query_id = 0)
	{
		if (!$query_id)
		{
			$query_id = $this->result;
		}

		if ($query_id)
		{
			$result = mysql_num_fields($query_id);

			return $result;
		}
		else
		{
			return false;
		}
	}
	// FUNCTION field_name
	//		return the name of a field at order $offset in a query result
	// PARAMETERS:
	//		$query_id:	the query to be get field_name
	//		$offset:	postion of the field name
	// ------------------------------------------------------------------------------------------
	function field_name($query_id = 0, $offset)
	{
		if (!$query_id)
		{
			$query_id = $this->result;
		}

		if ($query_id)
		{
			$result = mysql_field_name($query_id, $offset);

			return $result;
		}
		else
		{
			return false;
		}
	}
	// FUNCTION field_type
	//		get the type of the field
	// PARAMETERS:
	//		$query_id:	the query to be get
	//		$offset:	postion of field
	// ------------------------------------------------------------------------------------------
	function field_type($query_id = 0, $offset)
	{
		if (!$query_id)
		{
			$query_id = $this->result;
		}

		if ($query_id)
		{
			$result = mysql_field_type($query_id, $offset);

			return $result;
		}
		else
		{
			return false;
		}
	}
	// FUNCTION fetch
	//		fetch a row from a query result
	// PARAMETERS:
	//		$query_id:	the query result to be fetched
	// ------------------------------------------------------------------------------------------
	function fetch($query_id = 0)
	{
		if (!$query_id)
		{
			$query_id = $this->result;
		}

		if ($query_id)
		{
			$result = mysql_fetch_assoc($query_id);

			return $result;
		}
		else
		{
			return false;
		}
	}
	// FUNCTION fetch_all
	//		fetch all row in a result
	//		return an array of record
	// PARAMETERS:
	//		$query_id:	the query result to be fetched
	// ------------------------------------------------------------------------------------------
	function fetch_all($query_id = 0)
	{
		if (!$query_id)
		{
			$query_id = $this->result;
		}

		if ($query_id)
		{
			$result=array();

			while($row = mysql_fetch_assoc($query_id))
			{
				$result[$row[$this->_primaryKey]] = $row;
			}

			return $result;
		}
		else
		{
			return false;
		}
	}
	// FUNCTION fetchall
	//		fetch all row in a result not use id
	//		return an array of record
	// PARAMETERS:
	//		$query_id:	the query result to be fetched
	// ------------------------------------------------------------------------------------------
	function fetchall($query_id = 0)
	{
		if (!$query_id)
		{
			$query_id = $this->result;

		}
		$i=0;
		if ($query_id)
		{
			$result=array();

			while($row = mysql_fetch_assoc($query_id))
			{
				$i++;
				$result[$i] = $row;
			}

			return $result;
		}
		else
		{
			return false;
		}
	}

	// FUNCTION row_seek
	//		seek to a position in query result
	// PARAMETERS:
	//		$query_id:	the query result to be seek
	//		$rownum:	position
	// ------------------------------------------------------------------------------------------
	function row_seek($query_id = 0, $rownum)
	{
		if (!$query_id)
		{
			$query_id = $this->result;
		}

		if ($query_id)
		{
			$result = mysql_data_seek($query_id, $rownum);

			return $result;
		}
		else
		{
			return false;
		}
	}
	// FUNCTION insert_id
	//		get the id has just been inserted
	// ------------------------------------------------------------------------------------------
	
	// FUNCTION free_result
	//		free memory of the query result
	// PARAMETERS:
	//		$query_id:	the query result to be free
	// ------------------------------------------------------------------------------------------

	function free_result($query_id = 0)
	{
		if (!$query_id)
		{
			$query_id = $this->result;
		}

		if ($query_id)
		{
			mysql_free_result($query_id);

			return true;
		}
		else
		{
			return false;
		}
	}
	// FUNCTION error
	//		return an array contain error code and message
	//			$result["code"]: error code
	//			$result["message"]: error message
	// ------------------------------------------------------------------------------------------

	function error()
	{
		$result['message'] = mysql_error($this->_adapter->getConnect());
		$result['code'] = mysql_errno($this->_adapter->getConnect());
		return $result;
	}

	// FUNCTION escape
	//		This function will escape the unescaped_string, so that it is safe to place it in a query().
	// PARAMETERS:
	//		$sql:	the unescaped_string
	// ------------------------------------------------------------------------------------------
	function escape($sql)
	{
		return mysql_real_escape_string($sql);
	}
	// FUNCTION num_queries
	//		Return the number of queries
	// ------------------------------------------------------------------------------------------
	function num_queries()
	{
		if (!isset($this->db_queries[$this->_adapter->getConfig('dbname')])) 
			$this->db_queries[$this->_adapter->getConfig('dbname')]="0";
		return 
			$this->db_queries[$this->_adapter->getConfig('dbname')];
	}
	public function insert_id()
	{

		if ($this->_adapter->getConnect())
		{
			$result = mysql_insert_id();

			return $result;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Lấy ra tên tất cả các cột trong câu lệnh select
	 * @param unknown_type $query
	 */
    function mysql_field_array() {
    
    	$query = mysql_query("SELECT * FROM `" . $this->_name . "` LIMIT 0, 1", $this->_adapter->getConnect());
    	
        $field = mysql_num_fields( $query );
    
        for ( $i = 0; $i < $field; $i++ ) {
        
            $names[] = mysql_field_name( $query, $i );
        
        }
        
        return $names;
    
    }
	
}