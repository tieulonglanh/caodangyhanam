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
 * @package		XPHP_Form
 * @author		XWEB Dev Team
 * @copyright	Copyright (c) 2010-2011 XWEB. (http://xweb.vn)
 * @license		http://xphp.xweb.vn/license.html     GNU GPL License
 * @version		$Id: Form.php 20118 2011-22-08 02:05:09 Mr.UBKey $
 */
/**
 * XPHP_Html_Base.
 *
 * @see XPHP_Html_Base
 */
require_once 'XPHP/Html/Base.php';
/**
 * XPHP_String.
 *
 * @see XPHP_String
 */
require_once 'XPHP/String.php';
/**
 * XPHP_Config.
 *
 * @see XPHP_Config
 */
require_once 'XPHP/Config.php';
/**
 * XPHP_Exception.
 *
 * @see XPHP_Exception
 */
require_once 'XPHP/Exception.php';
/**
 * XPHP_Attribute.
 *
 * @see XPHP_Attribute
 */
require_once 'XPHP/Attribute.php';
/**
 * Lớp hỗ trợ View hiển thị các thành phần của form
 *
 * @category	XPHP
 * @package		XPHP_Form
 * @author		Mr.UBKey
 * @author		BuiPhong
 * @link		http://xphp.xweb.vn/user_guide/xphp_form.html
 */
class XPHP_Form
{
    /**
     * Model được sử dụng trong View Result
     * @var XPHP_Model
     */
    protected $model;
    /**
     * Router của hệ thống
     * @var XPHP_Router
     */
    private $_router;
    /**
     * Mã javascript
     * @var bool
     */
    private $_script = "";
    /**
     * Tên form
     */
    private $_formName;
    /**
     * Cho phép valid dữ liệu phía client
     * @var bool
     */
    public $enableClentSideValidation = true;
    /**
     * Cho phép sử dụng Unobtrusive
     * @var bool
     */
    public $enableUnobtrusive = true;
    /**
     * @var XPHP_Html_ValidationUnobtrusive
     */
    private $_validationUnobtrusive;
    /**
     * Model của hệ thống
     * @param XPHP_Router $router
     * @param XPHP_Action_Result_View $viewResult
     */
    public function __construct ($router = null, $actionResultView = null)
    {
        //Load cấu hình trong file config với node view
        XPHP_Config::load($this, 'form');
        if ($router !== null)
            $this->_router = $router;
        if ($actionResultView !== null)
            $this->model = &$actionResultView->model;
        if ($this->enableUnobtrusive && $this->enableClentSideValidation)
            $this->_validationUnobtrusive = new XPHP_Html_ValidationUnobtrusive(
            $actionResultView);
    }
    /**
     * Mở ra một form mới
     * @param string $name Tên (ID) của form
     * @param array $attrs Mảng các attributes name => value
     */
    public function begin ($name = null, $attrs = array())
    {
        if ($name == null)
            $name = XPHP_String::randomString(5);
        if (! isset($attrs["method"]))
            $attrs["method"] = "post";
        if (! isset($attrs["id"]))
            $attrs["id"] = $name;
             //Gán tên form
        $this->_formName = $attrs["id"];
        //Lấy ra chuỗi attribute
        $attr = XPHP_Html_Base::htmlAttributes($attrs);
        echo "<form name='{$this->_formName}' $attr>";
    }
    /**
     * Kết thúc một form
     */
    public function end ()
    {
        echo "</form>";
    }
    public function beginAjax ($ajaxOption = array(), $name = null, $attrs = array())
    {
        //Phân tích các ajax option để đưa vào attribute
        $attrs['data-ajax'] = "true";
        foreach ($ajaxOption as $ajaxKey => $ajaxValue) {
            $option = "data-ajax-" . $ajaxKey;
            $attrs[$option] = $ajaxValue;
        }
        $this->begin($name, $attrs);
    }
    /**
     * Tạo label
     * @param string $text Text hiển thị trong label
     * @param array $attrs Mảng các attributes name => value
     */
    public function label ($text, $attrs = null)
    {
        if ($attrs !== null && is_array($attrs)) {
            $attr = XPHP_Html_Base::htmlAttributes($attrs);
            $html = "<label $attr>$text</label>";
        } else
            $html = "<label>$text</label>";
        echo $html;
    }
    /**
     * Tạo label cho Model
     * @param string $property Tên thuộc tính trong Model
     * @param array $attrs Mảng các attributes name => value
     */
    public function labelFor ($property, $attrs = null)
    {
        //Kiểm tra xem thuộc tính có trong Model hay không
        $this->_propertyExistsModel($property);
        $attrs["for"] = $property;
        //Lấy ra XPHPmodel_Attribute_Html_Label của thuộc tính 
        $labelAttribute = XPHP_Attribute::ofProperty($this->model, 
        $property, 'XPHP_Model_Attribute_Html_Label');
        //Nếu có định nghĩa attribute label
        if (isset($labelAttribute[0]))
            $this->label($labelAttribute[0]->text, $attrs);
             //Nếu không hiển thị tên thuộc tính
        else
            $this->label($property, $attrs);
    }
    /**
     * Tạo ô nhập liệu textbox
     * @param string $name Tên (ID) của hộp textbox
     * @param string $value Giá trị trong hộp
     * @param array $attrs Mảng các attributes name => value
     */
    public function textbox ($name, $value = null, $attrs = null)
    {
        //Nếu truyền vào giá trị
        if ($value !== null)
            $attrs["value"] = $value;
             //Nếu không truyền vào attribute id lấy tên làm id
        if (! isset($attrs["id"]))
            $attrs["id"] = $name;
        if ($attrs !== null && is_array($attrs)) {
            $attr = XPHP_Html_Base::htmlAttributes($attrs);
            $html = "<input name='$name' type='text' $attr/>";
        } else
            $html = "<input name='$name' type='text' />";
        echo $html;
    }
    /**
     * Tạo ô nhập liệu textbox cho Model
     * @param string $property Tên thuộc tính trong Model
     * @param array $attrs Mảng các attributes name => value
     * @throws XPHP_Exception
     */
    public function textboxFor ($property, $attrs = array())
    {
        //Kiểm tra xem thuộc tính có trong Model hay không
        $this->_propertyExistsModel($property);
        //Nếu có sử dụng Unobtrusive thì merge mảng attribute với mảng attribute của Unobtrusive
        if ($this->enableUnobtrusive)
            $attrs = array_merge($attrs, 
            $this->_validationUnobtrusive->get($property));
        if (! empty($this->model->$property))
            $attrs["value"] = $this->model->$property;
        $this->textbox($property, null, $attrs);
    }
    /**
     * Tạo ô nhập password
     * @param string $name Tên (ID) của hộp password
     * @param string $value Giá trị của hộp
     * @param array $attrs Mảng các attributes name => value
     */
    public function password ($name, $value = null, $attrs = null)
    {
        //Nếu truyền vào giá trị
        if ($value !== null)
            $attrs["value"] = $value;
             //Nếu không truyền vào attribute id lấy tên làm id
        if (! isset($attrs["id"]))
            $attrs["id"] = $name;
        if ($attrs !== null && is_array($attrs)) {
            $attr = XPHP_Html_Base::htmlAttributes($attrs);
            $html = "<input name='$name' type='password' $attr/>";
        } else
            $html = "<input name='$name' type='password' />";
        echo $html;
    }
    /**
     * Tạo ô nhập password cho Model
     * @param string $property Tên thuộc tính
     * @param array $attrs  Mảng các attributes name => value
     * @throws XPHP_Exception
     */
    public function passwordFor ($property, $attrs = array())
    {
        //Kiểm tra xem thuộc tính có trong Model hay không
        $this->_propertyExistsModel($property);
        //Nếu có sử dụng Unobtrusive thì merge mảng attribute với mảng attribute của Unobtrusive
        if ($this->enableUnobtrusive)
            $attrs = array_merge($attrs, 
            $this->_validationUnobtrusive->get($property));
        if (! empty($this->model->$property))
            $attrs["value"] = $this->model->$property;
        $this->password($property, null, $attrs);
    }
    /**
     * Tạo ô nhập textarea
     * @param string $name Tên của ô nhập liệu
     * @param string $value Giá trị của ô
     * @param array $attrs  Mảng các attributes name => value
     */
    public function textarea ($name, $value = null, $attrs = null)
    {
        //Nếu không truyền vào attribute id lấy tên làm id
        if (! isset($attrs["id"]))
            $attrs["id"] = $name;
        if ($attrs !== null && is_array($attrs)) {
            $attr = XPHP_Html_Base::htmlAttributes($attrs);
            $html = "<textarea name='$name' $attr>" . $value .
             "</textarea>";
        } else
            $html = "<textarea name='$name'>" . $value . "</textarea>";
        echo $html;
    }
    /**
     * Tạo ô nhập textbox cho Model
     * @param string $property Tên thuộc tính
     * @param array $attrs  Mảng các attributes name => value
     */
    public function textareaFor ($property, $attrs = array())
    {
        //Kiểm tra xem thuộc tính có trong Model hay không
        $this->_propertyExistsModel($property);
        //Nếu có sử dụng Unobtrusive thì merge mảng attribute với mảng attribute của Unobtrusive
        if ($this->enableUnobtrusive)
            $attrs = array_merge($attrs, $this->_validationUnobtrusive->get($property));
        $value = "";
        if (! empty($this->model->$property))
            $value = $this->model->$property;
        $this->textarea($property, $value, $attrs);
    }
    /**
     * Tạo một trường ẩn
     * @param string $name Tên của trường ẩn
     * @param string $value Giá trị
     * @param array $attrs  Mảng các attributes name => value
     */
    public function hidden ($name, $value = null, $attrs = null)
    {
        //Nếu truyền vào giá trị
        if ($value !== null)
            $attrs["value"] = $value;
             //Nếu không truyền vào attribute id lấy tên làm id
        if (! isset($attrs["id"]))
            $attrs["id"] = $name;
        if ($attrs !== null && is_array($attrs)) {
            $attr = XPHP_Html_Base::htmlAttributes($attrs);
            $html = "<input name='$name' type='hidden' $attr/>";
        } else
            $html = "<input name='$name' type='hidden' />";
        echo $html;
    }
    /**
     * Tạo một trường ẩn cho Model
     * @param string $property Tên thuộc tính
     * @param array $attrs  Mảng các attributes name => value
     */
    public function hiddenFor ($property, $attrs = array())
    {
        //Kiểm tra xem thuộc tính có trong Model hay không
        $this->_propertyExistsModel($property);
        //Nếu có sử dụng Unobtrusive thì merge mảng attribute với mảng attribute của Unobtrusive
        if ($this->enableUnobtrusive)
            $attrs = array_merge($attrs, 
            $this->_validationUnobtrusive->get($property));
        if (! empty($this->model->$property))
            $attrs["value"] = $this->model->$property;
        $this->hidden($property, null, $attrs);
    }
    /**
     * Tạo ô checkbox
     * @param string $name Tên ô checkbox
     * @param string $value Giấ trị của ô checkbox
     * @param array $attrs  Mảng các attributes name => value
     */
    public function checkbox ($name, $value = null, $attrs = null)
    {
        //Nếu truyền vào giá trị
        if ($value !== null) {
            if (is_bool($value))
                $attrs["checked"] = "checked";
            else
                $attrs["value"] = $value;
        }
        //Nếu không truyền giá trị thì giá trị của value là true
        $attrs["value"] = "true";
        //Nếu không truyền vào attribute id lấy tên làm id
        if (! isset($attrs["id"]))
            $attrs["id"] = $name;
        if ($attrs !== null && is_array($attrs)) {
            $attr = XPHP_Html_Base::htmlAttributes($attrs);
            $html = "<input type='checkbox' name='$name' $attr />";
        } else
            $html = "<input type='checkbox' name='$name' />";
        echo $html;
    }
    /**
     * Tạo một ô checkbox cho Model
     * @param string $property Tên thuộc tính
     * @param array $attrs  Mảng các attributes name => value
     */
    public function checkboxFor ($property, $attrs = array())
    {
        //Kiểm tra xem thuộc tính có trong Model hay không
        $this->_propertyExistsModel($property);
        //Nếu có sử dụng Unobtrusive thì merge mảng attribute với mảng attribute của Unobtrusive
        if ($this->enableUnobtrusive)
            $attrs = array_merge($attrs, 
            $this->_validationUnobtrusive->get($property));
        $value = null;
        if (! empty($this->model->$property))
            $value = $this->model->$property;
             //Nếu value = true thì check
        if ($value || $value == "true")
            $attrs["checked"] = "checked";
             //Gọi phương thức checkbox
        $this->checkbox($property, $value, $attrs);
    }
    /**
     * Tạo một nhóm checkbox
     * @param string $name Tên checkbox
     * @param array $arrOptions Các giá trị để lựa chọn
     * @param string $value Giá trị của ô checkbox
     * @param array $attrs  Mảng các attributes name => value
     */
    public function checkgroup($name, $arrOptions, $value=null, $attrs = array())
    {
    	$html = "<ul>";
    	$i = 1;
    	if($value !== null)
    	{
    		if(is_string($value))
    		{
    			$value = explode(',', $value);
    		}
    	}
    	foreach ($arrOptions as $key => $option) {
    		$attr = $attrs;
    		if($value !== null)
    		{
    			if(in_array($key, $value))
    				$attr['checked'] = 'checked';
    		}
    		$attr = XPHP_Html_Base::htmlAttributes($attr);
    		$html .=  "<li><input id='{$name}{$i}' type='checkbox' name='{$name}[]' value='{$key}' $attr /><label for='{$name}{$i}'>{$option}</label></li>";
    		$i++;
    	}
    	$html .= "</ul>";
    	echo $html;
    }
    /**
     * Tạo một nhóm checkbox cho model
     * @param string $property Tên thuộc tính
     * @param array $arrOptions Các giá trị để lựa chọn
     * @param array $attrs  Mảng các attributes name => value
     */
    public function checkgroupFor($property, $arrOptions, $attrs = array())
    {
    	//Kiểm tra xem thuộc tính có trong Model hay không
    	$this->_propertyExistsModel($property);
    	//Nếu có sử dụng Unobtrusive thì merge mảng attribute với mảng attribute của Unobtrusive
    	if ($this->enableUnobtrusive)
    		$attrs = array_merge($attrs,
    				$this->_validationUnobtrusive->get($property));
    	$value = null;
    	if (isset($this->model->$property))
    		$value = $this->model->$property;
    	$this->checkgroup($property, $arrOptions, $value, $attrs);
    }
    /**
     * Tạo một nhóm radio
     * @param string $name Tên thuộc tính
     * @param array $arrOptions Các giá trị để lựa chọn
     * @param string $value Giá trị của ô radio
     * @param array $attrs  Mảng các attributes name => value
     */
    public function radio ($name, $arrOptions, $value=null, $attrs = array())
    {
    	$html = "<ul>";
    	$i = 1;
    	foreach ($arrOptions as $key => $option) {
    		$attr = $attrs;
    		if($value !== null)
    		{
	    		if($key == $value)
	    			$attr['checked'] = 'checked';
    		}
    		$attr = XPHP_Html_Base::htmlAttributes($attr);
    		$html .= "<li><input type='radio' id='{$name}{$i}' name='{$name}' value='{$key}' $attr> <label for='{$name}{$i}'>{$option}</label></li>";
    		$i++;
    	}
    	$html .= "</ul>";
    	echo $html;
    }
    
    /**
     * Tạo hộp select cho Model
     * @param string $property Tên thuộc tính
     * @param array $arrOptions Danh sách các lựa chọn option của hộp
     * @param array $attrs  Mảng các attributes name => value
     */
    public function radioFor ($property, $arrOptions, $attrs = array())
    {
    	//Kiểm tra xem thuộc tính có trong Model hay không
    	$this->_propertyExistsModel($property);
    	//Nếu có sử dụng Unobtrusive thì merge mảng attribute với mảng attribute của Unobtrusive
    	if ($this->enableUnobtrusive)
    		$attrs = array_merge($attrs,
    				$this->_validationUnobtrusive->get($property));
    	$value = null;
    	if (isset($this->model->$property))
    		$value = $this->model->$property;
    	$this->radio($property, $arrOptions, $value, $attrs);
    }
    /**
     * Tạo một hộp select
     * @param string $name Tên hộp select
     * @param array $arrOptions Danh sách các lựa chọn option của hộp
     * @param string $value Giá trị của ô
     * @param string $valueID
     * @param string $nameID
     * @param array $attrs  Mảng các attributes name => value
     */
    public function select ($name, $arrOptions, $value = null, $valueID = null, $nameID = null, 
    $attrs = null)
    {
        if ($attrs !== null) {
            $attr = XPHP_Html_Base::htmlAttributes($attrs);
            //Nếu có id
            if (isset($attrs['id']))
                $html = "<select name='$name' $attr >";
                 //Không có id
            else
                $html = "<select name='$name' id='$name' $attr >";
        } else {
            $html = "<select name='$name' id='$name'>";
        }
        foreach ($arrOptions as $key => $option) {
            //Nếu không có valueID và nameID
            if ($valueID == null || $nameID == null)
                if ($value == $key)
                    $html .= '<option value="' . $key . '" selected="selected">' .
                     $option . '</option>';
                else
                    $html .= '<option value="' . $key . '">' . $option .
                     '</option>';
            else 
                if ($value == $option[$valueID])
                    $html .= '<option value="' . $option[$valueID] .
                     '" selected="selected">' . $option[$nameID] . '</option>';
                else
                    $html .= '<option value="' . $option[$valueID] . '">' .
                     $option[$nameID] . '</option>';
        }
        $html .= "</select>";
        echo $html;
    }
    /**
     * Tạo hộp select cho Model
     * @param string $property Tên thuộc tính
     * @param array $arrOptions Danh sách các lựa chọn option của hộp
     * @param unknown_type $valueID
     * @param unknown_type $nameID
     * @param array $attrs  Mảng các attributes name => value
     */
    public function selectFor ($property, $arrOptions, $valueID = null, 
    $nameID = null, $attrs = array())
    {
        //Kiểm tra xem thuộc tính có trong Model hay không
        $this->_propertyExistsModel($property);
        //Nếu có sử dụng Unobtrusive thì merge mảng attribute với mảng attribute của Unobtrusive
        if ($this->enableUnobtrusive)
            $attrs = array_merge($attrs, 
            $this->_validationUnobtrusive->get($property));
        $value = null;
        if (isset($this->model->$property))
            $value = $this->model->$property;
        $this->select($property, $arrOptions, $value, $valueID, $nameID, $attrs);
    }
    /**
     * Tạo nút submit
     * @param string $name Tên nút
     * @param string $value Giá trị
     * @param array $attrs  Mảng các attributes name => value
     */
    public function submit ($name = null, $value = null, $attrs = null)
    {
        //Nếu truyền vào tên
        if ($name !== null)
            $attrs["name"] = $name;
             //Nếu truyền vào giá trị
        if ($value !== null)
            $attrs["value"] = $value;
             //Nếu không truyền vào attribute id lấy tên làm id
        if (! isset($attrs["id"]) && isset($attrs["name"]))
            $attrs["id"] = $attrs["name"];
        if ($attrs !== null && is_array($attrs)) {
            $attr = XPHP_Html_Base::htmlAttributes($attrs);
            $html = "<input name='$name' type='submit' $attr/>";
        } else
            $html = "<input name='$name' type='submit' />";
        echo $html;
    }
    /**
     * Button reset
     * @param string $name Tên nút
     * @param string $value Giá trị
     * @param array $attrs  Mảng các attributes name => value
     */
    public function reset ($name = null, $value = null, $attrs = null)
    {
        //Nếu truyền vào tên
        if ($name !== null)
            $attrs["name"] = $name;
             //Nếu truyền vào giá trị
        if ($value !== null)
            $attrs["value"] = $value;
             //Nếu không truyền vào attribute id lấy tên làm id
        if (! isset($attrs["id"]) && isset($attrs["name"]))
            $attrs["id"] = $attrs["name"];
        if ($attrs !== null && is_array($attrs)) {
            $attr = XPHP_Html_Base::htmlAttributes($attrs);
            $html = "<input type='reset' $attr/>";
        } else
            $html = "<input type='reset' />";
        echo $html;
    }
    /**
     * Hiển thị thông báo lỗi từ Model
     * @param string $property Tên thuộc tính
     * @param array $attrs  Mảng các attributes name => value
     */
    public function validationMessage ($property, $attrs = null)
    {
        $errorMess = "";
        $messages = $this->model->getErrorMessage();
        if (! empty($messages)) {
            if (isset($messages[$property]) && sizeof($messages[$property]) > 0) {
                sort($messages[$property], SORT_ASC);
                $errorMess = $messages[$property][0];
            }
        }
        echo "<span class=\"field-validation-valid\" data-valmsg-for=\"{$property}\" data-valmsg-replace=\"true\">
			  {$errorMess}
			  </span>";
    }
    /**
     * Hiển thị script tự động render
     */
    public function scriptRegistrar ()
    {
        if ($this->enableClentSideValidation) {
            require_once 'XPHP/Html/Validation.php';
            $validation = new XPHP_Html_Validation($this->model);
            $validation->setFormName($this->_formName);
            $this->_script .= $validation->getScript();
            print $this->_script;
        }
    }
    /**
     * Phương thức kiểm tra xem trong Model có thuộc tính hay không
     * Nếu không có hiển thị thông báo lỗi
     * @param string $property Tên thuộc tính
     * @throws XPHP_Exception
     */
    private function _propertyExistsModel ($property)
    {
        if (! property_exists($this->model, $property)) {
            throw new XPHP_Exception(
            "Không tìm thấy thuộc tính $" . $property . " trong lớp " .
             get_class($this->model));
        }
    }
}