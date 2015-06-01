<?php
class XPHP_DataSource
{
    /**
     * Tên bảng dữ liệu
     * @var string
     */
    public $table;

    /**
     * Model được sử dụng để truy xuất
     * @var XPHP_Model
     */
    public $model;
    
    /**
     * Trang hiện tại
     * @var int
     */
    public $page;
    
    /**
     * Số dòng dữ liệu hiển thị trên một trang
     * @var int
     */
    public $pageSize;
    
    /**
     * Số dòng dữ liệu
     * @var int
     */
    public $count;
    
    /**
     * Sắp xếp theo
     * @var string
     */
    public $sort;
    
    /**
     * Hướng sắp xếp mặc định SORT_ASC
     * @var int
     */
    public $sortDirect = SORT_ASC;
    
    /**
     * Dữ liệu sau khi thông qua datasource
     */
    private $_data;
    
    /**
     * Model schema
     */
    public $schema;
    /**
     * Danh sách các cột và thuộc tính của nó
     * @var array
     */
    public $columns = array();
    /**
     * Tên các cột được lựa chọn sau khi xử lý
     * @var array
     */
    public $columnNames = array();
    /**
     * Cột đưọc thêm vào
     * @var array
     */
    public $addColumns = array();
    /**
     * Cột đưọc thêm vào với php template
     * @var array
     */
    public $addTemplateColumns = array();
    /**
     * Các cột được sửa lại giá trị
     * @var array
     */
    public $editColumns = array();
    /**
     * Các cột được sửa lại giá trị với php template
     * @var array
     */
    public $editTemplateColumns = array();
    /**
     * Các cột bị loại bỏ
     * @var array
     */
    public $unsetColumns = array();
    
    public $pageable     = true;
    
    public $sortable     = true;
    
    public $filterable   = true;
    
    public $groupable    = true;

    protected $distinct;

    protected $group_by;

    protected $order_by       = array();

    protected $select         = array();

    protected $joins          = array();

    protected $where          = array();

    /**
     * Quyết định dữ liệu trả về là dạng key => value
     * @var boolean
     */
    public $isAssociative = true;
    
    /**
     * Khởi tạo với tham số truyền vào là tên model hoặc object model
     * @param XPHP_Model | String | NULL $mixed
     * @param array $columns
     */
    public function __construct($mixed, $columns = array())
    {
        if($mixed===NULL)
            $model = new XPHP_Model(); //Khởi tạo model mới
        else if(is_string($mixed))
        {
            $model = new XPHP_Model(); //Khởi tạo model mới và set tên bảng
            $model->_name = $mixed;
            $this->table  = $mixed;
        }
        else if($mixed instanceof XPHP_Model)
            $model = $mixed; //Set vào model cung cấp sẵn

        $this->model = $model; //Set model

        //Gán các thuộc tính
        $this->columns = $columns;
    }

    /**
     * Thêm một cột
     * Nếu $replacement = false sẽ sử dụng php template
     * @param $column
     * @param $content
     * @param $replacement
     * @return XPHP_DataSource
     */
    public function addColumn($column, $content, $replacement=array())
    {
        if(is_array($replacement))
            $this->addColumns[$column] = array('content' => $content, 'replacement' => $replacement);
        else
            $this->addTemplateColumns[$column] = $content;
        return $this;
    }

    /**
     * Sửa giá trị trả về của cột
     * @param $column
     * @param $content
     * @param string $replacement
     * @return XPHP_DataSource
     */
    public function editColumn($column, $content, $replacement=array())
    {
        if(is_array($replacement))
            $this->editColumns[$column] = array('content' => $content, 'replacement' => $replacement);
        else
            $this->editTemplateColumns[$column] = $content;
        return $this;
    }

    /**
     * Xoá một cột
     *
     * @param string $column
     * @return XPHP_DataSource
     */
    public function unsetColumn($column)
    {
        $this->unsetColumns[] = $column;
        return $this;
    }

    /**
     * Thêm SELECT vào câu truy vấn
     *
     * @param string $columns
     * @param bool $backtick_protect
     * @return XPHP_DataSource
     */
    public function select($columns, $backtick_protect = TRUE)
    {
        foreach(explode(',', $columns) as $val)
        {
            $column = trim(preg_replace('/(.*)\s+as\s+(\w*)/i', '$2', $val));
            $this->columns[] =  $column;
            $this->select[$column] =  trim(preg_replace('/(.*)\s+as\s+(\w*)/i', '$1', $val));
        }

        $this->model->db->select($columns, $backtick_protect);
        return $this;
    }

