<?php
/**
 * CodeIgniter MongoDB Active Record Library
 *
 * A library to interface with the NoSQL database MongoDB. For more information see http://www.mongodb.org
 * Originally created by Alex Bilbie, but extended and updated by Kyle J. Dye
 *
 * @package		CodeIgniter
 * @author		Alex Bilbie | www.alexbilbie.com | alex@alexbilbie.com
 * @author		Kyle J. Dye | www.kyledye.com | kyle@kyledye.com
 * @copyright	Copyright (c) 2010, Alex Bilbie.
 * @license		http://www.opensource.org/licenses/mit-license.php
 * @link		http://alexbilbie.com
 * @link		http://kyledye.com
 * @version		Version 0.4.1
 *
 * Thanks to Nick Jackson (nickjackson.me) and Phil Sturgeon (philsturgeon.co.uk) for additional help
 */

class XPHP_Db_NoSQLActiveRecord
{
	/**
	 * Lớp Adapter hỗ trợ kết nối, truy xuất tới Mysql
	 * @var XPHP_Db_Adapter_MongoDB
	 */
	private $_adapter;
	
	private $_table;
	
	private $selects = array();
	public  $wheres = array(); // Public to make debugging easier
	private $sorts = array();
	public  $updates = array(); // Public to make debugging easier
	
	private $limit = 999999;
	private $offset = 0;
	
	/**
	*	--------------------------------------------------------------------------------
	*	Constructor
	*	--------------------------------------------------------------------------------
	*
	*	Automatically check if the Mongo PECL extension has been installed/enabled.
	*	Generate the connection string and establish a connection to the MongoDB.
	*	@param XPHP_Db_Adapter_MongoDB $adapter
	*/
	public function __construct($adapter)
	{
		$this->_adapter = $adapter;
	}
		
	/**
	 * Gán tên table cho statement
	 * @param string $name
	 */
	public function setTable ($name)
	{
		$this->_table = $name;
	}
	
	/**
	 * Lấy ra tên table
	 */
	public function getTable ()
	{
		return $this->_table;
	}
	
	/**
	*	--------------------------------------------------------------------------------
	*	//! Drop database
	*	--------------------------------------------------------------------------------
	*
	*	Drop a Mongo database
	*/
	public function drop_db()
	{
		return $this->_adapter->drop_db();
	}
		
	/**
	*	--------------------------------------------------------------------------------
	*	//! Drop collection
	*	--------------------------------------------------------------------------------
	*
	*	Drop a Mongo collection
	*	@param string $collection
	*/
	public function drop_collection($collection)
	{
		return $this->_adapter->drop_collection($collection);
	}
	
	/**
	*	--------------------------------------------------------------------------------
	*	//! Select
	*	--------------------------------------------------------------------------------
	*
	*	Determine which fields to include OR which to exclude during the query process.
	*	Currently, including and excluding at the same time is not available, so the 
	*	$includes array will take precedence over the $excludes array.  If you want to 
	*	only choose fields to exclude, leave $includes an empty array().
	*
	*	@usage: $this->mongo_db->select(array('foo', 'bar'))->get('foobar');
	*/
	
	public function select($includes = array(), $excludes = array())
	{
	 	if ( ! is_array($includes))
	 	{
	 		$includes = array();
	 	}
	 	
	 	if ( ! is_array($excludes))
	 	{
	 		$excludes = array();
	 	}
	 	
	 	if ( ! empty($includes))
	 	{
	 		foreach ($includes as $col)
	 		{
	 			$this->selects[$col] = 1;
	 		}
	 	}
	 	else
	 	{
	 		foreach ($excludes as $col)
	 		{
	 			$this->selects[$col] = 0;
	 		}
	 	}
	 	return $this;
	}
	
    public function distinct ($val)
    {
        $result = $this->command(array(
                            'distinct' => $this->_table, 
                            'key'      => $val,
                            'query'    => $this->wheres
                  ));
        if((int)$result['ok'] == 1)
            return $result['values'];
    }
    
