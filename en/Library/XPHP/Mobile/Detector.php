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
 * @package		XPHP_Mobile
 * @author		XWEB Dev Team
 * @copyright	Copyright (c) 2010-2011 XWEB. (http://xweb.vn)
 * @license		http://xphp.xweb.com/license.html     GNU GPL License
 * @version		$Id: Detector.php 20119 2011-20-09 09:55:29 Mr.UBKey $
 */
/**
 * Lớp nhận biết các thiết bị mobile
 *
 * @category	XPHP
 * @package		XPHP_Mobile
 * @author		Mr.UBKey
 * @link		http://xphp.xweb.vn/user_guide/xphp_mobile_detector.html
 */
class XPHP_Mobile_Detector
{
    /**
     * Header của request
     * @var string
     */
    private $_httpAccept;
    /**
     * Header của request lưu trữ thông tin về trình duyệt
     * @var string
     */
    private $_httpUserAgent;
    /**
     * Mảng định nghĩa các thiết bị mobile và Regex của nó
     * @var array
     */
    private $_devices;
    /**
     * Sử dụng mobile
     * @var boolean
     */
    public $isMobile = false;
    /**
     * Thiết bị đang được sử dụng hiện tại
     * @var string
     */
    public $device;
    /**
     * Lớp nhận biết các thiết bị mobile
     */
    public function __construct ()
    {
        //Lấy ra thông tin phía người dùng
        $this->_httpAccept = $_SERVER['HTTP_ACCEPT'];
        $this->_httpUserAgent = $_SERVER['HTTP_USER_AGENT'];
        //Các thiết bị và Regex của nó
        $this->_devices = array("android" => "android", 
        "blackberry" => "blackberry", "iphone" => "(iphone|ipod)", 
        "opera" => "opera mini", 
        "palm" => "(avantgo|blazer|elaine|hiptop|palm|plucker|xiino)", 
        "windows" => "windows ce; (iemobile|ppc|smartphone)", 
        "generic" => "(kindle|mobile|mmp|midp|o2|pda|pocket|psp|symbian|smartphone|treo|up.browser|up.link|vodafone|wap)");
        //Nếu sử dụng WAP
        if (isset($_SERVER['HTTP_X_WAP_PROFILE']) ||
         isset($_SERVER['HTTP_PROFILE'])) {
            $this->isMobile = true;
        } else 
            if (strpos($this->_httpAccept, 'text/vnd.wap.wml') > 0 ||
             strpos($this->_httpAccept, 'application/vnd.wap.xhtml+xml') > 0) {
                $this->isMobile = true;
            } else {
                //Đánh dấu để bỏ generic
                $hasDevice = false;
                //Nếu sử dụng các thiết bị mobile
                foreach ($this->_devices as $device => $regexp) {
                    if ($this->_checkDevice($device)) {
                        $this->isMobile = true;
                        if (! $hasDevice)
                        {
                            $this->device = $device;
                            $hasDevice = true;
                        }
                    }
                }
            }
    }
    /**
     * Lấy ra thuộc tính chứa thông tin kiểm tra của thiết bị mobile
     * @param string $key VD: isMobile, isGeneric, isIphone, isAndroid
     */
    public function __get ($key)
    {
        return $this->$key;
    }
    /**
     * Kiểm tra với toàn bộ các thiết bị di động bằng Regex của nó
     * @param string $device Tên loại thiết bị
     */
    private function _checkDevice ($device)
    {
        //Tên thuộc tính kiểm tra thiết bị mobile
        $var = "is" . ucfirst($device);
        //Kiểm tra xem đã check thiết bị mobile chưa
        if (isset($this->$var) && $this->$var)
            $result = $this->$var;
        else {
            $result = preg_match("/" . $this->_devices[$device] . "/i", 
            $this->_httpUserAgent);
        }
        if ($result && $device != 'generic') {
            $this->isGeneric = false;
        }
        //Lưu kết quả vào thuộc tính
        $this->$var = $result;
        //Trả về kết quả
        return $result;
    }
}