    /**
     * Thêm DISTINCT vào câu truy vấn dữ liệu
     *
     * @param string $column
     * @return XPHP_DataSource
     */
    public function distinct($column)
    {
        $this->distinct = $column;
        $this->model->db->distinct($column);
        return $this;
    }

    /**
     * Thêm GROUP BY vào câu truy vấn dữ liệu
     *
     * @param string $column
     * @return XPHP_DataSource
     */
    public function group_by($column)
    {
        $this->group_by = $column;
        $this->model->db->group_by($column);
        return $this;
    }

    /**
     * Thêm FROM vào câu truy vấn dữ liệu
     *
     * @param string $table
     * @return XPHP_DataSource
     */
    public function from($table)
    {
        $this->table = $table;
        $this->model->db->from($table);
        return $this;
    }

    /**
     * Thêm the JOIN vào câu truy vấn dữ liệu
     *
     * @param string $table
     * @param string $fk
     * @param string $type
     * @return XPHP_DataSource
     */
    public function join($table, $fk, $type = NULL)
    {
        $this->joins[] = array($table, $fk, $type);
        $this->model->db->join($table, $fk, $type);
        return $this;
    }

    /**
     * Thêm WHERE vào câu truy vấn dữ liệu
     *
     * @param mixed $key_condition
     * @param string $val
     * @param bool $backtick_protect
     * @return XPHP_DataSource
     */
    public function where($key_condition, $val = NULL, $backtick_protect = TRUE)
    {
        $this->where[] = array($key_condition, $val, $backtick_protect);
        $this->model->db->where($key_condition, $val, $backtick_protect);
        return $this;
    }

    /**
     * Thêm OR WHERE vào câu truy vấn
     *
     * @param mixed $key_condition
     * @param string $val
     * @param bool $backtick_protect
     * @return XPHP_DataSource
     */
    public function or_where($key_condition, $val = NULL, $backtick_protect = TRUE)
    {
        $this->where[] = array($key_condition, $val, $backtick_protect);
        $this->model->db->or_where($key_condition, $val, $backtick_protect);
        return $this;
    }

    /**
     * Thêm WHERE LIKE vào câu truy vấn
     *
     * @param mixed $key_condition
     * @param string $val
     * @param bool $backtick_protect
     * @return XPHP_DataSource
     */
    public function like($key_condition, $val = NULL, $backtick_protect = TRUE)
    {
        $this->where[] = array($key_condition, $val, $backtick_protect);
        $this->model->db->like($key_condition, $val, $backtick_protect);
        return $this;
    }

    /**
     * Thêm ORDER BY vào câu truy vấn
     *
     * @param string $col
     * @param mixed $direct
     * @return XPHP_DataSource
     */
    public function order_by($col, $direct=false)
    {
        $this->order_by[] = array($col, $direct);
        return $this;
    }

    public function resetOrderBy()
    {
        $this->order_by = array();
    }

    private function _compileOrderBy()
    {
        foreach($this->order_by as $v)
            $this->model->db->order_by($v[0], $v[1]);
    }

    /**
     * Thêm LIMIT vào câu truy vấn
     *
     * @param int $limit
     * @param mixed $offset
     * @return XPHP_DataSource
     */
    public function limit($limit, $offset=NULL)
    {
        $this->model->db->limit($limit, $offset);
    }