    public function group_by($fields, $initial = NULL, $reduce = NULL, $collection = "")
    {
        if(empty($collection))
            $collection = $this->_table;
        $keys = array();
        if(is_array($fields))
        {
            foreach($fields as $f)
                $keys[$f] = 1;
        }
        else
            $keys[$fields] = 1;
        return $this->_adapter->connect->{$collection}->group($keys, 
                                                       array('count' => 0), // initial value of the aggregation counter object. 
                                                       new MongoCode('function(doc, prev) { prev.count += 1 }'),
                                                       $this->wheres);
    }
	
	/**
	*	--------------------------------------------------------------------------------
	*	//! Where
	*	--------------------------------------------------------------------------------
	*
	*	Get the documents based on these search parameters.  The $wheres array should 
	*	be an associative array with the field as the key and the value as the search
	*	criteria.
	*
	*	@usage : $this->mongo_db->where(array('foo' => 'bar'))->get('foobar');
	*/
	
	public function where($wheres, $value = null)
	{
		if (is_array($wheres))
		{
			foreach ($wheres as $wh => $val)
			{
				$this->wheres[$wh] = $val;
			}
		}
		
		else
		{
			$this->wheres[$wheres] = $value;
		}
		
		return $this;
	}
	
	/**
	*	--------------------------------------------------------------------------------
	*	or where
	*	--------------------------------------------------------------------------------
	*
	*	Get the documents where the value of a $field may be something else
	*
	*	@usage : $this->mongo_db->or_where(array( array('foo'=>'bar', 'bar'=>'foo' ))->get('foobar');
	*/
	
	public function or_where($wheres = array())
	{
		if (count($wheres) > 0)
		{
			if ( ! isset($this->wheres['$or']) || ! is_array($this->wheres['$or']))
			{
				$this->wheres['$or'] = array();
			}
			
			foreach ($wheres as $wh => $val)
			{
				$this->wheres['$or'][] = array($wh=>$val);
			}
		}
		return $this;
	}
	
	/**
	*	--------------------------------------------------------------------------------
	*	Where in
	*	--------------------------------------------------------------------------------
	*
	*	Get the documents where the value of a $field is in a given $in array().
	*
	*	@usage : $this->mongo_db->where_in('foo', array('bar', 'zoo', 'blah'))->get('foobar');
	*/
	
	public function where_in($field = "", $in = array())
	{
		$this->_where_init($field);
		$this->wheres[$field]['$in'] = $in;
		return $this;
	}
	
	/**
	*	--------------------------------------------------------------------------------
	*	Where in all
	*	--------------------------------------------------------------------------------
	*
	*	Get the documents where the value of a $field is in all of a given $in array().
	*
	*	@usage : $this->mongo_db->where_in('foo', array('bar', 'zoo', 'blah'))->get('foobar');
	*/
	
	public function where_in_all($field = "", $in = array())
	{
		$this->_where_init($field);
		$this->wheres[$field]['$all'] = $in;
		return $this;
	}
	
	/**
	*	--------------------------------------------------------------------------------
	*	Where not in
	*	--------------------------------------------------------------------------------
	*
	*	Get the documents where the value of a $field is not in a given $in array().
	*
	*	@usage : $this->mongo_db->where_not_in('foo', array('bar', 'zoo', 'blah'))->get('foobar');
	*/
	
	public function where_not_in($field = "", $in = array())
	{
		$this->_where_init($field);
		$this->wheres[$field]['$nin'] = $in;
		return $this;
	}
	
	/**
	*	--------------------------------------------------------------------------------
	*	Where greater than
	*	--------------------------------------------------------------------------------
	*
	*	Get the documents where the value of a $field is greater than $x
	*
	*	@usage : $this->mongo_db->where_gt('foo', 20);
	*/
	
	public function where_gt($field = "", $x)
	{
		$this->_where_init($field);
		$this->wheres[$field]['$gt'] = $x;
		return $this;
	}

	/**
	*	--------------------------------------------------------------------------------
	*	Where greater than or equal to
	*	--------------------------------------------------------------------------------
	*
	*	Get the documents where the value of a $field is greater than or equal to $x
	*
	*	@usage : $this->mongo_db->where_gte('foo', 20);
	*/
	
	public function where_gte($field = "", $x)
	{
		$this->_where_init($field);
		$this->wheres[$field]['$gte'] = $x;
		return $this;
	}

	/**
	*	--------------------------------------------------------------------------------
	*	Where less than
	*	--------------------------------------------------------------------------------
	*
	*	Get the documents where the value of a $field is less than $x
	*
	*	@usage : $this->mongo_db->where_lt('foo', 20);
	*/
	
