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
 * @package		XPHP_Widget
 * @author		XWEB Dev Team
 * @copyright	Copyright (c) 2010-2011 XWEB. (http://xweb.vn)
 * @license		http://xphp.xweb.vn/license.html     GNU GPL License
 * @version		$Id: Widget.php 20109 2011-22-08 02:05:09 Mr.UBKey $
 */
/**
 * XPHP_Xml.
 *
 * @see XPHP_Xml
 */
require_once 'XPHP/Widget/Abstract.php';
/**
 * XPHP_Xml.
 *
 * @see XPHP_Xml
 */
require_once 'XPHP/Widget/Exception.php';
/**
 * XPHP_Widget.
 *
 * @category	XPHP
 * @package		XPHP_Widget
 * @author		Mr.UBKey
 * @link		http://xphp.xweb.vn/user_guide/xphp_widget.html
 */
class XPHP_Widget
{
    /**
     * Phần content được đặt giữa begin() và end()
     * @var string
     */
    private $_content;
    /**
     * Tên widget
     * @var string
     */
    private $_name;
    /**
     * Các thiết lập của widget
     * @var array
     */
    private $_options;
    /**
     * Bắt đầu một Widget có thiết lập content
     * @param string $name
     * @param array $options
     */
    public function begin ($name, $options = array())
    {
        //Nếu một widget mà chưa đóng, throw ra lỗi
        if ($this->_name)
            trigger_error(
            'Phải đóng phiên end() trước khi bắt đầu begin() widget mới.', 
            E_USER_ERROR);
        $this->_name = $name;
        $this->_options = $options;
        ob_start();
        return true;
    }
    /**
     * Kết thúc Widget
     */
    public function end ()
    {
        //Nếu chưa begin đưa ra thông báo lỗi
        if (! $this->_name)
            trigger_error('Phải sử dụng begin() để mở widget trước khi end().', 
            E_USER_ERROR);
             //Gán content
        $this->_content = ob_get_contents();
        //Xóa trắng bộ đệm
        ob_end_clean();
        //Tạo widget
        $this->_createWidget();
        //Xóa thuộc tính tên file để có thể begin widget khác 
        $this->name = null;
    }
    /**
     * Tạo widget
     * @param       $name
     * @param array $options
     */
    public function widget ($name, $options = array())
    {
        //Gán tên và option
        $this->_name = $name;
        $this->_options = $options;
        //Tạo thuộc tính
        $this->_createWidget();
    }
    /**
     * Tạo widget
     * @param       $name
     * @param array $options
     */
    public function create ($name, $options = array())
    {
        //Gán tên và option
        $this->_name = $name;
        $this->_options = $options;
        //Tạo thuộc tính
        $this->_createWidget();
    }
    private function _createWidget ()
    {
        //Kiểm tra xem có tồn tại Widget có tên như trên không?
        if (! class_exists($this->_name))
            throw new XPHP_Widget_Exception(
            "Không tìm thấy Widget {$this->_name}");
             //Khởi tạo Widget với tham số truyền vào là các option
        $wClass = $this->_name;
        /* @var $widget XPHP_Widget_Abstract */
        $widget = new $wClass($this->_options);
        //Kiểm tra xem Widget có thừa kế từ lớp XPHP_Widget_Abstract hay không ?
        if (! ($widget instanceof XPHP_Widget_Abstract))
            throw new XPHP_Widget_Exception(
            "Lớp Widget {$this->_name} bắt buộc phải được thừa kế từ XPHP_Widget_Abstract");
             //Gán nội dung
        if (! empty($this->_content))
            $widget->setContent($this->_content);
             //init widget
        $widget->init();
        //Chạy widget
        $widget->render();
    }
}