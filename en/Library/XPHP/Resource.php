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
 * @package		XPHP_Resource
 * @author		XWEB Dev Team
 * @copyright	Copyright (c) 2010-2011 XWEB. (http://xweb.vn)
 * @license		http://xphp.xweb.vn/license.html     GNU GPL License
 * @version		$Id: Resource.php 20109 2011-22-08 02:05:09 Mr.UBKey $
 */
/**
 * XPHP_Xml.
 *
 * @see XPHP_Xml
 */
require_once 'XPHP/String.php';
/**
 * XPHP_Array.
 *
 * @see XPHP_Array
 */
require_once 'XPHP/Array.php';
/**
 * XPHP_Xml.
 *
 * @see XPHP_Xml
 */
require_once 'XPHP/Xml.php';
/**
 * XPHP_Cache_Simple.
 *
 * @see XPHP_Cache_Simple
 */
require_once 'XPHP/Cache/Simple.php';
/**
 * XPHP_Config.
 *
 * @see XPHP_Config
 */
require_once 'XPHP/Config.php';
/**
 * XPHP_Resource 
 *
 * @category	XPHP
 * @package		XPHP_Resource
 * @author		Mr.UBKey
 * @link		http://xphp.xweb.vn/user_guide/xphp_resource.html
 */
class XPHP_Resource
{
    /**
     * Đường dẫn tới thư mục có chứa resource
     * @var string
     */
    private static $resourcePath = 'Resources';
    /**
     * Mảng chứa toàn bộ resource của hệ thống
     * @var array
     */
    private static $resourceArray = array();
    /**
     * Ngôn ngữ được sử dụng
     * @var string VD: vi-VN, en-US
     */
    public $language;
    /**
     * Tên resource
     * @var string
     */
    public $name;
    /**
     * Tên resource được đặt default
     * @var string
     */
    public $default;
    /**
     * Khởi tạo
     */
    public function __construct ()
    {}
    /**
     * Set resource làm default
     */
    public function setDefault ()
    {
        require_once 'XPHP/Registry.php';
        XPHP_Registry::set("DefaultResource", $this);
    }
    /**
     * Kiểm tra xem có Resource được load trong hệ thống hay không ?
     * @param string $name
     */
    public function hasResource ($name = NULL)
    {
        if ($name === NULL)
            return sizeof(self::$resourceArray) > 0;
        else
            return isset(self::$resourceArray[$name]);
    }
    /**
     * Lấy ra ngôn ngữ của resource
     */
    public function getLanguage ()
    {
        return $this->language;
    }
    /**
     * Gán ngôn ngữ được sử dụng của resource
     * @param string $lang Mã chuẩn quốc tế VD: vi-VN, en-US
     */
    public function setLanguage ($lang)
    {
        $this->language = $lang;
    }
    /**
     * Lấy ra chuỗi
     * @param string $name
     */
    public function getString ($name)
    {
        //Lấy ra mảng các cấu hình
        if ($this->language)
            $resource = self::$resourceArray[$this->name][$this->language];
        else
            $resource = self::$resourceArray[$this->name]['default'];
            /*
         * Kiểm tra xem trong key có chứa các dấy kí tự đặc biệt > hay không?
         * Nếu không chọn key làm node
         * Nếu có trỏ config đến node tương ứng
         */
        $arrKey = explode('>', $name);
        if (sizeof($arrKey) == 1) {
            if ($this->hasPhrase($name))
                $str = $resource[$name];
        } else {
            $pNode = array();
            for ($i = 0; $i < sizeof($arrKey); $i ++) {
                //Trim khoảng trắng
                $arrKey[$i] = trim($arrKey[$i]);
                //Nếu là node gốc
                if ($i == 0)
                    $pNode = $resource[$name];
                else {
                    if (isset($pNode[$arrKey[$i]]))
                        $pNode = $pNode[$arrKey[$i]];
                }
            }
            $str = $pNode;
        }
        return isset($str['#text']) ? $str['#text'] : "";
    }
    public function __get ($name)
    {
        return $this->getString($name);
    }
    /**
     * Kiểm tra xem thẻ có giá trị hay không?
     * @param string $name
     */
    public function hasString ($name)
    {
        $str = $this->getString($name);
        return ! empty($str);
    }
    /**
     * Kiểm tra xem có thẻ này trong resource hay không?
     * @param string $name
     */
    public function hasPhrase ($name)
    {
        if ($this->language)
            $resource = self::$resourceArray[$this->name][$this->language];
        else
            $resource = self::$resourceArray[$this->name]['default'];
        if (isset($resource[$name]))
            return true;
        else
            return false;
    }
    /**
     * Thể hiện của lớp
     * @var XPHP_Resource
     */
    private static $instance;
    /**
     * Lấy ra thể hiện của lớp resource
     */
    public static function getInstance ()
    {
        return new self();
    }
    /**
     * Load tệp tin định nghĩa ngôn ngữ
     * @param string $name
     */
    public static function load ($name, $resource = NULL)
    {
        //Khởi tạo lớp Runtime
        $runtime = XPHP_Runtime::getInstance();
        $arrXmlFile = array();
        $handle = opendir(self::$resourcePath);
        if ($handle) {
            /*
			 * Lấy ra toàn bộ file kiểm tra 
			 * Nếu file bắt đầu bằng tên load
			 * và file kết thúc bằng .xml thì đưa vào mảng
			 */
            while (false !== ($file = readdir($handle))) {
                if (XPHP_String::startsWith($file, $name, false) &&
                 XPHP_String::endsWith($file, ".xml", false)) {
                    $arrXmlFile[] = $file;
                }
            }
        }
        //Nếu không truyền vào thể hiện một lớp
        //Lấy ra thể hiện của lớp
        if (! $resource) {
            $resource = self::getInstance();
            $resource->name = $name;
        }
        foreach ($arrXmlFile as $file) {
            //Phân tích chuỗi tên file để lấy mã chuẩn của ngôn ngữ
            $filePart = explode(".", $file);
            $filePart = XPHP_Array::trim($filePart);
            //Load file theo định dạng DOMDocument và lưu vào mảng với key = mã code và value = DOM
            if (sizeof($filePart) == 3) {
                //Kiểm tra xem có resource được lưu trong Runtime hay không ?
                $runtimeKey = "Resource_" . $filePart[0] .
                 $filePart[1];
                if (! isset($runtime->$runtimeKey)) {
                    $xml = new XPHP_Xml(
                    self::$resourcePath . DIRECTORY_SEPARATOR . $file);
                    //Gán resource vào mảng
                    self::$resourceArray[$filePart[0]][$filePart[1]] = $xml->getArray();
                    //Runtime
                    $runtime->$runtimeKey = $xml->getArray();
                } else
                    self::$resourceArray[$filePart[0]][$filePart[1]] = $runtime->$runtimeKey;
            } else 
                if (sizeof($filePart) == 2) {
                    //Kiểm tra xem có cache resource được lưu hay không ?
                    $runtimeKey = $filePart[0];
                    if (! isset($runtime->$runtimeKey)) {
                        $dom = new XPHP_Xml(
                        self::$resourcePath . DIRECTORY_SEPARATOR . $file);
                        //Gán resource vào mảng
                        self::$resourceArray[$filePart[0]]['default'] = $dom->getArray();
                        //Runtime
                        $runtime->$runtimeKey = $xml->getArray();
                    } else
                        self::$resourceArray[$filePart[0]]['default'] = $runtime->$runtimeKey;
                }
        }
        //Trả về thể hiện của lớp
        return $resource;
    }
    /**
     * Load thông tin resource trong file cấu hình
     * @param string $name
     */
    public static function loadConfig ($name)
    {
        //Instance
        $resource = self::getInstance();
        //Load cấu hình trong file config với node $name
        require_once 'XPHP/Config.php';
        XPHP_Config::load($resource, $name);
        //Lấy ra một số thông tin cấu hình
        $name = XPHP_Config::get("resource")->name;
        if (isset(XPHP_Config::get("resource")->default)) {
            if (XPHP_Config::get("resource")->default)
                //Nếu default true gán làm resource mặc định
                $resource->setDefault();
        }
        //Gọi đến phương thức load
        return self::load($name, $resource);
    }
}