	public function where_lt($field = "", $x)
	{
		$this->_where_init($field);
		$this->wheres[$field]['$lt'] = $x;
		return $this;
	}

	/**
	*	--------------------------------------------------------------------------------
	*	Where less than or equal to
	*	--------------------------------------------------------------------------------
	*
	*	Get the documents where the value of a $field is less than or equal to $x
	*
	*	@usage : $this->mongo_db->where_lte('foo', 20);
	*/
	
	public function where_lte($field = "", $x)
	{
		$this->_where_init($field);
		$this->wheres[$field]['$lte'] = $x;
		return $this;
	}
	
	/**
	*	--------------------------------------------------------------------------------
	*	Where between
	*	--------------------------------------------------------------------------------
	*
	*	Get the documents where the value of a $field is between $x and $y
	*
	*	@usage : $this->mongo_db->where_between('foo', 20, 30);
	*/
	
	public function where_between($field = "", $x, $y)
	{
		$this->_where_init($field);
		$this->wheres[$field]['$gte'] = $x;
		$this->wheres[$field]['$lte'] = $y;
		return $this;
	}
	
	/**
	*	--------------------------------------------------------------------------------
	*	Where between and but not equal to
	*	--------------------------------------------------------------------------------
	*
	*	Get the documents where the value of a $field is between but not equal to $x and $y
	*
	*	@usage : $this->mongo_db->where_between_ne('foo', 20, 30);
	*/
	
	public function where_between_ne($field = "", $x, $y)
	{
		$this->_where_init($field);
		$this->wheres[$field]['$gt'] = $x;
		$this->wheres[$field]['$lt'] = $y;
		return $this;
	}
	
	/**
	*	--------------------------------------------------------------------------------
	*	Where not equal
	*	--------------------------------------------------------------------------------
	*
	*	Get the documents where the value of a $field is not equal to $x
	*
	*	@usage : $this->mongo_db->where_not_equal('foo', 1)->get('foobar');
	*/
	
	public function where_ne($field = '', $x)
	{
		$this->_where_init($field);
		$this->wheres[$field]['$ne'] = $x;
		return $this;
	}
	
	/**
	*	--------------------------------------------------------------------------------
	*	Where near
	*	--------------------------------------------------------------------------------
	*
	*	Get the documents nearest to an array of coordinates (your collection must have a geospatial index)
	*
	*	@usage : $this->mongo_db->where_near('foo', array('50','50'))->get('foobar');
	*/
	
	function where_near($field = '', $co = array())
	{
		$this->__where_init($field);
		$this->where[$what]['$near'] = $co;
		return $this;
	}
	
	/**
	*	--------------------------------------------------------------------------------
	*	Like
	*	--------------------------------------------------------------------------------
	*	
	*	Get the documents where the (string) value of a $field is like a value. The defaults
	*	allow for a case-insensitive search.
	*
	*	@param $flags
	*	Allows for the typical regular expression flags:
	*		i = case insensitive
	*		m = multiline
	*		x = can contain comments
	*		l = locale
	*		s = dotall, "." matches everything, including newlines
	*		u = match unicode
	*
	*	@param $enable_start_wildcard
	*	If set to anything other than TRUE, a starting line character "^" will be prepended
	*	to the search value, representing only searching for a value at the start of 
	*	a new line.
	*
	*	@param $enable_end_wildcard
	*	If set to anything other than TRUE, an ending line character "$" will be appended
	*	to the search value, representing only searching for a value at the end of 
	*	a line.
	*
	*	@usage : $this->mongo_db->like('foo', 'bar', 'im', FALSE, TRUE);
	*/
	
	public function like($field = "", $value = "", $flags = "i", $enable_start_wildcard = TRUE, $enable_end_wildcard = TRUE)
	 {
	 	$field = (string) trim($field);
	 	$this->where_init($field);
	 	$value = (string) trim($value);
	 	$value = quotemeta($value);
	 	
	 	if ($enable_start_wildcard !== TRUE)
	 	{
	 		$value = "^" . $value;
	 	}
	 	
	 	if ($enable_end_wildcard !== TRUE)
	 	{
	 		$value .= "$";
	 	}
	 	
	 	$regex = "/$value/$flags";
	 	$this->wheres[$field] = new MongoRegex($regex);
	 	return $this;
	 }
	
