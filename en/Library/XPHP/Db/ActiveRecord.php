<?php
/**
 * XPHP Framework
 *
 * LICENSE
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * @category	XPHP
 * @package		XPHP_Db
 * @author		XWEB Dev Team - ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2010-2011 XWEB. (http://xweb.vn)	-	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://xphp.xweb.vn/license.html     GNU GPL License	-	http://codeigniter.com/user_guide/license.html
 * @version		$Id: ActiveRecord.php 20118 2011-22-08 02:05:09 Mr.UBKey $
 */
/**
 * Lớp hỗ trợ truy vấn CSDL ActiveRecord
 * @category	XPHP
 * @package		XPHP_Db
 * @author		ExpressionEngine Dev Team	-	XWEB Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc. - Copyright (c) 2010-2011 XWEB. (http://xweb.vn)
 * @license		http://codeigniter.com/user_guide/license.html	-	http://xphp.xweb.vn/license.html
 */
class XPHP_Db_ActiveRecord
{
    /**
     * Lớp Adapter hỗ trợ kết nối, truy xuất tới Mysql
     * @var XPHP_Db_Adapter_Abstract
     */
    private $_adapter;
    private $_table;
    /**
     * Lớp Adapter hỗ trợ kết nối, truy xuất tới Mysql
     * @param XPHP_Db_Adapter_Abstract $adapter
     */
    public function __construct ($adapter)
    {
        $this->_adapter = $adapter;
    }
    var $ar_select = array();
    var $ar_distinct = FALSE;
    var $ar_from = array();
    var $ar_join = array();
    var $ar_where = array();
    var $ar_like = array();
    var $ar_groupby = array();
    var $ar_having = array();
    var $ar_limit = FALSE;
    var $ar_offset = FALSE;
    var $ar_order = FALSE;
    var $ar_orderby = array();
    var $ar_set = array();
    var $ar_wherein = array();
    var $ar_aliased_tables = array();
    var $ar_store_array = array();
    // Active Record Caching variables
    var $ar_caching = FALSE;
    var $ar_cache_exists = array();
    var $ar_cache_select = array();
    var $ar_cache_from = array();
    var $ar_cache_join = array();
    var $ar_cache_where = array();
    var $ar_cache_like = array();
    var $ar_cache_groupby = array();
    var $ar_cache_having = array();
    var $ar_cache_orderby = array();
    var $ar_cache_set = array();
    // --------------------------------------------------------------------
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
    // --------------------------------------------------------------------
    /**
     * Select
     *
     * Tạo ra phần SELECT của câu truy vấn
     * 
     * @access	public
     * @param	string
     * @return	XPHP_Db_ActiveRecord
     */
    public function select ($select = '*', $escape = NULL)
    {
        // Set the global value if this was sepecified	
        if (is_bool($escape)) {
            $this->_protect_identifiers = $escape;
        }
        if (is_string($select)) {
            $select = explode(',', $select);
        }
        foreach ($select as $val) {
            $val = trim($val);
            if ($val != '') {
                $this->ar_select[] = $val;
                if ($this->ar_caching === TRUE) {
                    $this->ar_cache_select[] = $val;
                    $this->ar_cache_exists[] = 'select';
                }
            }
        }
        return $this;
    }
    // --------------------------------------------------------------------
    /**
     * Select Max
     *
     * Tạo ra một phần SELECT MAX (field) của một câu truy vấn
     *
     * @access	public
     * @param	string	tên field
     * @param	string	bí danh
     * @return	XPHP_Db_ActiveRecord
     */
    public function select_max ($select = '', $alias = '')
    {
        return $this->_max_min_avg_sum($select, $alias, 'MAX');
    }
    // --------------------------------------------------------------------
    /**
     * Select Min
     *
     * Tạo ra một phần SELECT MIN(field) của một câu truy vấn
     *
     * @access	public
     * @param	string	tên field
     * @param	string	bí danh
     * @return	XPHP_Db_ActiveRecord
     */
    public function select_min ($select = '', $alias = '')
    {
        return $this->_max_min_avg_sum($select, $alias, 'MIN');
    }
    // --------------------------------------------------------------------
    /**
     * Select Average
     *
     * Tạo ra một phần SELECT AVG (field) của một câu truy vấn
     *
     * @access	public
     * @param	string	tên field
     * @param	string	bí danh
     * @return	XPHP_Db_ActiveRecord
     */
    public function select_avg ($select = '', $alias = '')
    {
        return $this->_max_min_avg_sum($select, $alias, 'AVG');
    }
    // --------------------------------------------------------------------
    /**
     * Select Sum
     *
     * Tạo ra một phần SELECT SUM (field) của một câu truy vấn
     *
     * @access	public
     * @param	string	tên field
     * @param	string	bí danh
     * @return	XPHP_Db_ActiveRecord
     */
    public function select_sum ($select = '', $alias = '')
    {
        return $this->_max_min_avg_sum($select, $alias, 'SUM');
    }
    // --------------------------------------------------------------------
    /**
     * Phương thức xử lý các phương thức:
     *
     * select_max()
     * select_min()
     * select_avg()
     * select_sum()
     * 
     * @access	public
     * @param	string	tên field
     * @param	string	bí danh
     * @param	mixed	loại
     * @return	XPHP_Db_ActiveRecord
     */
    public function _max_min_avg_sum ($select = '', $alias = '', $type = 'MAX')
    {
        if (! is_string($select) or $select == '') {
            $this->display_error('db_invalid_query');
        }
        $type = strtoupper($type);
        if (! in_array($type, array('MAX', 'MIN', 'AVG', 'SUM'))) {
            $this->display_error('Invalid function type: ' . $type);
        }
        if ($alias == '') {
            $alias = $this->_create_alias_from_table(trim($select));
        }
        $sql = $type . '(' . $this->_protect_identifiers(trim($select)) . ') AS ' .
         $alias;
        $this->ar_select[] = $sql;
        if ($this->ar_caching === TRUE) {
            $this->ar_cache_select[] = $sql;
            $this->ar_cache_exists[] = 'select';
        }
        return $this;
    }
    // --------------------------------------------------------------------
    /**
     * Xác định tên bí danh dựa trên bảng
     *
     * @access	private
     * @param	string
     * @return	string
     */
    public function _create_alias_from_table ($item)
    {
        if (strpos($item, '.') !== FALSE) {
            return end(explode('.', $item));
        }
        return $item;
    }
    // --------------------------------------------------------------------
    /**
     * DISTINCT
     *
     * Thiết lập một đánh dấu cho trình biên dịch chuỗi truy vấn để thêm DISTINCT
     *
     * @access	public
     * @param	bool
     * @return	XPHP_Db_ActiveRecord
     */
    public function distinct ($val = TRUE)
    {
        $this->ar_distinct = (is_bool($val)) ? $val : TRUE;
        return $this;
    }
    // --------------------------------------------------------------------
    /**
     * From
     *
     * Tạo ra FROM của câu truy vấn
     *
     * @access	public
     * @param	mixed string | array
     * @return	XPHP_Db_ActiveRecord
     */
    public function from ($from)
    {
        foreach ((array) $from as $val) {
            if (strpos($val, ',') !== FALSE) {
                foreach (explode(',', $val) as $v) {
                    $v = trim($v);
                    $this->_track_aliases($v);
                    $this->ar_from[] = $this->_protect_identifiers($v, TRUE, 
                    NULL, FALSE);
                    if ($this->ar_caching === TRUE) {
                        $this->ar_cache_from[] = $this->_protect_identifiers($v, 
                        TRUE, NULL, FALSE);
                        $this->ar_cache_exists[] = 'from';
                    }
                }
            } else {
                $val = trim($val);
                // Extract any aliases that might exist.  We use this information
                // in the _protect_identifiers to know whether to add a table prefix 
                $this->_track_aliases($val);
                $this->ar_from[] = $this->_protect_identifiers($val, TRUE, NULL, 
                FALSE);
                if ($this->ar_caching === TRUE) {
                    $this->ar_cache_from[] = $this->_protect_identifiers($val, 
                    TRUE, NULL, FALSE);
                    $this->ar_cache_exists[] = 'from';
                }
            }
        }
        return $this;
    }
    // --------------------------------------------------------------------
    /**
     * Join
     *
     * Tạo ra JOIN của các truy vấn
     *
     * @access	public
     * @param	string	tên bảng cần join
     * @param	string	điều kiện join
     * @param	string	kiểu join
     * @return	XPHP_Db_ActiveRecord
     */
    public function join ($table, $cond, $type = '')
    {
        if ($type != '') {
            $type = strtoupper(trim($type));
            if (! in_array($type, 
            array('LEFT', 'RIGHT', 'OUTER', 'INNER', 'LEFT OUTER', 
            'RIGHT OUTER'))) {
                $type = '';
            } else {
                $type .= ' ';
            }
        }
        // Extract any aliases that might exist.  We use this information
        // in the _protect_identifiers to know whether to add a table prefix 
        $this->_track_aliases($table);
        // Strip apart the condition and protect the identifiers
        if (preg_match('/([\w\.]+)([\W\s]+)(.+)/', $cond, $match)) {
            $match[1] = $this->_protect_identifiers($match[1]);
            $match[3] = $this->_protect_identifiers($match[3]);
            $cond = $match[1] . $match[2] . $match[3];
        }
        // Assemble the JOIN statement
        $join = $type . 'JOIN ' .
         $this->_protect_identifiers($table, TRUE, NULL, FALSE) . ' ON ' . $cond;
        $this->ar_join[] = $join;
        if ($this->ar_caching === TRUE) {
            $this->ar_cache_join[] = $join;
            $this->ar_cache_exists[] = 'join';
        }
        return $this;
    }
    // --------------------------------------------------------------------
    /**
     * Where
     *
     * Tạo ra các mệnh đề WHERE của câu truy vấn.
     * Gọi phương thức này nhiều lần để AND
     *
     * @access	public
     * @param	mixed
     * @param	mixed
     * @return	XPHP_Db_ActiveRecord
     */
    public function where ($key, $value = NULL, $escape = TRUE)
    {
        return $this->_where($key, $value, 'AND ', $escape);
    }
    // --------------------------------------------------------------------
    /**
     * OR Where
     *
     * Tạo ra các mệnh đề WHERE của câu truy vấn
     * Gọi phương thức này nhiều lần để OR
     *
     * @access	public
     * @param	mixed
     * @param	mixed
     * @return	XPHP_Db_ActiveRecord
     */
    public function or_where ($key, $value = NULL, $escape = TRUE)
    {
        return $this->_where($key, $value, 'OR ', $escape);
    }
    // --------------------------------------------------------------------
    /**
     * orwhere() là một tên khác của or_where()
     * @return XPHP_Db_ActiveRecord
     */
    public function orwhere ($key, $value = NULL, $escape = TRUE)
    {
        return $this->or_where($key, $value, $escape);
    }
    // --------------------------------------------------------------------
    /**
     * Where
     * 
     * Được gọi bởi where() hoặc orwhere()
     *
     * @access	private
     * @param	mixed
     * @param	mixed
     * @param	string
     * @return	XPHP_Db_ActiveRecord
     */
    public function _where ($key, $value = NULL, $type = 'AND ', $escape = NULL)
    {
        if (! is_array($key)) {
            $key = array($key => $value);
        }
        // If the escape value was not set will will base it on the global setting
        if (! is_bool($escape)) {
            $escape = $this->_protect_identifiers;
        }
        foreach ($key as $k => $v) {
            $prefix = (count($this->ar_where) == 0 and
             count($this->ar_cache_where) == 0) ? '' : $type;
            if (is_null($v) && ! $this->_has_operator($k)) {
                // value appears not to have been set, assign the test to IS NULL
                $k .= ' IS NULL';
            }
            if (! is_null($v)) {
                if ($escape === TRUE) {
                    $k = $this->_protect_identifiers($k, FALSE, $escape);
                    $v = ' ' . $this->escape($v);
                }
                if (! $this->_has_operator($k)) {
                    $k .= ' =';
                }
            } else {
                $k = $this->_protect_identifiers($k, FALSE, $escape);
            }
            $this->ar_where[] = $prefix . $k . $v;
            if ($this->ar_caching === TRUE) {
                $this->ar_cache_where[] = $prefix . $k . $v;
                $this->ar_cache_exists[] = 'where';
            }
        }
        return $this;
    }
    // --------------------------------------------------------------------
    /**
     * Where_in
     *
     * Tạo ra một mệnh đề WHERE field IN ('item', 'item') trong câu truy vấn tham gia với
     * AND nếu thích hợp
     *
     * @access	public
     * @param	string	tên trường
     * @param	array	các giá trị
     * @return	XPHP_Db_ActiveRecord
     */
    public function where_in ($key = NULL, $values = NULL)
    {
        return $this->_where_in($key, $values);
    }
    // --------------------------------------------------------------------
    /**
     * Where_in_or
     *
     * Tạo ra một mệnh đề WHERE field IN ('item', 'item') trong câu truy vấn tham gia với
     * OR nếu thích hợp
     *
     * @access	public
     * @param	string	tên trường
     * @param	array	các giá trị
     * @return	XPHP_Db_ActiveRecord
     */
    public function or_where_in ($key = NULL, $values = NULL)
    {
        return $this->_where_in($key, $values, FALSE, 'OR ');
    }
    // --------------------------------------------------------------------
    /**
     * Where_not_in
     * 
     * Tạo ra một mệnh đề WHERE field NOT IN ('item', 'item') trong câu truy vấn tham gia
     * với AND nếu thích hợp
     *
     * @access	public
     * @param	string	tên field
     * @param	array	các giá trị
     * @return	XPHP_Db_ActiveRecord
     */
    public function where_not_in ($key = NULL, $values = NULL)
    {
        return $this->_where_in($key, $values, TRUE);
    }
    // --------------------------------------------------------------------
    /**
     * Where_not_in_or
     *
     * Tạo một mệnh đề WHERE field NOT IN ('item', 'item') trong câu truy vấn tham gia
     * với OR nếu thích hợp
     *
     * @access	public
     * @param	string	tên field
     * @param	array	các giá trị
     * @return	XPHP_Db_ActiveRecord
     */
    public function or_where_not_in ($key = NULL, $values = NULL)
    {
        return $this->_where_in($key, $values, TRUE, 'OR ');
    }
    // --------------------------------------------------------------------
    /**
     * Where_in
     *
     * Được gọi bởi where_in, where_in_or, where_not_in, where_not_in_or
     *
     * @access	public
     * @param	string	tên field
     * @param	array	các giá trị
     * @param	boolean IN or NOT IN
     * @param	string	
     * @return	XPHP_Db_ActiveRecord
     */
    public function _where_in ($key = NULL, $values = NULL, $not = FALSE, $type = 'AND ')
    {
        if ($key === NULL or $values === NULL) {
            return;
        }
        if (! is_array($values)) {
            $values = array($values);
        }
        $not = ($not) ? ' NOT' : '';
        foreach ($values as $value) {
            $this->ar_wherein[] = $this->escape($value);
        }
        $prefix = (count($this->ar_where) == 0) ? '' : $type;
        $where_in = $prefix . $this->_protect_identifiers($key) . $not . " IN (" .
         implode(", ", $this->ar_wherein) . ") ";
        $this->ar_where[] = $where_in;
        if ($this->ar_caching === TRUE) {
            $this->ar_cache_where[] = $where_in;
            $this->ar_cache_exists[] = 'where';
        }
        // reset the array for multiple calls
        $this->ar_wherein = array();
        return $this;
    }
    // --------------------------------------------------------------------
    /**
     * Like
     *
     * Tạo ra %LIKE% của một câu truy vấn.
     * Gọi phương thức nhiều lần để AND
     *
     * @access	public
     * @param	mixed
     * @param	mixed
     * @return	XPHP_Db_ActiveRecord
     */
    public function like ($field, $match = '', $side = 'both')
    {
        return $this->_like($field, $match, 'AND ', $side);
    }
    // --------------------------------------------------------------------
    /**
     * Not Like
     *
     * Tạo ra NOT LIKE của một câu truy vấn.
     * Gọi phương thức nhiều lần để AND
     *
     * @access	public
     * @param	mixed
     * @param	mixed
     * @return	XPHP_Db_ActiveRecord
     */
    public function not_like ($field, $match = '', $side = 'both')
    {
        return $this->_like($field, $match, 'AND ', $side, 'NOT');
    }
    // --------------------------------------------------------------------
    /**
     * OR Like
     *
     * Tạo ra %LIKE% trong câu truy vấn.
     * Gọi phương thức nhiều lần để OR
     *
     * @access	public
     * @param	mixed
     * @param	mixed
     * @return	XPHP_Db_ActiveRecord
     */
    public function or_like ($field, $match = '', $side = 'both')
    {
        return $this->_like($field, $match, 'OR ', $side);
    }
    // --------------------------------------------------------------------
    /**
     * OR Not Like
     *
     * Tạo ra một NOT LIKE trong một câu truy vấn
     * Gọi phương thức nhiều lần để OR
     *
     * @access	public
     * @param	mixed
     * @param	mixed
     * @return	XPHP_Db_ActiveRecord
     */
    public function or_not_like ($field, $match = '', $side = 'both')
    {
        return $this->_like($field, $match, 'OR ', $side, 'NOT');
    }
    // --------------------------------------------------------------------
    /**
     * orlike() là một tên khác của or_like()
     * @return XPHP_Db_ActiveRecord
     */
    public function orlike ($field, $match = '', $side = 'both')
    {
        return $this->or_like($field, $match, $side);
    }
    // --------------------------------------------------------------------
    /**
     * Like
     *
     * Được gọi bởi like() hoặc orlike()
     *
     * @access	private
     * @param	mixed
     * @param	mixed
     * @param	string
     * @return	XPHP_Db_ActiveRecord
     */
    public function _like ($field, $match = '', $type = 'AND ', $side = 'both', $not = '')
    {
        if (! is_array($field)) {
            $field = array($field => $match);
        }
        foreach ($field as $k => $v) {
            $k = $this->_protect_identifiers($k);
            $prefix = (count($this->ar_like) == 0) ? '' : $type;
            $v = $this->_adapter->escape_like_str($v);
            if ($side == 'before') {
                $like_statement = $prefix . " $k $not LIKE '%{$v}'";
            } elseif ($side == 'after') {
                $like_statement = $prefix . " $k $not LIKE '{$v}%'";
            } else {
                $like_statement = $prefix . " $k $not LIKE '%{$v}%'";
            }
            // some platforms require an escape sequence definition for LIKE wildcards
            if ($this->_adapter->_like_escape_str != '') {
                $like_statement = $like_statement . sprintf(
                $this->_adapter->_like_escape_str, 
                $this->_adapter->_like_escape_chr);
            }
            $this->ar_like[] = $like_statement;
            if ($this->ar_caching === TRUE) {
                $this->ar_cache_like[] = $like_statement;
                $this->ar_cache_exists[] = 'like';
            }
        }
        return $this;
    }
    // --------------------------------------------------------------------
    /**
     * GROUP BY
     *
     * @access	public
     * @param	string
     * @return	XPHP_Db_ActiveRecord
     */
    public function group_by ($by)
    {
        if (is_string($by)) {
            $by = explode(',', $by);
        }
        foreach ($by as $val) {
            $val = trim($val);
            if ($val != '') {
                $this->ar_groupby[] = $this->_protect_identifiers($val);
                if ($this->ar_caching === TRUE) {
                    $this->ar_cache_groupby[] = $this->_protect_identifiers(
                    $val);
                    $this->ar_cache_exists[] = 'groupby';
                }
            }
        }
        return $this;
    }
    // --------------------------------------------------------------------
    /**
     * groupby() là một tên khác của group_by()
     * @return XPHP_Db_ActiveRecord
     */
    public function groupby ($by)
    {
        return $this->group_by($by);
    }
    // --------------------------------------------------------------------
    /**
     * Sets the HAVING value
     *
     * Gọi phương thức nhiều lần để AND
     *
     * @access	public
     * @param	string
     * @param	string
     * @return	XPHP_Db_ActiveRecord
     */
    public function having ($key, $value = '', $escape = TRUE)
    {
        return $this->_having($key, $value, 'AND ', $escape);
    }
    // --------------------------------------------------------------------
    /**
     * orhaving() tên khác của or_having()
     * @return XPHP_Db_ActiveRecord
     */
    public function orhaving ($key, $value = '', $escape = TRUE)
    {
        return $this->or_having($key, $value, $escape);
    }
    // --------------------------------------------------------------------
    /**
     * Sets the OR HAVING value
     *
     * Gọi phương thức nhiều lần để OR
     *
     * @access	public
     * @param	string
     * @param	string
     * @return	XPHP_Db_ActiveRecord
     */
    public function or_having ($key, $value = '', $escape = TRUE)
    {
        return $this->_having($key, $value, 'OR ', $escape);
    }
    // --------------------------------------------------------------------
    /**
     * Sets the HAVING values
     *
     * Được gọi bởi having() hoặc or_having()
     *
     * @access	private
     * @param	string
     * @param	string
     * @return	XPHP_Db_ActiveRecord
     */
    public function _having ($key, $value = '', $type = 'AND ', $escape = TRUE)
    {
        if (! is_array($key)) {
            $key = array($key => $value);
        }
        foreach ($key as $k => $v) {
            $prefix = (count($this->ar_having) == 0) ? '' : $type;
            if ($escape === TRUE) {
                $k = $this->_protect_identifiers($k);
            }
            if (! $this->_has_operator($k)) {
                $k .= ' = ';
            }
            if ($v != '') {
                $v = ' ' . $this->escape_str($v);
            }
            $this->ar_having[] = $prefix . $k . $v;
            if ($this->ar_caching === TRUE) {
                $this->ar_cache_having[] = $prefix . $k . $v;
                $this->ar_cache_exists[] = 'having';
            }
        }
        return $this;
    }
    // --------------------------------------------------------------------
    /**
     * Sets the ORDER BY value
     *
     * @access	public
     * @param	string
     * @param	string	direction: asc or desc
     * @return	XPHP_Db_ActiveRecord
     */
    public function order_by ($orderby, $direction = '')
    {
        if (strtolower($direction) == 'random') {
            $orderby = ''; // Random results want or don't need a field name
            $direction = $this->_random_keyword;
        } elseif (trim($direction) != '') {
            $direction = (in_array(strtoupper(trim($direction)), 
            array('ASC', 'DESC'), TRUE)) ? ' ' . $direction : ' ASC';
        }
        if (strpos($orderby, ',') !== FALSE) {
            $temp = array();
            foreach (explode(',', $orderby) as $part) {
                $part = trim($part);
                if (! in_array($part, $this->ar_aliased_tables)) {
                    $part = $this->_protect_identifiers(trim($part));
                }
                $temp[] = $part;
            }
            $orderby = implode(', ', $temp);
        } else 
            if ($direction != $this->_adapter->_random_keyword) {
                $orderby = $this->_protect_identifiers($orderby);
            }
        $orderby_statement = $orderby . $direction;
        $this->ar_orderby[] = $orderby_statement;
        if ($this->ar_caching === TRUE) {
            $this->ar_cache_orderby[] = $orderby_statement;
            $this->ar_cache_exists[] = 'orderby';
        }
        return $this;
    }
    // --------------------------------------------------------------------
    /**
     * orderby() là tên khác của order_by()
     * @return XPHP_Db_ActiveRecord
     */
    public function orderby ($orderby, $direction = '')
    {
        return $this->order_by($orderby, $direction);
    }
    // --------------------------------------------------------------------
    /**
     * Sets the LIMIT value
     *
     * @access	public
     * @param	integer	the limit value
     * @param	integer	the offset value
     * @return	XPHP_Db_ActiveRecord
     */
    public function limit ($value, $offset = '')
    {
        $this->ar_limit = $value;
        if ($offset != '') {
            $this->ar_offset = $offset;
        }
        return $this;
    }
    // --------------------------------------------------------------------
    /**
     * Sets the OFFSET value
     *
     * @access	public
     * @param	integer	the offset value
     * @return	XPHP_Db_ActiveRecord
     */
    public function offset ($offset)
    {
        $this->ar_offset = $offset;
        return $this;
    }
    // --------------------------------------------------------------------
    /**
     * Phương thức "set" cho phép key/value được sử dụng để inserting hoặc updating
     *
     * @access	public
     * @param	mixed
     * @param	string
     * @param	boolean
     * @return	XPHP_Db_ActiveRecord
     */
    public function set ($key, $value = '', $escape = TRUE)
    {
        $key = $this->_object_to_array($key);
        if (! is_array($key)) {
            $key = array($key => $value);
        }
        foreach ($key as $k => $v) {
            if ($escape === FALSE) {
                $this->ar_set[$this->_protect_identifiers($k)] = $v;
            } else {
                $this->ar_set[$this->_protect_identifiers($k)] = $this->escape(
                $v);
            }
        }
        return $this;
    }
    // --------------------------------------------------------------------
    /**
     * Get
     * 
     * Biên dịch các câu lệnh select dựa trên các phương thức được gọi và chạy câu lệnh truy vấn
     *
     * @access	public
     * @param	string	tên bảng
     * @param	string	limit
     * @param	string	offset
     * @return	XPHP_Db_Result	
     */
    public function get ($table = '', $limit = null, $offset = null)
    {
        if (empty($table))
            $table = $this->_table;
        if (! empty($table)) {
            $this->_track_aliases($table);
            $this->from($table);
        }
        if (! is_null($limit)) {
            $this->limit($limit, $offset);
        }
        $sql = $this->_compile_select();
        $result = $this->_adapter->query($sql);
        $this->_reset_select();
        return $result;
    }
    
