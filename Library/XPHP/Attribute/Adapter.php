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
 * @package		XPHP_Attribute
 * @author		XWEB Dev Team
 * @copyright	Copyright (c) 2010-2011 XWEB. (http://xweb.vn)
 * @license		http://xphp.xweb.vn/license.html     GNU GPL License
 * @version		$Id: Adapter.php 20109 2011-22-08 02:05:09 Mr.UBKey $
 */
/**
 * XPHP_Attribute_Usage
 * @see XPHP_Attribute_Usage
 */
require_once 'XPHP/Attribute/Usage.php';
/**
 * XPHP_Attribute_Parser
 * @see XPHP_Attribute_Parser
 */
require_once 'XPHP/Attribute/Parser.php';
/**
 * XPHP_Attribute_Exception
 * @see XPHP_Attribute_Exception
 */
require_once 'XPHP/Attribute/Exception.php';
/**
 * XPHP_Config
 * @see XPHP_Config
 */
require_once 'XPHP/Config.php';
/**
 * XPHP_Registry
 * @see XPHP_Registry
 */
require_once 'XPHP/Registry.php';
/**
 * Lớp điều khiển Attribute
 * @package		XPHP_Attribute
 * @author		XWEB Dev Team
 */
class XPHP_Attribute_Adapter
{
    /**
     * @var array Danh sách các Attribute của hệ thống gồm array(ten_attribute => ten_lop)
     */
    public $registry = array(
    #Attribute định nghĩa các khả năng sử dụng của lớp Attribute
	'Usage' => 'XPHP_Attribute_Usage', 
    #Action Attribute
    'Compresses' => 'XPHP_Action_Attribute_Compresses', 
    'OutputCache' => 'XPHP_Action_Attribute_OutputCache', 
    #Model Attribute
    'Type' => 'XPHP_Model_Attribute_Type',
    'Join' => 'XPHP_Model_Attribute_Join',
    #Model Html Attribute
    'Label' => 'XPHP_Model_Attribute_Html_Label', 
    'Tooltip' => 'XPHP_Model_Attribute_Html_Tooltip', 
    #Model Binding Attribute
    'Table' => 'XPHP_Model_Attribute_Binding_Table', 
    'PrimaryKey' => 'XPHP_Model_Attribute_Binding_PrimaryKey', 
    'Adapter' => 'XPHP_Model_Attribute_Binding_Adapter', 
    'ReadOnly' => 'XPHP_Model_Attribute_Binding_ReadOnly',
    'Command'   => 'XPHP_Model_Attribute_Binding_Command',
    #Model Validation Attribute
    'Required' => 'XPHP_Model_Attribute_Validation_Required', 
    'MinLength' => 'XPHP_Model_Attribute_Validation_Minlength', 
    'MaxLength' => 'XPHP_Model_Attribute_Validation_Maxlength', 
    'RangeLength' => 'XPHP_Model_Attribute_Validation_RangeLength', 
    'Min' => 'XPHP_Model_Attribute_Validation_Min', 
    'Max' => 'XPHP_Model_Attribute_Validation_Max', 
    'Range' => 'XPHP_Model_Attribute_Validation_Range', 
    'Email' => 'XPHP_Model_Attribute_Validation_Email', 
    'Url' => 'XPHP_Model_Attribute_Validation_Url', 
    'Number' => 'XPHP_Model_Attribute_Validation_Number', 
    #Widget DataTable Attribute
    'DataTable' => 'XPHP_Widget_Attribute_DataTable');
    /**
     * @var XPHP_Attribute_Parser
     */
    protected $parser;
    /**
     * @var array Các Attributes được lấy ra được từ lớp
     */
    protected $attributes = array();
    /**
     * @var array Mảng lưu các thể hiện của XPHP_Attribute_Usage
     */
    protected $usage = array();
    /**
     * Runtime để lưu trữ Attribute
     * @var XPHP_Runtime
     */
    protected $runtime;
    /**
     * Mảng lưu trữ các lớp đã được lấy Attribute.
     * Tránh trường hợp lặp vô tận
     * @var array
     */
    protected $initialized;
    /**
     * Khởi tạo XPHP_Attribute_Adapter
     */
    public function __construct ()
    {
        //Lấy ra toàn bộ danh sách các attribute định nghĩa trong file config
        $attributes = XPHP_Config::get("attributes > attribute");
        //Nếu có định nghĩa attribute trong file config
        if ($attributes) {
            //Nếu chỉ có một attribute thì gán attribute này vào mảng registry
            if (isset($attributes->class))
                $this->registry[$attributes->name] = $attributes->class;
            else //Nếu có một mảng attribute
                //Đưa vào mảng $registry
                foreach ($attributes as $attr)
                    $this->registry[$attr->name] = $attr->class;
        }
        //Khởi tạo lớp Runtime để lưu trữ các Attribute
        $this->runtime = XPHP_Runtime::getInstance();
    }
    /**
     * Kiểm tra xem trong hệ thống có lớp Attribute này không ?
     * @param string $class Tên lớp cần kiểm tra
     */
    public function hasAttributeClassName ($class)
    {
        if (class_exists($class))
            return true;
        return false;
    }
    /**
     * Kiểm tra xem trong hệ thống có tên Attribute này không ?
     * @param string $name
     */
    public function hasAttribute ($name)
    {
        return isset($this->registry[$name]);
    }
    /**
     * Tạo và trả về thể hiện của lớp XPHP_Attribute_Parser
     * @return XPHP_Attribute_Parser
     */
    public function getParser ()
    {
        if (! isset($this->parser)) {
            $this->parser = new XPHP_Attribute_Parser($this);
        }
        return $this->parser;
    }
    /**
     * Lấy ra tên lớp Attribute
     * 
     * @return string|bool Trả về tên lớp, hoặc false nếu Attribute bị disabled(gán false) hoặc không tìm thấy trong registry .
     */
    public function getAttributeClassName ($name)
    {
        $type = $name;
        if (@$this->registry[$type] === false) {
            return false; // Attribute bị disabled
        }
        if (isset($this->registry[$type]))
            return $this->registry[$type]; // Lớp Attribute đã đăng ký
        else if(preg_match('/^[a-zA-Z_]+/i', $name) && class_exists($name))
            return $name; //Lấy tên class là tên Attribute
        else
            return false; //Không tìm thấy
        return $type;
    }
    /**
     * Lấy ra các Attribute
     * @param string $class Tên của lớp cần lấy Attributes
     * @param string $member Kiểu của member (VD: "class", "property" hoặc "method")
     * @param string $name Tên của member
     */
    protected function getAttributes ($class, $member = 'class', $name = null)
    {
        //Lấy ra đường dẫn tới tệp tin chứa class
        $reflection = new ReflectionClass($class);
        $path = $reflection->getFileName();
        //Nếu không tìm thấy path tới class
        if($path === false)
            return array();
        //Tên Cache
        $runtimeKey = "Attribute_" . md5($path);
        //Lấy ra tên của key trong mảng Attribute
        if ($member == "class")
            $key = $class;
        else 
            if ($member == "property")
                $key = $class . "::" . $name;
            else 
                if ($member == "method")
                    $key = $class . "::" . $name . '()';
        if (! isset($this->attributes[$key]))
            $this->attributes[$key] = array();
             //Nếu lớp này chưa được lấy Attribute thì phân tích lấy ra attribute
        if (! isset($this->initialized[$key])) {
            if ($member !== 'class') {
                $this->getAttributes($class, 'class');
            }
            //Lấy ra Attribute của lớp cha lớp cần lấy Attribute
            $parent = get_parent_class($class);
            if ($parent)
                if ($parent !== 'XPHP_Attribute_Abstract')
                    foreach ($this->getAttributes($parent, $member, $name) as $attribute)
                        if ($this->getUsageAttribute(get_class($attribute))->inherited)
                            $this->attributes[$key][] = $attribute;
                 //Đánh dấu là lớp này đã được xử lý lấy Attribute tránh lặp vô hạn
            $this->initialized[$key] = true;
            /*
			 * Nếu có trong Runtime lấy Attribute từ Runtime ra
			 * Nếu không thì phân tích file lấy ra mảng định nghĩa Attribute
			 */
            if (! isset($this->runtime->$runtimeKey)) {
                $specs = $this->getParser()->parse($path);
                $this->runtime->$runtimeKey = $specs;
            } else
                $specs = $this->runtime->$runtimeKey;
            if (isset($specs[$key])) {
                $this->attributes[$key] = array_merge($this->attributes[$key], 
                $specs[$key]);
            }
            $this->applyUsage($this->attributes[$key], $member);
        }
        return $this->attributes[$key];
    }
    /**
     * Kiểm tra tính hợp lệ các ràng buộc (được định nghĩa XPHP_Attribute_Usage của mỗi Attribute)
     *
     * @param array Một mảng của các đối tượng XPHP_Attribute_Interface để kiểm tra.
     * @param string Loại member để kiểm tra (VD: "class", "property" hoặc "method")
     */
    protected function applyUsage (&$attributes, $member)
    {
        foreach ($attributes as $outer => $attribute) {
            $type = get_class($attribute);
            $usage = $this->getUsageAttribute($type);
            if (! $usage->$member)
                throw new XPHP_Attribute_Exception(
                "{$type} không thể áp dụng cho {$member}");
            if (! $usage->multiple) {
                foreach ($attributes as $inner => $other) {
                    if ($inner >= $outer)
                        break;
                    if ($other instanceof $type) {
                        if ($usage->inherited)
                            unset($attributes[$inner]);
                        else
                            throw new XPHP_Attribute_Exception(
                            "Chỉ một {$type} có thể áp dụng giống với {$member}");
                    }
                }
            }
        }
    }
    /**
     * Lọc Attribute
     *
     * @param array $Attributes Mảng đối tượng Attribute
     * @param string $type Tên lớp sử dụng để lọc Attribute
     * @return array Mảng lọc đối tượng Attribute - Trả về rỗng nếu không có Attribute phù hợp
     */
    protected function filterAttributes ($attributes, $type)
    {
        //Kiểm tra xem type là tên lớp hay tên Attribute. Nếu là tên Attribute thì chuyển thành tên lớp
        if ($this->hasAttribute($type))
            $type = $this->getAttributeClassName(substr($type, 1));
        $result = array();
        foreach ($attributes as $attribute)
            if ($attribute instanceof $type)
                $result[] = $attribute;
        return $result;
    }
    /**
     * Lấy XPHP_Attributes_Usage để kiểm tra khả năng sử dụng của loại Attribute này
     * @param string $class Tên lớp Attribute
     */
    public function getUsageAttribute ($class)
    {
        if (! isset($this->usage[$class])) {
            if (! class_exists($class))
                throw new XPHP_Attribute_Exception(
                get_class($this) .
                 "::getUsageAttribute() : chưa định nghĩa Attribute lớp '{$class}'");
                 //Lấy usage của Attribute
            $usage = $this->getAttributes($class);
            //Nếu không có lấy từ lớp cha
            if (count($usage) == 0) {
                //Lấy ra lớp cha của lớp cần lấy usage
                $parent = get_parent_class($class);
                if ($parent) {
                    $usage = $this->getUsageAttribute($parent);
                } else {
                    throw new XPHP_Attribute_Exception(
                    get_class($this) .
                     "::getUsageAttribute() : lớp '{$class}' chính xác phải có một XPHP_Attribute_Usage");
                }
            } else {
                sort($usage, SORT_ASC);
                if (count($usage) < 1 ||
                 ! ($usage[0] instanceof XPHP_Attribute_Usage))
                    throw new XPHP_Attribute_Exception(
                    get_class($this) .
                     "::getUsageAttribute() : lớp '{$class}' chính xác phải có một XPHP_Attribute_Usage (không có Attribute khác được phép)");
                else
                    $usage = $usage[0];
            }
            $this->usage[$class] = $usage;
        }
        return $this->usage[$class];
    }
    /**
     * Lấy ra các Attributes được áp dụng cho lớp
     *
     * @param mixed $class Tên lớp, đối tượng, thể hiện của ReflectionClass
     * @param string $type name/class/abstract/interface Attribute  - Nếu được truyền vào chỉ có các Attribute có kiểu này được trả về.
     * @return array Mảng các Attributes
     */
    public function getClassAttributes ($class, $type = null)
    {
        if (is_object($class))
            $class = get_class($class);
        else 
            if ($class instanceof ReflectionClass)
                $class = $class->getName();
        if (! class_exists($class))
            throw new XPHP_Attribute_Exception(
            get_class($this) .
             "::getClassAttributes() : chưa định nghĩa lớp {$class}");
        if ($type === null)
            return $this->getAttributes($class);
        else
            return $this->filterAttributes($this->getAttributes($class), $type);
    }
    /**
     * Lấy ra các Attributes được áp dụng cho phương thức
     *
     * @param mixed $class Tên lớp, đối tượng, thể hiện của ReflectionClass, hoặc ReflectionMethod
     * @param string $method Tên một phương thức định nghĩa trong class (để null, nếu tham số thứ nhất là thể hiện ReflectionMethod)
     * @param string $type name/class/abstract/interface Attribute  - Nếu được truyền vào chỉ có các Attributes có kiểu này được trả về.
     * @return array Mảng các Attributes
     */
    public function getMethodAttributes ($class, $method = null, $type = null)
    {
        if (is_object($class))
            $class = get_class($class);
        else 
            if ($class instanceof ReflectionClass)
                $class = $class->getName();
            else 
                if ($class instanceof ReflectionMethod) {
                    $method = $class->name;
                    $class = $class->class;
                }
        if (! class_exists($class))
            throw new XPHP_Attribute_Exception(
            get_class($this) .
             "::getMethodAttributes() : chưa định nghĩa lớp {$class}");
        if (! method_exists($class, $method))
            throw new XPHP_Attribute_Exception(
            get_class($this) .
             "::getMethodAttributes() : chưa định nghĩa phương thức {$class}::{$method}()");
        if ($type === null)
            return $this->getAttributes($class, 'method', $method);
        else
            return $this->filterAttributes(
            $this->getAttributes($class, 'method', $method), $type);
    }
    /**
     * Lấy ra các Attributes được áp dụng cho thuộc tính
     *
     * @param mixed $class Tên lớp, đối tượng, thể hiện của ReflectionClass, hoặc ReflectionProperty
     * @param string $property Tên một thuộc tính định nghĩa trong class (để null, nếu tham số thứ nhất là thể hiện ReflectionProperty)
     * @param string $type name/class/abstract/interface Attribute  - Nếu được truyền vào chỉ có các Attributes có kiểu này được trả về.
     * @return array Mảng các Attributes
     */
    public function getPropertyAttributes ($class, $property = null, $type = null)
    {
        if (is_object($class))
            $class = get_class($class);
        else 
            if ($class instanceof ReflectionClass)
                $class = $class->getName();
            else 
                if ($class instanceof ReflectionProperty) {
                    $property = $class->name;
                    $class = $class->class;
                }
        if (! class_exists($class))
            throw new XPHP_Attribute_Exception(
            get_class($this) .
             "::getPropertyAttributes() : chưa định nghĩa lớp {$class}");
        if ($type === null)
            return $this->getAttributes($class, 'property', '$' . $property);
        else
            return $this->filterAttributes(
            $this->getAttributes($class, 'property', '$' . $property), $type);
    }
}