	/**
	*	--------------------------------------------------------------------------------
	*	// Order by
	*	--------------------------------------------------------------------------------
	*
	*	Sort the documents based on the parameters passed. To set values to descending order,
	*	you must pass values of either -1, FALSE, 'desc', or 'DESC', else they will be
	*	set to 1 (ASC).
	*
	*	@usage : $this->mongo_db->where_between('foo', 20, 30);
	*/
	
	public function order_by($field, $direction = 'desc')
	{
		if ($direction == -1 || $direction === FALSE || strtolower($direction) == 'desc')
		{
			$this->sorts[$field] = -1; 
		}
		else
		{
			$this->sorts[$field] = 1;
		}
		return $this;
	}
	
	/**
	*	--------------------------------------------------------------------------------
	*	// Limit results
	*	--------------------------------------------------------------------------------
	*
	*	Limit the result set to $x number of documents
	*
	*	@usage : $this->mongo_db->limit($x);
	*/
	
	public function limit($x = 99999, $offset=NULL)
	{
		if ($x !== NULL && is_numeric($x) && $x >= 1)
		{
			$this->limit = (int) $x;
		}
		if ($offset !== NULL && is_numeric($offset) && $offset >= 0)
		{
		    $this->offset = (int) $offset;
		}
		return $this;
	}
	
	/**
	*	--------------------------------------------------------------------------------
	*	// Offset
	*	--------------------------------------------------------------------------------
	*
	*	Offset the result set to skip $x number of documents
	*
	*	@usage : $this->mongo_db->offset($x);
	*/
	
	public function offset($x = 0)
	{
		if ($x !== NULL && is_numeric($x) && $x >= 1)
		{
			$this->offset = (int) $x;
		}
		return $this;
	}
	
	/**
	*	--------------------------------------------------------------------------------
	*	// Get where
	*	--------------------------------------------------------------------------------
	*
	*	Get the documents based upon the passed parameters
	*
	*	@usage : $this->mongo_db->get_where('foo', array('bar' => 'something'));
	*/
	
	public function get_where($collection = "", $where = array())
	{
		return $this->where($where)->get($collection);
	}
	
	/**
	*	--------------------------------------------------------------------------------
	*	// Get
	*	--------------------------------------------------------------------------------
	*
	*	Get the documents based upon the passed parameters
	*
	*	@usage : $this->mongo_db->get('foo', array('bar' => 'something'));
	*/
	
	 public function get($collection = "")
	 {
	 	if(empty($collection))
	 	    $collection = $this->_table;
	 	if (empty($collection)) {
	 	    return $this->display_error('db_must_set_table');
	 	}
	 	if (isset($this->wheres['_id']) and ! ($this->wheres['_id'] instanceof MongoId))
		{
			$this->wheres['_id'] = new MongoId($this->wheres['_id']);
		}
	 		 	
	 	$documents = $this->_adapter->connect->{$collection}
	 	                                     ->find($this->wheres, $this->selects)
	 	                                     ->limit((int) $this->limit)
	 	                                     ->skip((int) $this->offset)
	 	                                     ->sort($this->sorts);
	 	
	 	// Clear
	 	$this->_clear();
	 	
	 	$returns = array();
	 	
	 	while ($documents->hasNext())
		{
			$returns[] = (object) $documents->getNext();
		}
	 	
	    return (object)$returns;
	 }
	
	/**
	*	--------------------------------------------------------------------------------
	*	Count
	*	--------------------------------------------------------------------------------
	*
	*	Count the documents based upon the passed parameters
	*
	*	@usage : $this->mongo_db->get('foo');
	*/
	
	public function count($collection = "") {
		if (empty($collection))
		{
	        $collection = $this->_table;
		}
		if (empty($collection)) {
		    return $this->display_error('db_must_set_table');
		}
		$count = $this->_adapter->connect->{$collection}->find($this->wheres)->limit((int) $this->limit)->skip((int) $this->offset)->count();
		$this->_clear();
		return ($count);
	}
	
	/**
	*	--------------------------------------------------------------------------------
	*	//! Insert
	*	--------------------------------------------------------------------------------
	*
	*	Insert a new document into the passed collection
	*
	*	@usage : $this->mongo_db->insert('foo', $data = array());
	*/
	