    /**
     * Chạy câu lênh query
     * @param string $query
     * @return	XPHP_Db_Result
     */
    public function query($query)
    {
        $result = $this->_adapter->query($query);
        return $result;
    }
    
    /**
     * "Count All Results" Truy vấn
     *
     * Tạo ra một chuỗi truy vấn mà đếm tất cả các bản ghi trả về bởi câu truy vấn Active Record
     * @access	public
     * @param	string
     * @return	string
     */
    public function count_all_results ($table = '')
    {
        if (empty($table))
            $table = $this->_table;
        if ($table != '') {
            $this->_track_aliases($table);
            $this->from($table);
        }
        $sql = $this->_compile_select(
        $this->_adapter->_count_string . $this->_protect_identifiers('numrows'));
        $query = $this->_adapter->query($sql);
        $this->_reset_select();
        if ($query->num_rows() == 0) {
            return '0';
        }
        $row = $query->row();
        return $row->numrows;
    }
    // --------------------------------------------------------------------
    /**
     * Get_Where
     *
     * Cho phép mệnh đề WHERE, LIMIT và OFFSET có thể thêm vào trực tiếp
     *
     * @access	public
     * @param	string	mệnh đề where
     * @param	string	limit
     * @param	string	offset
     * @return	XPHP_Db_Result
     */
    public function get_where ($table = '', $where = null, $limit = null, $offset = null)
    {
        if ($table != '') {
            $this->from($table);
        }
        if (! is_null($where)) {
            $this->where($where);
        }
        if (! is_null($limit)) {
            $this->limit($limit, $offset);
        }
        $sql = $this->_compile_select();
        $result = $this->_adapter->query($sql);
        $this->_reset_select();
        return $result;
    }
    // --------------------------------------------------------------------
    /**
     * getwhere() là một tên khác của get_where()
     * 
     * @return XPHP_Db_Result
     */
    public function getwhere ($table = '', $where = null, $limit = null, $offset = null)
    {
        return $this->get_where($table, $where, $limit, $offset);
    }
    // --------------------------------------------------------------------
    /**
     * Insert
     *
     * Biên dịch một chuỗi và chạy truy vấn
     *
     * @access	public
     * @param	string	Tên bảng để lấy ra kết quả
     * @param	array	Mảng các giá trị cần insert
     * @return	object
     */
    public function insert ($set = NULL, $table = '')
    {
        if (! is_null($set)) {
            $this->set($set);
        }
        if (count($this->ar_set) == 0) {
            if ($this->_adapter->db_debug) {
                return $this->display_error('db_must_use_set');
            }
            return FALSE;
        }
        //Nếu không truyền vào tên bảng lấy tên mặc định
        if (empty($table))
            $table = $this->_table;
        if (empty($table)) {
            if (! isset($this->ar_from[0])) {
                if ($this->_adapter->db_debug) {
                    return $this->display_error('db_must_set_table');
                }
                return FALSE;
            }
            $table = $this->ar_from[0];
        }
        $sql = $this->_adapter->_insert(
        $this->_protect_identifiers($table, TRUE, NULL, FALSE), 
        array_keys($this->ar_set), array_values($this->ar_set));
        $this->_reset_write();
        return $this->_adapter->simple_query($sql);
    }
    // --------------------------------------------------------------------
    /**
     * Update
     *
     * Biên dịch một chuỗi UPDATE và chạy câu truy vấn
     *
     * @access	public
     * @param	string	the table to retrieve the results from
     * @param	array	an associative array of update values
     * @param	mixed	the where clause
     * @return	object
     */
    public function update ($set = NULL, $where = NULL, $limit = NULL, $table = '')
    {
        // Combine any cached components with the current statements
        $this->_merge_cache();
        if (! is_null($set)) {
            $this->set($set);
        }
        if (count($this->ar_set) == 0) {
            if ($this->_adapter->db_debug) {
                return $this->display_error('db_must_use_set');
            }
            return FALSE;
        }
        //Nếu không truyền vào tên bảng lấy tên mặc định
        if (empty($table))
            $table = $this->_table;
        if (empty($table)) {
            if (! isset($this->ar_from[0])) {
                if ($this->_adapter->db_debug) {
                    return $this->display_error('db_must_set_table');
                }
                return FALSE;
            }
            $table = $this->ar_from[0];
        }
        if ($where != NULL) {
            $this->where($where);
        }
        if ($limit != NULL) {
            $this->limit($limit);
        }
        $sql = $this->_adapter->_update(
        $this->_protect_identifiers($table, TRUE, NULL, FALSE), $this->ar_set, 
        $this->ar_where, $this->ar_orderby, $this->ar_limit);
        $this->_reset_write();
        return $this->_adapter->simple_query($sql);
    }
    // --------------------------------------------------------------------
    /**
     * Empty Table
     *
     * Biên dịch một chuỗi DELETE và chạy câu truy vấn "DELETE FROM table"
     *
     * @access	public
     * @param	string	tên bảng
     * @return	object
     */
    public function empty_table ($table = '')
    {
        //Nếu không truyền vào tên bảng lấy tên mặc định
        if (empty($table))
            $table = $this->_table;
        if (empty($table)) {
            if (! isset($this->ar_from[0])) {
                if ($this->_adapter->db_debug) {
                    return $this->display_error('db_must_set_table');
                }
                return FALSE;
            }
            $table = $this->ar_from[0];
        } else {
            $table = $this->_protect_identifiers($table, TRUE, NULL, FALSE);
        }
        $sql = $this->_adapter->_delete($table);
        $this->_reset_write();
        return $this->_adapter->simple_query($sql);
    }
    // --------------------------------------------------------------------
    /**
     * Truncate
     *
     * Biên dịch một chuỗi truncate và chạy câu lệnh query
     * Nếu cơ sở dữ liệu không hỗ trợ lệnh truncate ()
     * Phương thức này sẽ map tới "DELETE FROM table"
     *
     * @access	public
     * @param	string	tên bảng
     * @return	object
     */
    public function truncate ($table = '')
    {
        //Nếu không truyền vào tên bảng lấy tên mặc định
        if (empty($table))
            $table = $this->_table;
        if (empty($table)) {
            if (! isset($this->ar_from[0])) {
                if ($this->_adapter->db_debug) {
                    return $this->display_error('db_must_set_table');
                }
                return FALSE;
            }
            $table = $this->ar_from[0];
        } else {
            $table = $this->_protect_identifiers($table, TRUE, NULL, FALSE);
        }
        $sql = $this->_adapter->_truncate($table);
        $this->_reset_write();
        return $this->_adapter->simple_query($sql);
    }
    // --------------------------------------------------------------------
    /**
     * Delete
     *
     * Compiles a delete string and runs the query
     * Biên dịch một chuỗi DELETE và chạy câu truy vấn
     *
     * @access	public
     * @param	mixed	strin | array Tên bảng 
     * @param	mixed	mệnh đề where
     * @param	mixed	limit
     * @param	boolean
     * @return	object
     */
    public function delete ($table = '', $where = '', $limit = NULL, $reset_data = TRUE)
    {
        // Combine any cached components with the current statements
        $this->_merge_cache();
        //Nếu không truyền vào tên bảng lấy tên mặc định
        if (empty($table))
            $table = $this->_table;
        if (empty($table)) {
            if (! isset($this->ar_from[0])) {
                if ($this->_adapter->db_debug) {
                    return $this->display_error('db_must_set_table');
                }
                return FALSE;
            }
            $table = $this->ar_from[0];
        } elseif (is_array($table)) {
            foreach ($table as $single_table) {
                $this->delete($single_table, $where, $limit, FALSE);
            }
            $this->_reset_write();
            return;
        } else {
            $table = $this->_protect_identifiers($table, TRUE, NULL, FALSE);
        }
        if ($where != '') {
            $this->where($where);
        }
        if ($limit != NULL) {
            $this->limit($limit);
        }
        if (count($this->ar_where) == 0 && count($this->ar_wherein) == 0 &&
         count($this->ar_like) == 0) {
            if ($this->_adapter->db_debug) {
                return $this->display_error('db_del_must_use_where');
            }
            return FALSE;
        }
        $sql = $this->_adapter->_delete($table, $this->ar_where, $this->ar_like, 
        $this->ar_limit);
        if ($reset_data) {
            $this->_reset_write();
        }
        return $this->_adapter->simple_query($sql);
    }
    // --------------------------------------------------------------------
    /**
     * DB Prefix
     *
     * Thêm một tiền tố phía trước nếu tồn tại trong cấu hình
     *
     * @access	public
     * @param	string tên bảng
     * @return	string
     */
    public function dbprefix ($table = '')
    {
        if ($table == '') {
            $this->display_error('db_table_name_required');
        }
        return $this->_adapter->getConfig('dbprefix') . $table;
    }
    // --------------------------------------------------------------------
    /**
     * Theo dõi các bí danh
     *
     * Được sử dụng để theo dõi các câu lệnh SQL bằng văn bản với các bảng aliased.
     *
     * @access	private
     * @param	string	Tên bảng để kiểm tra
     * @return	string
     */
    public function _track_aliases ($table)
    {
        if (is_array($table)) {
            foreach ($table as $t) {
                $this->_track_aliases($t);
            }
            return;
        }
        // Does the string contain a comma?  If so, we need to separate
        // the string into discreet statements
        if (strpos($table, ',') !== FALSE) {
            return $this->_track_aliases(explode(',', $table));
        }
        // if a table alias is used we can recognize it by a space
        if (strpos($table, " ") !== FALSE) {
            // if the alias is written with the AS keyword, remove it
            $table = preg_replace('/ AS /i', ' ', $table);
            // Grab the alias
            $table = trim(strrchr($table, " "));
            // Store the alias, if it doesn't already exist
            if (! in_array($table, $this->ar_aliased_tables)) {
                $this->ar_aliased_tables[] = $table;
            }
        }
    }
    // --------------------------------------------------------------------
    /**
     * Biên dịch câu lệnh SELECT
     *
     * Tạo ra một chuỗi truy vấn dựa trên đó các functions được sử dụng.
     * Không nên gọi trực tiếp. Phương thức get() gọi nó.
     *
     * @access	private
     * @return	string
     */
    public function _compile_select ($select_override = FALSE)
    {
        // Combine any cached components with the current statements
        $this->_merge_cache();
        // ----------------------------------------------------------------
        // Write the "select" portion of the query
        if ($select_override !== FALSE) {
            $sql = $select_override;
        } else {
            $sql = (! $this->ar_distinct) ? 'SELECT ' : 'SELECT DISTINCT ';
            if (count($this->ar_select) == 0) {
                $sql .= '*';
            } else {
                // Cycle through the "select" portion of the query and prep each column name.
                // The reason we protect identifiers here rather then in the select() function
                // is because until the user calls the from() function we don't know if there are aliases
                foreach ($this->ar_select as $key => $val) {
                    $this->ar_select[$key] = $this->_protect_identifiers($val);
                }
                $sql .= implode(', ', $this->ar_select);
            }
        }
        // ----------------------------------------------------------------
        // Write the "FROM" portion of the query
        if (count($this->ar_from) > 0) {
            $sql .= "\nFROM ";
            $sql .= $this->_adapter->_from_tables($this->ar_from);
        }
        // ----------------------------------------------------------------
        // Write the "JOIN" portion of the query
        if (count($this->ar_join) > 0) {
            $sql .= "\n";
            $sql .= implode("\n", $this->ar_join);
        }
        // ----------------------------------------------------------------
        // Write the "WHERE" portion of the query
        if (count($this->ar_where) > 0 or
         count($this->ar_like) > 0) {
            $sql .= "\n";
            $sql .= "WHERE ";
        }
        $sql .= implode("\n", $this->ar_where);
        // ----------------------------------------------------------------
        // Write the "LIKE" portion of the query
        if (count($this->ar_like) > 0) {
            if (count($this->ar_where) > 0) {
                $sql .= "\nAND ";
            }
            $sql .= implode("\n", $this->ar_like);
        }
        // ----------------------------------------------------------------
        // Write the "GROUP BY" portion of the query
        if (count($this->ar_groupby) > 0) {
            $sql .= "\nGROUP BY ";
            $sql .= implode(', ', $this->ar_groupby);
        }
        // ----------------------------------------------------------------
        // Write the "HAVING" portion of the query
        if (count($this->ar_having) > 0) {
            $sql .= "\nHAVING ";
            $sql .= implode("\n", $this->ar_having);
        }
        // ----------------------------------------------------------------
        // Write the "ORDER BY" portion of the query
        if (count($this->ar_orderby) > 0) {
            $sql .= "\nORDER BY ";
            $sql .= implode(', ', $this->ar_orderby);
            if ($this->ar_order !== FALSE) {
                $sql .= ($this->ar_order == 'desc') ? ' DESC' : ' ASC';
            }
        }
        // ----------------------------------------------------------------
        // Write the "LIMIT" portion of the query
        if (is_numeric($this->ar_limit)) {
            $sql .= "\n";
            $sql = $this->_adapter->_limit($sql, $this->ar_limit, 
            $this->ar_offset);
        }
        return $sql;
    }
    // --------------------------------------------------------------------
    /**
     * Chuyển một đối tượng thành mảng
     * 
     * Truyền vào một objecy và chuyển các thuộc tính thành mảng key/vals
     *
     * @access	public
     * @param	object
     * @return	array
     */
    public function _object_to_array ($object)
    {
        if (! is_object($object)) {
            return $object;
        }
        $array = array();
        foreach (get_object_vars($object) as $key => $val) {
            // There are some built in keys we need to ignore for this conversion
            if (! is_object($val) && ! is_array($val)) {
                $array[$key] = $val;
            }
        }
        return $array;
    }
    // --------------------------------------------------------------------
    /**
     * Start Cache
     *
     * Starts AR caching
     *
     * @access	public
     * @return	void
     */
    public function start_cache ()
    {
        $this->ar_caching = TRUE;
    }
    // --------------------------------------------------------------------
    /**
     * Stop Cache
     *
     * Stops AR caching
     *
     * @access	public
     * @return	void
     */
    public function stop_cache ()
    {
        $this->ar_caching = FALSE;
    }
    // --------------------------------------------------------------------
    /**
     * Xóa cache
     *
     * Empties the AR cache
     *
     * @access	public
     * @return	void
     */
    public function flush_cache ()
    {
        $this->_reset_run(
        array('ar_cache_select' => array(), 'ar_cache_from' => array(), 
        'ar_cache_join' => array(), 'ar_cache_where' => array(), 
        'ar_cache_like' => array(), 'ar_cache_groupby' => array(), 
        'ar_cache_having' => array(), 'ar_cache_orderby' => array(), 
        'ar_cache_set' => array(), 'ar_cache_exists' => array()));
    }
    // --------------------------------------------------------------------
    /**
     * Merge Cache
     *
     * When called, this function merges any cached AR arrays with 
     * locally called ones.
     *
     * @access	private
     * @return	void
     */
    public function _merge_cache ()
    {
        if (count($this->ar_cache_exists) == 0) {
            return;
        }
        foreach ($this->ar_cache_exists as $val) {
            $ar_variable = 'ar_' . $val;
            $ar_cache_var = 'ar_cache_' . $val;
            if (count($this->$ar_cache_var) == 0) {
                continue;
            }
            $this->$ar_variable = array_unique(
            array_merge($this->$ar_cache_var, $this->$ar_variable));
        }
        // If we are "protecting identifiers" we need to examine the "from"
        // portion of the query to determine if there are any aliases
        if ($this->_protect_identifiers === TRUE and
         count($this->ar_cache_from) > 0) {
            $this->_track_aliases($this->ar_from);
        }
    }
    // --------------------------------------------------------------------
    /**
     * Xóa trắng các giá trị của active record. Được gọi sau khi gọi phương thức get()
     *
     * @access	private
     * @param	array	An array of fields to reset
     * @return	void
     */
    public function _reset_run ($ar_reset_items)
    {
        foreach ($ar_reset_items as $item => $default_value) {
            if (! in_array($item, $this->ar_store_array)) {
                $this->$item = $default_value;
            }
        }
    }
    // --------------------------------------------------------------------
    /**
     * Xóa trắng các giá trị của active record. Được gọi sau khi gọi phương thức get()
     *
     * @access	private
     * @return	void
     */
    public function _reset_select ()
    {
        $ar_reset_items = array('ar_select' => array(), 'ar_from' => array(), 
        'ar_join' => array(), 'ar_where' => array(), 'ar_like' => array(), 
        'ar_groupby' => array(), 'ar_having' => array(), 'ar_orderby' => array(), 
        'ar_wherein' => array(), 'ar_aliased_tables' => array(), 'ar_distinct' => FALSE, 
        'ar_limit' => FALSE, 'ar_offset' => FALSE, 'ar_order' => FALSE);
        $this->_reset_run($ar_reset_items);
    }
    // --------------------------------------------------------------------
    /**
     * Resets the active record "write" values.
     *
     * Called by the insert() update() and delete() functions
     *
     * @access	private
     * @return	void
     */
    public function _reset_write ()
    {
        $ar_reset_items = array('ar_set' => array(), 'ar_from' => array(), 
        'ar_where' => array(), 'ar_like' => array(), 'ar_orderby' => array(), 
        'ar_limit' => FALSE, 'ar_order' => FALSE);
        $this->_reset_run($ar_reset_items);
    }
    private $_protect_identifiers = TRUE;
    private $_reserved_identifiers = array('*'); // Identifiers that should NOT be escaped
    private $swap_pre = '';
    /**
     * Protect Identifiers
     *
     * This function is used extensively by the Active Record class, and by
     * a couple functions in this class. 
     * It takes a column or table name (optionally with an alias) and inserts
     * the table prefix onto it.  Some logic is necessary in order to deal with
     * column names that include the path.  Consider a query like this:
     *
     * SELECT * FROM hostname.database.table.column AS c FROM hostname.database.table
     *
     * Or a query with aliasing:
     *
     * SELECT m.member_id, m.member_name FROM members AS m
     *
     * Since the column name can include up to four segments (host, DB, table, column)
     * or also have an alias prefix, we need to do a bit of work to figure this out and
     * insert the table prefix (if it exists) in the proper position, and escape only
     * the correct identifiers.
     *
     * @access	private
     * @param	string
     * @param	bool
     * @param	mixed
     * @param	bool
     * @return	string
     */
    public function _protect_identifiers ($item, $prefix_single = FALSE, 
    $protect_identifiers = NULL, $field_exists = TRUE)
    {
        if (! is_bool($protect_identifiers)) {
            $protect_identifiers = $this->_protect_identifiers;
        }
        if (is_array($item)) {
            $escaped_array = array();
            foreach ($item as $k => $v) {
                $escaped_array[$this->_protect_identifiers($k)] = $this->_protect_identifiers(
                $v);
            }
            return $escaped_array;
        }
        // Convert tabs or multiple spaces into single spaces
        $item = preg_replace('/[\t ]+/', ' ', $item);
        // If the item has an alias declaration we remove it and set it aside.
        // Basically we remove everything to the right of the first space
        $alias = '';
        if (strpos($item, ' ') !== FALSE) {
            $alias = strstr($item, " ");
            $item = substr($item, 0, - strlen($alias));
        }
        // This is basically a bug fix for queries that use MAX, MIN, etc.
        // If a parenthesis is found we know that we do not need to 
        // escape the data or add a prefix.  There's probably a more graceful
        // way to deal with this, but I'm not thinking of it -- Rick
        if (strpos($item, '(') !== FALSE) {
            return $item . $alias;
        }
        // Break the string apart if it contains periods, then insert the table prefix
        // in the correct location, assuming the period doesn't indicate that we're dealing
        // with an alias. While we're at it, we will escape the components
        if (strpos($item, '.') !== FALSE) {
            $parts = explode('.', $item);
            // Does the first segment of the exploded item match
            // one of the aliases previously identified?  If so,
            // we have nothing more to do other than escape the item
            if (in_array($parts[0], 
            $this->ar_aliased_tables)) {
                if ($protect_identifiers === TRUE) {
                    foreach ($parts as $key => $val) {
                        if (! in_array($val, $this->_reserved_identifiers)) {
                            $parts[$key] = $this->_escape_identifiers($val);
                        }
                    }
                    $item = implode('.', $parts);
                }
                return $item . $alias;
            }
            // Is there a table prefix defined in the config file?  If not, no need to do anything
            if ($this->_adapter->getConfig('dbprefix') != '') {
                // We now add the table prefix based on some logic.
                // Do we have 4 segments (hostname.database.table.column)?
                // If so, we add the table prefix to the column name in the 3rd segment.
                if (isset(
                $parts[3])) {
                    $i = 2;
                } // Do we have 3 segments (database.table.column)?
// If so, we add the table prefix to the column name in 2nd position
                elseif (isset($parts[2])) {
                    $i = 1;
                } // Do we have 2 segments (table.column)?
// If so, we add the table prefix to the column name in 1st segment
                else {
                    $i = 0;
                }
                // This flag is set when the supplied $item does not contain a field name.
                // This can happen when this function is being called from a JOIN.
                if ($field_exists == FALSE) {
                    $i ++;
                }
                // Verify table prefix and replace if necessary
                if ($this->swap_pre != '' && strncmp($parts[$i], 
                $this->swap_pre, strlen($this->swap_pre)) === 0) {
                    $parts[$i] = preg_replace(
                    "/^" . $this->swap_pre . '(\S+?)/', 
                    $this->_adapter->getConfig('dbprefix') . "\\1", $parts[$i]);
                }
                // We only add the table prefix if it does not already exist
                if (substr($parts[$i], 0, 
                strlen($this->_adapter->getConfig('dbprefix'))) !=
                 $this->_adapter->getConfig('dbprefix')) {
                    $parts[$i] = $this->_adapter->getConfig('dbprefix') .
                     $parts[$i];
                }
                // Put the parts back together
                $item = implode('.', $parts);
            }
            if ($protect_identifiers === TRUE) {
                $item = $this->_escape_identifiers($item);
            }
            return $item . $alias;
        }
        // Is there a table prefix?  If not, no need to insert it
        if ($this->_adapter->getConfig('dbprefix') != '') {
            // Verify table prefix and replace if necessary
            if ($this->swap_pre != '' &&
             strncmp($item, $this->swap_pre, strlen($this->swap_pre)) === 0) {
                $item = preg_replace("/^" . $this->swap_pre . '(\S+?)/', 
                $this->_adapter->getConfig('dbprefix') . "\\1", $item);
            }
            // Do we prefix an item with no segments?
            if ($prefix_single == TRUE and substr($item, 0, 
            strlen($this->_adapter->getConfig('dbprefix'))) !=
             $this->_adapter->getConfig('dbprefix')) {
                $item = $this->_adapter->getConfig('dbprefix') . $item;
            }
        }
        if ($protect_identifiers === TRUE and
         ! in_array($item, $this->_reserved_identifiers)) {
            $item = $this->_escape_identifiers($item);
        }
        return $item . $alias;
    }
    // --------------------------------------------------------------------
    /**
     * Escape the SQL Identifiers
     *
     * This function escapes column and table names
     *
     * @access	private
     * @param	string
     * @return	string
     */
    public function _escape_identifiers ($item)
    {
        if ($this->_adapter->_escape_char == '') {
            return $item;
        }
        foreach ($this->_reserved_identifiers as $id) {
            if (strpos($item, '.' . $id) !== FALSE) {
                $str = $this->_adapter->_escape_char .
                 str_replace('.', $this->_adapter->_escape_char . '.', $item);
                // remove duplicates if the user already included the escape
                return preg_replace(
                '/[' . $this->_adapter->_escape_char . ']+/', 
                $this->_adapter->_escape_char, $str);
            }
        }
        if (strpos($item, '.') !== FALSE) {
            $str = $this->_adapter->_escape_char . str_replace('.', 
            $this->_adapter->_escape_char . '.' . $this->_adapter->_escape_char, 
            $item) . $this->_adapter->_escape_char;
        } else {
            $str = $this->_adapter->_escape_char . $item .
             $this->_adapter->_escape_char;
        }
        // remove duplicates if the user already included the escape
        return preg_replace(
        '/[' . $this->_adapter->_escape_char . ']+/', 
        $this->_adapter->_escape_char, $str);
    }
    // --------------------------------------------------------------------
    public function display_error ($str)
    {
        require_once 'XPHP/Db/Exception.php';
        throw new XPHP_Db_Exception($str);
    }
    // --------------------------------------------------------------------
    /**
     * "Smart" Escape String
     *
     * Escapes data based on type
     * Sets boolean and null types
     *
     * @access	public
     * @param	string
     * @return	mixed		
     */
    public function escape ($str)
    {
        if (is_string($str)) {
            $str = "'" . $this->_adapter->escape_str($str) . "'";
        } elseif (is_bool($str)) {
            $str = ($str === FALSE) ? 0 : 1;
        } elseif (is_null($str)) {
            $str = 'NULL';
        }
        return $str;
    }
    // --------------------------------------------------------------------
    /**
     * Kiểm tra xem chuỗi có một toán tử SQL hay không ?
     *
     * @access	private
     * @param	string
     * @return	bool
     */
    public function _has_operator ($str)
    {
        $str = trim($str);
        if (! preg_match('/(\s|<|>|!|=|is null|is not null)/i', $str)) {
            return FALSE;
        }
        return TRUE;
    }

    // ---------------------------------------------------------------------
    /**
     * Lấy ra id được insert lần cuối cùng
     * 
     * @return int
     */
    public function insert_id()
    {
        return $this->_adapter->insert_id();
    }
    
    //----------------------------------------------------------------------
    /**
     * Lấy ra thông báo lỗi
     * 
     * @return string
     */
    public function getErrorMessage()
    {
    	return $this->_adapter->_error_message();
    }
    
    /**
     * Lấy ra mã lỗi
     * 
     * @return int
     */
    public function getErrorNumber()
    {
    	return $this->_adapter->_error_number();
    }
}