    /**
     * Bind các cột với kiểu dữ liệu trong model tạo ra schema
     * nếu không định nghĩa các cột thì sẽ tự nhận các thuộc tính của model làm tên cột
     * @return XPHP_DataSource
     */
    public function bind()
    {
        $modelProperties = $this->model->getModelProperties();
        //Lấy ra model schema
        $fields = new stdClass();
        foreach ($modelProperties as $p)
        {
            //Lấy ra kiểu dữ liệu
            $attributes = XPHP_Attribute::ofProperty($this->model, $p, 'XPHP_Model_Attribute_Type');
            $type = 'string';
            //Nếu có attribute type
            foreach ($attributes as $att)
            {
                $type = $att->type;
            }
            //Gán kiểu dữ liệu vào lược đồ
            $fields->$p = new stdClass();
            $fields->$p->type = $type;
        }
        $this->schema->model->fields = $fields;

        //Lấy ra thông tin cột(nếu truyền vào danh sách cột thì chỉ lấy các cột lựa chọn)
        $colums = array();
        if(count($this->columns) > 0)
        {
            $colums = $this->columns;
            $this->columns = array();
        }
        else
            $colums = $modelProperties; //Lấy ra danh sách các thuộc tính của lớp
        //Lấy ra tile của columns
        foreach ($colums as $k => $c)
        {
            if(!is_numeric($k))
            {
                if(!is_array($c))
                {
                    $this->columnNames[] = $k;
                    $this->columns[$k]['template'] = $c;
                }
                else
                {
                    $this->columnNames[] = $k;
                    //Các op cũ
                    $this->columns[$k] = $c;
                }
                //Lấy ra label của
                $attributes = XPHP_Attribute::ofProperty($this->model, $k, 'XPHP_Model_Attribute_Html_Label');
                if(is_array($attributes) && count($attributes) > 0)
                {
                    //Nếu có attribute label
                    foreach ($attributes as $att)
                    {
                        $this->columns[$k]['title'] = $att->text;
                    }
                }
                else
                    $this->columns[$k]['title'] = ucwords(str_replace("_", " ", $k));
            }
            else
            {
                $this->columnNames[] = $c;
                //Lấy ra label của
                $attributes = XPHP_Attribute::ofProperty($this->model, $c, 'XPHP_Model_Attribute_Html_Label');
                if(is_array($attributes) && count($attributes) > 0)
                {
                    //Nếu có attribute label
                    foreach ($attributes as $att)
                    {
                        $this->columns[$c]['title'] = $att->text;
                    }
                }
                else
                    $this->columns[$c]['title'] = ucwords(str_replace("_", " ", $c));
            }
        }
        return $this;
    }

    /**
     * Đếm tổng số bản ghi sau khi đã JOIN, WHERE hoặc có FILTER
     * @return int
     */
    public function countResults($filtering = false)
    {
        foreach($this->joins as $val)
            $this->model->db->join($val[0], $val[1], $val[2]);

        foreach($this->where as $val)
            $this->model->db->where($val[0], $val[1], $val[2]);

        if(! $filtering)
        {
            $this->count = $this->model->db->count_all_results();
            return $this->count;
        }
        else
            return $this->model->db->count_all_results(); //Lấy ra tổng số các record sau khi filter
    }
    
    /**
     * Lấy dữ liệu ra từ DataSource
     * @return array
     */
    public function getDataTable()
    {
        //Lấy ra dữ liệu
        if($this->_data === null)
        {
            //Set các cột cần select dữ liệu
            $this->model->db->select(implode(', ', $this->columnNames));
            //Order sắp xếp
            $this->_compileOrderBy();
            //Lấy ra mảng data sau khi phân trang(nếu có)
            $this->_data = $this->model->db->get()
                                           ->result_array();
        }
        if($this->_data)
        {
            $data = array(); #Dữ liệu trả về
            for($i=0; $i<count($this->_data); $i++)
            {
                $data[$i] = $this->isAssociative ? $this->_data[$i] : array_values($this->_data[$i]);

                foreach ($data[$i] as $k => $v)
                {
                    //Xử lý kiểu dữ liệu type
                    $this->_data[$i][$k] = $this->_type($this->schema->model->fields->$k->type, $v);
                    //Xử lý join
                    //Lấy ra join của
                    $attributes = XPHP_Attribute::ofProperty($this->model, $k, 'XPHP_Model_Attribute_Join');
                    if(is_array($attributes) && count($attributes) > 0)
                    {
                        //Nếu có attribute label
                        foreach ($attributes as $att)
                        {
                            if(strtolower($att->relation) == 'one-one')
                            {
                                $result = $this->model->db->where($att->field, $this->_data[$i][$k])
                                                          ->get($att->table)
                                                          ->result_array();
                                if(isset($result[0]))
                                    $data[$i][$k] = $result[0];
                            }
                            else if(strtolower($att->relation) == 'one-many')
                            {
                                $result = $this->model->db->where($att->field, $this->_data[$i][$k])
                                                          ->get($att->table)
                                                          ->result_array();
                                if(isset($result[0]))
                                    $data[$i][$k] = $result;
                            }
                        }
                    }
                    //Xử lý template
                    if(isset($this->columns[$k]['template']))
                    {
                        if(! isset($this->columns[$k]['replacement']))
                            $data[$i][$k] = $this->_template((object)$this->_data[$i], $this->columns[$k]['template']);
                        else
                            $data[$i][$k] = $this->_replace($data[$i],
                                                                   array('content'     => $this->columns[$k]['template'],
                                                                         'replacement' => $this->columns[$k]['replacement']));
                    }
                }

                //Thêm cột có template php
                foreach($this->addTemplateColumns as $c => $t)
                    if($this->isAssociative)
                        $data[$i][$c] = $this->_template((object)$this->_data[$i], $t);
                    else
                        $data[$i][] = $this->_template((object)$this->_data[$i], $t);

                //Thêm cột có template
                foreach($this->addColumns as $c => $tc)
                    if($this->isAssociative)
                        $data[$i][$c] = $this->_replace($data[$i], $tc);
                    else
                        $data[$i][] = $this->_replace($data[$i], $tc);

                //Sửa các cột với template php
                foreach($this->editTemplateColumns as $c => $t)
                    $data[$i][$this->isAssociative ? $c : array_search($c, $this->columnNames)] = $this->_template((object)$this->_data[$i], $t);

                //Sửa lại các cột với template
                foreach($this->editColumns as $c => $tc)
                    $data[$i][$this->isAssociative ? $c : array_search($c, $this->columnNames)] = $this->_replace($data[$i], $t);

                //Xoá các cột không cần thiết
                $data[$i] = array_diff_key($data[$i], $this->isAssociative ? $this->unsetColumns : array_intersect($this->columnNames, $this->unsetColumns));

                if(!$this->isAssociative)
                    $data[$i] = array_values($data[$i]);
            }
            $this->_data = $data;
        }
        return $this->_data;
    }
    