	public function insert($insert = array(), $collection = "")
	{
	    if (empty($collection))
		{
	        $collection = $this->_table;
		}
		if (empty($collection)) {
		    return $this->display_error('db_must_set_table');
		}
		if (count($insert) == 0 || !is_array($insert))
		{
			return $this->display_error('db_must_use_set');
		}
		try
		{
			$this->_adapter->connect->{$collection}->insert($insert, array($this->_adapter->querySafe => TRUE));
			if (isset($insert['_id']))
			{
				return $insert['_id'];
			}
			else
			{
				return false;
			}
		}
		catch (MongoCursorException $e)
		{
		    return $this->display_error($e->getMessage());
		}
	}
	
	
	/**
	*	--------------------------------------------------------------------------------
	*	//! Update
	*	--------------------------------------------------------------------------------
	*
	*	Updates a single document
	*
	*	@usage: $this->mongo_db->update('foo', $data = array());
	*/
	
	public function update($data = array(), $collection = "", $options = array())
	{
		if (empty($collection))
		{
			$collection = $this->_table;
		}
		if (empty($collection)) {
		    return $this->display_error('db_must_set_table');
		}
		if (is_array($data) && count($data) > 0)
		{
			$this->set($data);
		}
		if (count($this->updates) == 0)
		{
		    return $this->display_error('db_must_use_set in update');
		}		
		if (isset($this->wheres['_id']) and ! ($this->wheres['_id'] instanceof MongoId))
		{
			$this->wheres['_id'] = new MongoId($this->wheres['_id']);
		}		
		try
		{
			$options = array_merge($options, array($this->_adapter->querySafe => TRUE, 'multiple' => FALSE));
			$this->_adapter->connect->{$collection}->update($this->wheres, $this->updates, $options);
			$this->_clear();
			return true;
		}
		catch (MongoCursorException $e)
		{
		    return $this->display_error($e->getMessage());
		}
	}
	
	
	/**
	*	--------------------------------------------------------------------------------
	*	Update all
	*	--------------------------------------------------------------------------------
	*
	*	Updates a collection of documents
	*
	*	@usage: $this->mongo_db->update_all('foo', $data = array());
	*/
	
	public function update_all($data = array(), $collection = "", $options=array())
	{
	    if (empty($collection))
		{
			$collection = $this->_table;
		}
		if (empty($collection)) {
		    return $this->display_error('db_must_set_table');
		}
	    if (is_array($data) && count($data) > 0)
		{
			$this->set($data);
		}
		if (count($this->updates) == 0)
		{
		    return $this->display_error('db_must_use_set in update');
		}	
	 	if (isset($this->wheres['_id']) and ! ($this->wheres['_id'] instanceof MongoId))
		{
			$this->wheres['_id'] = new MongoId($this->wheres['_id']);
		}		
		try
		{
			$options = array_merge($options, array($this->_adapter->querySafe => TRUE, 'multiple' => TRUE));
			$this->_adapter->connect->{$collection}->update($this->wheres, $this->updates, $options);
			$this->_clear();
			return true;
		}
		catch (MongoCursorException $e)
		{
			show_error("Update of data into MongoDB failed: {$e->getMessage()}", 500);
		}
	}
	
	/**
	*	--------------------------------------------------------------------------------
	*	Inc
	*	--------------------------------------------------------------------------------
	*
	*	Increments the value of a field
	*
	*	@usage: $this->mongo_db->where(array('blog_id'=>123))->inc(array('num_comments' => 1))->update('blog_posts');
	*/
	
	public function inc($fields = array(), $value = 0)
	{
		$this->_update_init('$inc');
		
		if (is_string($fields))
		{
			$this->updates['$inc'][$fields] = $value;
		}
		
		elseif (is_array($fields))
		{
			foreach ($fields as $field => $value)
			{
				$this->updates['$inc'][$field] = $value;
			}
		}
		
		return $this;
	}
	
	/**
	*	--------------------------------------------------------------------------------
	*	Dec
	*	--------------------------------------------------------------------------------
	*
	*	Decrements the value of a field
	*
	*	@usage: $this->mongo_db->where(array('blog_id'=>123))->dec(array('num_comments' => 1))->update('blog_posts');
	*/
	
