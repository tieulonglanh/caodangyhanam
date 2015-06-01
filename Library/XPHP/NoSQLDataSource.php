<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of NoSQLDataSource
 *
 * @author tieulonglanh
 */
class XPHP_NoSQLDataSource {
    //put your code here
    
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
     * Hướng sắp xếp mặc định ASC
     * @var string
     */
    public $sortDirect = 'asc';
    
    /**
     * Dữ liệu sau khi thông qua datasource
     */
    private $_data;
    
    /**
     * Model schema
     */
    public $schema;
    /**
     * Danh sách các cột
     * @var array
     */
    public $columns = array();
    
    public $condition = array();
   
    public $pageable = true;
    
    public $sortable = true;
    
    public $filterable = true;
    
    public $groupable = true;
    
    /**
     * Khởi tạo với tham số truyền vào là tên model hoặc object model
     * @param mixed $model
     */
    public function __construct($model, $options = array())
    {
        if(is_string($model))
        {
            $this->model = new $model();
        }
        else if($model instanceof XPHP_Model)
        {
            $this->model = $model;
        }
        //Gán các thuộc tính
        foreach ($options as $k => $v)
        {
            $this->$k = $v;
        }
        //Phân trang nếu có biến $_REQUEST['page']
        if(isset($_REQUEST['page']))
        	$this->page = (int)$_REQUEST['page'];
    }
    
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
            //Lấy ra danh sách các thuộc tính của lớp
            $colums = $modelProperties;
        //Danh sách các cột đưa vào select
        $selectCol = array();
        //Lấy ra tile của columns
        foreach ($colums as $k => $c)
        {
            if(is_array($c))
            {
                $selectCol[] = $k;
                //Các op cũ
                $this->columns[$k] = $c;
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
                $selectCol[] = $c;
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
        //Lấy ra dữ liệu
        if($this->_data === null)
        {
        	//Lấy ra tổng số các record
        	$db = clone $this->model->db;        	
        	$this->count = $db->count();
	        $this->model->db->select(implode(', ', $selectCol));
	        foreach ($this->condition as $con){
                    if($con[2]=='!=')
                        $this->model->db->where_ne($con[0], $con[1]);
                    if($con[2]=='>')
                        $this->model->db->where_gt($con[0],$con[1]);
                    if($con[2]=='<')
                        $this->model->db->where_lt($con[0],$con[1]);
                    if($con[2]=='>=')
                        $this->model->db->where_gte($con[0],$con[1]);
                    if($con[2]=='<=')
                        $this->model->db->where_lte($con[0],$con[1]);
                    if($con[2]===NULL)
                        $this->model->db->where($con[0],$con[1]);
                }                
	        if($this->sort)
	            $this->model->db->order_by($this->sort, $this->sortDirect);
	        if($this->pageSize)
	        {
	            if(! $this->page)
	                $this->page = 1;
	            $this->model->db->limit($this->pageSize, 
	                                    ((int)$this->page - 1) * $this->pageSize);
	        }
	        //Lấy ra data sau khi phân trang(nếu có)
	        $this->_data = $this->model->db->get();
        }
        if($this->_data)
        {
            $this->_data = (array)$this->_data;
	        for($i=0; $i<count($this->_data); $i++)
	        {                    
	            foreach ($this->_data[$i] as $k => $v)
	            {
                        $this->_data[$i]->_id = $i+1;
	                //Xử lý template                        
	                if(isset($this->columns[$k]['template']))
	                    $this->_data[$i]->$k = $this->_template($this->_data[$i], $this->columns[$k]['template']);
	            }
	        }
        }
    }
    
    /**
     * Lấy dữ liệu ra từ DataSource
     * @return array
     */
    public function getData()
    {
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
     * @param unknown_type $rowIndex
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
}

?>