    /**
     * Gán dữ liệu vào DataSource
     * @param array $data
     */
    public function setData($data)
    {
    	$this->_data = $data;
    }
    
    /**
     * Lấy ra dữ liệu theo dòng
     * @param int $rowIndex
     */
    public function get($rowIndex)
    {
    
    }
    
    public function set()
    {
    
    }
    
    public function update()
    {
    
    }
    
    /**
     * Chuyển đổi kiểu dữ liệu
     * @param string $type Kiểu dữ liệu
     * @param string $value
     */
    private function _type($type, $value)
    {
        switch ($type)
        {
            case 'number':
            case 'int':
                return (int)$value;
            case 'date':
                return $value;
            default:
                return $value;
        }
    }
    
    /**
     * Xử lý template
     * @param array $row Dòng dữ liệu
     * @param array $template Template
     */
    private function _template($row, $template)
    {
        extract(get_object_vars($row));
        return eval($template);
    }

    /**
     * Xử lý replace dữ liệu với template
     * @param mixed $row
     * @param mixed $template
     * @return string
     */
    private function _replace($row, $template)
    {
        $replace_string = '';

        if(isset($template['replacement']) && is_array($template['replacement']))
        {
            foreach($template['replacement'] as $key => $val)
            {
                $sval = preg_replace("/(?<!\w)([\'\"])(.*)\\1(?!\w)/i", '$2', trim($val));

                if(preg_match('/(\w+)\((.*)\)/i', $val, $matches) && function_exists($matches[1]))
                {
                    $func = $matches[1];
                    $args = preg_split("/[\s,]*\\\"([^\\\"]+)\\\"[\s,]*|" . "[\s,]*'([^']+)'[\s,]*|" . "[,]+/", $matches[2], 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

                    foreach($args as $args_key => $args_val)
                    {
                        $args_val = preg_replace("/(?<!\w)([\'\"])(.*)\\1(?!\w)/i", '$2', trim($args_val));
                        $args[$args_key] = (in_array($args_val, $this->columnNames))? ($row[($this->isAssociative)? $args_val : array_search($args_val, $this->columnNames)]) : $args_val;
                    }

                    $replace_string = call_user_func_array($func, $args);
                }
                else if(in_array($sval, $this->columnNames))
                    $replace_string = $row[($this->isAssociative)? $sval : array_search($sval, $this->columnNames)];
                else
                    $replace_string = $sval;

                $template['content'] = str_ireplace('$' . ($key + 1), $replace_string, $template['content']);
            }
        }

        return $template['content'];
    }
}