	public function dec($fields = array(), $value = 0)
	{
		$this->_update_init('$inc');
		
		if (is_string($fields))
		{
			$this->updates['$inc'][$fields] = $value;
		}
		
		elseif (is_array($fields))
		{
			foreach ($fields as $field => $value)
			{
				$this->updates['$inc'][$field] = $value;
			}
		}
		
		return $this;
	}
	
	/**
	*	--------------------------------------------------------------------------------
	*	Set
	*	--------------------------------------------------------------------------------
	*
	*	Sets a field to a value
	*
	*	@usage: $this->mongo_db->where(array('blog_id'=>123))->set('posted', 1)->update('blog_posts');
	*	@usage: $this->mongo_db->where(array('blog_id'=>123))->set(array('posted' => 1, 'time' => time()))->update('blog_posts');
	*/
	
	public function set($fields, $value = NULL)
	{
		$this->_update_init('$set');
		
		if (is_string($fields))
		{
			$this->updates['$set'][$fields] = $value;
		}
		
		elseif (is_array($fields))
		{
			foreach ($fields as $field => $value)
			{
				$this->updates['$set'][$field] = $value;
			}
		}
		
		return $this;
	}
	
	/**
	*	--------------------------------------------------------------------------------
	*	Unset
	*	--------------------------------------------------------------------------------
	*
	*	Unsets a field (or fields)
	*
	*	@usage: $this->mongo_db->where(array('blog_id'=>123))->unset('posted')->update('blog_posts');
	*	@usage: $this->mongo_db->where(array('blog_id'=>123))->set(array('posted','time'))->update('blog_posts');
	*/

	public function unset_field($fields)
	{
		$this->_update_init('$unset');
		
		if (is_string($fields))
		{
			$this->updates['$unset'][$fields] = 1;
		}
		
		elseif (is_array($fields))
		{
			foreach ($fields as $field)
			{
				$this->updates['$unset'][$field] = 1;
			}
		}
		
		return $this;
	}
	
	/**
	*	--------------------------------------------------------------------------------
	*	Push
	*	--------------------------------------------------------------------------------
	*
	*	Pushes values into a field (field must be an array)
	*
	*	@usage: $this->mongo_db->where(array('blog_id'=>123))->push('comments', array('text'=>'Hello world'))->update('blog_posts');
	*	@usage: $this->mongo_db->where(array('blog_id'=>123))->push(array('comments' => array('text'=>'Hello world')), 'viewed_by' => array('Alex')->update('blog_posts');
	*/

	public function push($fields, $value = array())
	{
		$this->_update_init('$push');
		
		if (is_string($fields))
		{
			$this->updates['$push'][$fields] = $value;
		}
		
		elseif (is_array($fields))
		{
			foreach ($fields as $field => $value)
			{
				$this->updates['$push'][$field] = $value;
			}
		}
		
		return $this;
	}
	
	/*public function push_all($fields, $value = array())
	{
		$this->_update_init('$pushAll');
		
		if (is_string($fields))
		{
			$this->updates['$pushAll'][$fields] = $value;
		}
		
		elseif (is_array($fields))
		{
			foreach ($fields as $field => $value)
			{
				$this->updates['$pushAll'][$field] = $value;
			}
		}
		
		return $this;
	}*/
	
	/**
	*	--------------------------------------------------------------------------------
	*	Pop
	*	--------------------------------------------------------------------------------
	*
	*	Pops the last value from a field (field must be an array)
	*
	*	@usage: $this->mongo_db->where(array('blog_id'=>123))->pop('comments')->update('blog_posts');
	*	@usage: $this->mongo_db->where(array('blog_id'=>123))->pop(array('comments', 'viewed_by'))->update('blog_posts');
	*/
	
	public function pop($fields)
	{
		$this->_update_init('$pop');
		
		if (is_string($fields))
		{
			$this->updates['$pop'][$fields] = -1;
		}
		
		elseif (is_array($fields))
		{
			foreach ($fields as $field)
			{
				$this->updates['$pop'][$field] = -1;
			}
		}
		
		return $this;
	}

	/**
	*	--------------------------------------------------------------------------------
	*	Pull
	*	--------------------------------------------------------------------------------
	*
	*	Removes by an array by the value of a field
	*
	*	@usage: $this->mongo_db->pull('comments', array('comment_id'=>123))->update('blog_posts');
	*/
	
