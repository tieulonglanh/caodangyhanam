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
 * @category    XPHP
 * @package        XPHP_Model
 * @author        XWEB Dev Team
 * @copyright    Copyright (c) 2010-2011 XWEB. (http://xweb.vn)
 * @license        http://xphp.xweb.vn/license.html     GNU GPL License
 * @version        $Id: Model.php 20109 2011-22-08 02:05:09 Mr.UBKey $
 */
/**
 * XPHP_DB_Table_Model.
 *
 * @see XPHP_DB_Table_Model
 */
require_once 'XPHP/Db/Table/Model.php';
/**
 * XPHP_Attribute.
 *
 * @see XPHP_Attribute
 */
require_once 'XPHP/Attribute.php';
/**
 * Model
 *
 * @category    XPHP
 * @package        XPHP_Model
 * @author        Mr.UBKey
 * @link        http://xphp.xweb.vn/user_guide/xphp_model.html
 */
class XPHP_Model extends XPHP_DB_Table_Model
{
    /**
     * Thuộc tính cho phép Model tự động gắn kết với CSDL khi khởi tạo
     * @var bool
     */
    public $autoBinding = true;
    /**
     * Mảng lưu trữ các thông báo lỗi
     * @var array
     */
    protected $_errorMessage = array();

    /**
     * Khởi tạo Model
     * @param mixed $id Id của Model Nếu có truyền vào ID lấy thông tin tương ứng với Id từ database
     */
    public function __construct($id = NULL)
    {
        //Load cấu hình trong file config với node model
        require_once 'XPHP/Config.php';
        XPHP_Config::load($this, 'model');
        //Tự động gắn kết model với CSDL
        if ($this->autoBinding) {
            $this->bind($id);
        }
        //Gọi tới function __init nếu có sau khi khởi tạo lớp
        if (method_exists($this, '_init'))
            $this->_init();
    }

    /**
     * Lấy ra các thông báo lỗi từ model
     */
    public function getErrorMessage()
    {
        return $this->_errorMessage;
    }

    /**
     * Gán thông báo lỗi
     * @param string $error Tên lỗi unique
     * @param string $message Thông báo lỗi
     * @param string $field Thuộc tính phát sinh lỗi
     */
    public function setErrorMessage($error, $message, $field = NULL)
    {
        if ($field === NULL)
            $field = "_CUSTOMERROR";
        if (isset($this->_errorMessage[$field]) &&
            is_array($this->_errorMessage[$field])
        ) {
            $this->_errorMessage[$field] = array_merge(
                array($error => $message), $this->_errorMessage[$field]);
        } else {
            $this->_errorMessage[$field] = array($error => $message);
        }
    }

    /**
     * Phương thức kiểm tra tính valid của model
     */
    public function validate()
    {
        require_once 'XPHP/Reflection.php';
        require_once 'XPHP/Model.php';
        require_once 'XPHP/Attribute.php';
        require_once 'XPHP/Model/Attribute/Validation/Abstract.php';
        $validFlag = true;
        //Lấy toàn bộ danh sách các thuộc tính của Model
        $properties = $this->getModelProperties(); 
        foreach ($properties as $p) {
            //Lấy ra toàn bộ các Attribute của thuộc tính
            $attributes = XPHP_Attribute::ofProperty($this, $p, 'XPHP_Model_Attribute_Validation_Abstract');
            foreach ($attributes as $attribute) {
                //Gán Model hiện tại cho Attribute
                $attribute->setModel($this);
                //Thiết lâp tên thuộc tính cho Attribute
                $attribute->setProperty($p);
                //Gọi phương thức thực thi valid
                if (!$attribute->onValidate()) {
                    $validFlag = false;
                    $this->_errorMessage[$p][$attribute->name] = $attribute->message;
                }
                //freeModel
                $attribute->freeModel();
            }
        }
        return $validFlag;
    }

    /**
     * Phương thức gắn kết Model với CSDL
     * @param mixed $id Id của Model Nếu có truyền vào ID lấy thông tin tương ứng với Id từ database
     */
    public function bind($id = NULL)
    {
        //Gán adapter mặc định
        require_once 'XPHP/Registry.php';
        if (XPHP_Registry::isRegistered("Db_default")) {
            $this->_adapter = XPHP_Registry::get("Db_default");
            //Gán lớp hỗ trợ truy vấn csdl
            $this->db = clone $this->_adapter->db;
            //Lớp định nghĩa phương thức xử lý các attribute
            $modelAttribute = XPHP_Attribute::ofClass($this,
                                                      'XPHP_Model_Attribute_Binding_Abstract');
            foreach ($modelAttribute as $attribute) {
                //Gán Model hiện tại cho Attribute
                $attribute->setModel($this);
                //Gọi phương thức thực thi
                $attribute->onBinding();
                //freeModel
                $attribute->freeModel();
            }
            /**
             * Nếu có truyền vào ID lấy thông tin tương ứng với Id từ database
             * lưu vào thuộc tính đối tượng
             */
            if ($id !== NULL) {
                $this->get($id);
            }
        }
    }

    /**
     * (non-PHPdoc)
     * @see XPHP_DB_Table_Model::insert()
     */
    public function insert($arrData = array())
    {
        if(empty($arrData)) {
            //Kiểm tra read_only của model
            if ($this->_readOnly) {
                return false;
                throw new XPHP_Exception(get_class($this) . " chỉ được phép đọc .");
            }
            $arrData = array(); 
            //Lấy toàn bộ danh sách các thuộc tính của Model
            $properties = $this->getModelProperties(); 
            foreach ($properties as $p)
            {
                //Đánh dấu thuộc tính được insert hay không ? Mặc định là có
                $insert = true;
                $attributes = XPHP_Attribute::ofProperty($this, $p, 'XPHP_Model_Attribute_Binding_Command');
                //Nếu có attribute command
                foreach ($attributes as $att)
                {
                    if (($att->command !== null && $att->command === false) || ($att->insert !== null && $att->insert === false))
                    $insert = false;
                }
                if ($insert)
                    //Lấy tên thuộc tính trong lớp và giá trị của nó đưa vào mảng
                    $arrData[$p] = $this->$p;
            }
        }
        
        return parent::insert($arrData);
    }

    /**
     * (non-PHPdoc)
     * @see XPHP_DB_Table_Model::update()
     */
    public function update()
    {
        //Kiểm tra read_only của model
        if ($this->_readOnly) {
            return false;
            throw new XPHP_Exception(get_class($this) . " chỉ được phép đọc .");
        }
        $arrData = array(); //Mảng chứa dữ liệu đưa vào update
        //Lấy toàn bộ danh sách các thuộc tính của Model
        $properties = $this->getModelProperties();
        foreach ($properties as $p)
        {
            //Đánh dấu thuộc tính được update hay không ? Mặc định là có
            $update = true;
            $attributes = XPHP_Attribute::ofProperty($this, $p, 'XPHP_Model_Attribute_Binding_Command');
            //Nếu có attribute command
            foreach ($attributes as $att)
            {
                if (($att->command !== null && $att->command === false) || ($att->update !== null && $att->update === false))
                $update = false;
            }
            if ($update)
                //Lấy tên thuộc tính trong lớp và giá trị của nó đưa vào mảng
                $arrData[$p] = $this->$p;
        }
        return parent::update($arrData, $this->{$this->_primaryKey});
    }

    /**
     * (non-PHPdoc)
     * @see XPHP_DB_Table_Model::delete()
     */
    public function delete()
    {
        //Kiểm tra read_only của model
        if ($this->_readOnly) {
            return false;
            throw new XPHP_Exception(get_class($this) . " chỉ được phép đọc .");
        }
        return parent::delete($this->{$this->_primaryKey});
    }

    /**
     * Phương thức lấy giá trị từ database gán vào model hiện tại
     * @param int $id
     */
    public function get($id)
    {
        //Lấy dữ liệu từ database
        $row = $this->db->where($this->_primaryKey, $id)
                ->get()
                ->result();
        if (!isset($row[0]))
            return false;
        else
            $row = $row[0];
        //Gán giá trị vào các thuộc tính
        $properties = $this->getModelProperties();
        foreach ($properties as $p)
        {
            if(isset($row->$p))
                $this->$p = $row->$p;
        }
        //Trả về kết quả select được
        return $row;
    }
    
    /**
     * Gán một thể hiện của Model
     * @param object|array $obj
     */
    public function setInstance($obj)
    {
    	if(is_object($obj))
    		$obj = get_object_vars($obj);
   		//Each cac thuoc tinh
    	foreach($obj as $p => $v)
    	{
    		$this->$p = $v;
    	}
    }
    
    /**
     * Tham số truyền vào là một object hoặc mảng và trả về thể hiện của Model
     * @param object|array $obj
     * @return XPHP_Model
     */
    public function getInstanceFrom($obj)
    {
    	$class = get_class($this);
    	$model = new $class();
    	if(is_object($obj))
    		$obj = get_object_vars($obj);
    	//Each cac thuoc tinh
    	foreach($obj as $p => $v)
    	{
    		$model->$p = $v;
    	}
    	return $model;
    }
    
    /**
     * Lấy ra danh sách các thuộc tính của model
     * @return array
     */
    public function getModelProperties()
    {
        //Lấy ra đường dẫn tới tệp tin chứa class
        $reflection = new ReflectionClass($this);
        $path = $reflection->getFileName(); 
        //Runtime
        $runtime = XPHP_Runtime::getInstance();
        $runtimeKey = "Model_Properties_" . md5($path);
        if(!isset($runtime->$runtimeKey))
        {
            $properties = array();
            //Lấy ra các thuộc tính của XPHP_Model
            require_once 'XPHP/Reflection.php';
            $ref_model = new XPHP_Reflection("XPHP_Model");
            $properties_model = array();
            foreach ($ref_model->getProperties() as $property) {
                $properties_model[] = $property->getName();
            }
            //Lấy danh sách các property của lớp thừa kế XPHP_Model
            $ref = new XPHP_Reflection($this);
            foreach ($ref->getProperties() as $property) {
                $property_name = $property->getName();
                if (!in_array($property_name, $properties_model)) {
                    $properties[] = $property_name;
                }
            }
            $runtime->$runtimeKey = $properties;
            return $properties;
        }
        else 
            return $runtime->$runtimeKey;
    }
}