	public function pull($field = "", $value = array())
	{
		$this->_update_init('$pull');
	
		$this->updates['$pull'] = array($field => $value);
		
		return $this;
	}
	
	/*public function pull_all($field = "", $value = array())
	{
		$this->_update_init('$pullAll');
	
		$this->updates['$pullAll'] = array($field => $value);
		
		return $this;
	}*/
	
	/**
	*	--------------------------------------------------------------------------------
	*	Rename field
	*	--------------------------------------------------------------------------------
	*
	*	Renames a field
	*
	*	@usage: $this->mongo_db->where(array('blog_id'=>123))->rename_field('posted_by', 'author')->update('blog_posts');
	*/
	
	public function rename_field($old, $new)
	{
		$this->_update_init('$rename');
	
		$this->updates['$rename'][] = array($old => $new);
		
		return $this;
	}
		
	/**
	*	--------------------------------------------------------------------------------
	*	//! Delete
	*	--------------------------------------------------------------------------------
	*
	*	delete document from the passed collection based upon certain criteria
	*
	*	@usage : $this->mongo_db->delete('foo');
	*/
	
	public function delete($collection = "")
	{
	    if (empty($collection))
	    {
	        $collection = $this->_table;
	    }
	    if (empty($collection)) {
	        return $this->display_error('db_must_set_table');
	    }
	 	if (isset($this->wheres['_id']) and ! ($this->wheres['_id'] instanceof MongoId))
		{
			$this->wheres['_id'] = new MongoId($this->wheres['_id']);
		}
		try
		{
			$this->_adapter->connect->{$collection}->remove($this->wheres, array($this->_adapter->querySafe => TRUE, 'justOne' => TRUE));
			$this->_clear();
			return true;
		}
		catch (MongoCursorException $e)
		{
			$this->display_error($e->getMessage());
		}
	}
	
	/**
	*	--------------------------------------------------------------------------------
	*	Delete all
	*	--------------------------------------------------------------------------------
	*
	*	Delete all documents from the passed collection based upon certain criteria
	*
	*	@usage : $this->mongo_db->delete_all('foo', $data = array());
	*/
	
	 public function delete_all($collection = "")
	 {
		if (empty($collection))
		{
		    $collection = $this->_table;
		}
		if (empty($collection)) {
		    return $this->display_error('db_must_set_table');
		}
	 	if (isset($this->wheres['_id']) and ! ($this->wheres['_id'] instanceof MongoId))
		{
			$this->wheres['_id'] = new MongoId($this->wheres['_id']);
		}
		try
		{
			$this->_adapter->connect->{$collection}->remove($this->wheres, array($this->_adapter->querySafe => TRUE, 'justOne' => FALSE));
			$this->_clear();
			return true;
		}
		catch (MongoCursorException $e)
		{
			return $this->display_error($e->getMessage());
		}
	}
	
	/**
	*	--------------------------------------------------------------------------------
	*	//! Command
	*	--------------------------------------------------------------------------------
	*
	*	Runs a MongoDB command (such as GeoNear). See the MongoDB documentation for more usage scenarios:
	*	http://dochub.mongodb.org/core/commands
	*
	*	@usage : $this->mongo_db->command(array('geoNear'=>'buildings', 'near'=>array(53.228482, -0.547847), 'num' => 10, 'nearSphere'=>true));
	*/
	
	public function command($query = array())
	{
		try
		{
			$run = $this->_adapter->connect->command($query);
			return $run;
		}
		
		catch (MongoCursorException $e)
		{
			return $this->display_error($e->getMessage());
		}
	}
	
	/**
	*	--------------------------------------------------------------------------------
	*	//! Add indexes
	*	--------------------------------------------------------------------------------
	*
	*	Ensure an index of the keys in a collection with optional parameters. To set values to descending order,
	*	you must pass values of either -1, FALSE, 'desc', or 'DESC', else they will be
	*	set to 1 (ASC).
	*
	*	@usage : $this->mongo_db->add_index($collection, array('first_name' => 'ASC', 'last_name' => -1), array('unique' => TRUE));
	*/
	
	public function add_index($keys = array(), $collection = "", $options = array())
	{
	    if (empty($collection))
		{
		    $collection = $this->_table;
		}
		if (empty($collection)) {
		    return $this->display_error('db_must_set_table');
		}
		if (empty($keys) || ! is_array($keys))
		{
			return $this->display_error('add_index must set key');
		}
		foreach ($keys as $col => $val)
		{
			if($val == -1 || $val === FALSE || strtolower($val) == 'desc')
			{
				$keys[$col] = -1; 
			}
			else
			{
				$keys[$col] = 1;
			}
		}
		
		if ($this->_adapter->connect->{$collection}->ensureIndex($keys, $options) == TRUE)
		{
			$this->_clear();
			return $this;
		}
		else
		{
		    return $this->display_error("An error occured when trying to add an index to MongoDB Collection");
		}
	}
	
	
	
	/**
	*	--------------------------------------------------------------------------------
	*	Remove index
	*	--------------------------------------------------------------------------------
	*
	*	Remove an index of the keys in a collection. To set values to descending order,
	*	you must pass values of either -1, FALSE, 'desc', or 'DESC', else they will be
	*	set to 1 (ASC).
	*
	*	@usage : $this->mongo_db->remove_index($collection, array('first_name' => 'ASC', 'last_name' => -1));
	*/
	
	public function remove_index($keys = array(), $collection = "", $options = array())
	{
	    if (empty($collection))
	    {
	        $collection = $this->_table;
	    }
	    if (empty($collection)) {
	        return $this->display_error('db_must_set_table');
	    }
	    if (empty($keys) || ! is_array($keys))
	    {
	        return $this->display_error('add_index must set key');
	    }
		if ($this->_adapter->connect->{$collection}->deleteIndex($keys, $options) == TRUE)
		{
			$this->_clear();
			return $this;
		}
		else
		{
			return $this->display_error("An error occured when trying to remove an index from MongoDB Collection");
		}
	}
	
	/**
	*	--------------------------------------------------------------------------------
	*	Remove all indexes
	*	--------------------------------------------------------------------------------
	*
	*	Remove all indexes from a collection.
	*
	*	@usage : $this->mongo_db->remove_all_index($collection);
	*/
	
	public function remove_all_indexes($collection = "")
	{
	    if (empty($collection))
	    {
	        $collection = $this->_table;
	    }
	    if (empty($collection)) {
	        return $this->display_error('db_must_set_table');
	    }
		$this->_adapter->connect->{$collection}->deleteIndexes();
		$this->_clear();
		return $this;
	}
	
	/**
	*	--------------------------------------------------------------------------------
	*	List indexes
	*	--------------------------------------------------------------------------------
	*
	*	Lists all indexes in a collection.
	*
	*	@usage : $this->mongo_db->list_indexes($collection);
	*/
	public function list_indexes($collection = "")
	{
	    if (empty($collection))
	    {
	        $collection = $this->_table;
	    }
	    if (empty($collection)) {
	        return $this->display_error('db_must_set_table');
	    }
		return $this->_adapter->connect->{$collection}->getIndexInfo();
	}
	
	
	/**
	*	--------------------------------------------------------------------------------
	*	_clear
	*	--------------------------------------------------------------------------------
	*
	*	Resets the class variables to default settings
	*/
	
	private function _clear()
	{
		$this->selects	= array();
		$this->updates	= array();
		$this->wheres	= array();
		$this->limit	= 999999;
		$this->offset	= 0;
		$this->sorts	= array();
	}

	/**
	*	--------------------------------------------------------------------------------
	*	Where initializer
	*	--------------------------------------------------------------------------------
	*
	*	Prepares parameters for insertion in $wheres array().
	*/
	
	private function _where_init($param)
	{
		if ( ! isset($this->wheres[$param]))
		{
			$this->wheres[ $param ] = array();
		}
	}
	
	/**
	*	--------------------------------------------------------------------------------
	*	Update initializer
	*	--------------------------------------------------------------------------------
	*
	*	Prepares parameters for insertion in $updates array().
	*/
	
	private function _update_init($method)
	{
		if ( ! isset($this->updates[$method]))
		{
			$this->updates[ $method ] = array();
		}
	}
	
	public function display_error ($str)
	{
	    require_once 'XPHP/Db/Exception.php';
	    throw new XPHP_Db_Exception($str);
	}
}