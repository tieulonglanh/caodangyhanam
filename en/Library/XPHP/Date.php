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
 * @package		XPHP_Date
 * @author		XWEB Dev Team
 * @copyright	Copyright (c) 2010-2011 XWEB. (http://xweb.vn)
 * @license		http://xphp.xweb.vn/license.html     GNU GPL License
 * @version		$Id: Date.php 20109 2011-22-08 02:05:09 Mr.UBKey $
 */
/**
 * Lớp hỗ trợ Datetime
 *
 * @category	XPHP
 * @package		XPHP_Date
 * @author		Mr.UBKey
 * @link		http://xphp.xweb.vn/user_guide/xphp_date.html
 */
class XPHP_Date
{
    public function __construct ()
    {}
    /**
     * @desc Ham chuyen doi chuyen doi ngay thang kieu "H:i:s dd/mm/yyyy" hoac "dd/mm/yyyy" sang giay
     * @access public
     * @param $dateTime la chuoi co kieu "H:i:s dd/mm/yyyy" hoac "dd/mm/yyyy" 
     * @return int (so giay)
     * @note voi chuoi "H:i:s" co the bo qua tham so giay (s), phut (i)
     * @throws exception "chuoi truyen vao khong dung dinh dang"s
     */
    public static function toTime ($dateTime)
    {
        $hour = array(0 => 0, 1 => 0, 2 => 0);
        $dateMonth = array(0 => 0, 1 => 0, 2 => 0);
        $arr = explode(' ', $dateTime);
        if (is_array($arr) && sizeof($arr) > 1) {
            // chuyen doi "H:i:s" sang giay
            $hour = explode(':', $arr[0]);
            $dateMonth = explode('/', $arr[1]);
        } else {
            $dateMonth = explode('/', $dateTime);
        }
        //var_dump($dateMonth);
        try {
            return mktime($hour[0], $hour[1], $hour[2], $dateMonth[1], 
            $dateMonth[0], $dateMonth[2]);
        } catch (Exception $e) {
            return 0;
        }
    }
    /**
     * Make Timestamp
     * lay timestamp cua date
     *
     * @author Mr.UBKey
     * @param string $dateTime chuoi ngay thang
     * @param char $dateSep ki tu phan cach ngay thang
     * @param char $dateTimeSep 
     * @param char $timeSep ki tu phan cach thoi gian 
     * 
     * @return int TimeStamp
     */
    static public function makeTimestamp ($dateTime, $dateSep = '/', 
    $dateTimeSep = ' ', $timeSep = ':')
    {
        if ((! is_scalar($dateTime)) || $dateTime == null)
            throw new XPHP_Exception('Invalid date time string!');
        $dateTime = explode($dateTimeSep, $dateTime);
        $date = explode($dateSep, $dateTime[0]);
        if (isset($dateTime[1])) { // co chuoi gio
            $time = explode($timeSep, $dateTime[1]);
        }
        return mktime((int) $time[0], (int) $time[1], (int) $time[2], 
        (int) $date[1], (int) $date[0], (int) $date[2]);
    }
    /**
     * @desc Ham convert so giay ra: So ngay, so gio, so phut
     * @access public
     * @param $numberSecond so giay duoc truyen vao
     * @param $tag1 - the dinh dang font
     * @param $tag2 - dong dinh dang
     * @return ra chuoi co kieu: xxx ngay yy gio zz phut
     */
    public function doConvertSecond ($numberSecond, $tag1 = '', $tag2 = '')
    {
        $strTime = '';
        /**
         * @desc so ngay = so giay chia cho so giay trong 1 ngay
         */
        $day = floor($numberSecond / 86400);
        /**
         * @desc (phan nguyen so giay chia cho so giay trong 1 ngay) chia cho so giay trong 1 gio
         */
        $hour = floor(($numberSecond % 86400) / 3600);
        /**
         * @desc (phan nguyen so giay chia cho so giay trong 1 gio) chia cho 60 giay        
         */
        $minute = floor(($numberSecond % 3600) / 60);
        if ($minute != 0) {
            $strTime = $tag1 . $minute . $tag2 . "'";
        } else
            $strTime = '';
        if ($hour != 0)
            $strTime = $tag1 . $hour . $tag2 . 'h' . $strTime;
        if ($day != 0)
            $strTime = $tag1 . $day . $tag2 . ' ngày ' . $strTime;
        unset($day);
        unset($hour);
        unset($minute);
        return $strTime;
    }
    /** Ham convert thong tin bao nhieu ngay, gio, thang, nam
     *
     */
    public function convertFormatTime ($sub, $arrFormat = '')
    {
        $strTime = '';
        if (! is_array($arrFormat) or count($arrFormat) <= 0)
            $arrFormat = array(0 => 'giây', 1 => 'phút', 2 => 'giờ', 3 => 'ngày', 
            4 => 'tháng', 5 => 'năm');
        if (round($sub / (24 * 60 * 60 * 365)) >= 1) // be hon 1 nam
            $strTime .= round($sub / (24 * 60 * 60 * 365)) . ' ' .
             $arrFormat[5];
        else 
            if (round($sub / (24 * 60 * 60 * 30 * 12)) < 1 and
             round($sub / (24 * 60 * 60 * 30)) >= 1) // be hon 1 nam, lon hon 1 thang
                $strTime .= round($sub / (24 * 60 * 60 * 30)) .
                 ' ' . $arrFormat[4];
            else 
                if (round($sub / (24 * 60 * 60)) >= 1 and
                 round($sub / (24 * 60 * 60 * 30)) < 1) // be hon 1 thang, lon hon 1 ngay
                    $strTime .= round($sub / (24 * 60 * 60)) .
                     ' ' . $arrFormat[3];
                else 
                    if (round($sub / (60 * 60)) >= 1 and
                     round($sub / (24 * 60 * 60)) < 1) // be hon 1 ngay, lon hon 1 gio
                        $strTime .= round($sub / (60 *
                         60)) . ' ' . $arrFormat[2];
                    else 
                        if (round($sub / 60) >= 1 and round($sub / (60 * 60)) < 1) // be hon 1 gio
                            $strTime .= round(
                            $sub / 60) . ' ' . $arrFormat[1];
                        else 
                            if (round($sub / 60) < 1) // be hon 1 phut
                                $strTime .= $sub .
                                 ' ' . $arrFormat[0];
        return $strTime;
    }
    /**
     * Ngày này là ngày nào?
     * @param unknown_type $inputTime
     */
    static public function isTheDay ($numberSecond = 0)
    {
        $today = date("d/m/Y");
        $yesterday = date("d/m/Y", 
        mktime(0, 0, 0, date("m"), date("d") - 1, date("Y")));
        $tomorrow = date("d/m/Y", 
        mktime(0, 0, 0, date("m"), date("d") + 1, date("Y")));
        $the_day = date('d/m/Y', $numberSecond);
        if ($the_day == $today)
            return 'hôm nay';
        elseif ($the_day == $yesterday)
            return 'hôm qua';
        elseif ($the_day == $tomorrow)
            return 'mai';
        return $the_day;
    }
    /**
     * Đưa ra ngày tháng hiển thị theo dạng facebook style
     * Nguồn: http://www.lkdeveloper.com/facebook-style-date-in-php-date/
     * @param type $timestamp
     * @return type 
     */
    static public function RelativeTime($timestamp){

        $difference = time() - $timestamp; // Make different between this time and time value which pass throw $timestamp
        $periods = array("giây", "phút", "giờ", "ngày", "tuần", "tháng", "năm", "thập kỉ");
        $lengths = array("60","60","24","7","4.35","12","10");
        if($difference < 2*24*60*60){
            if ($difference > 0) { // this was in the past 
                $ending = "trước";
            } else { // this was in the future
                $difference = -$difference;
                $ending = "tới";
            }
            for($j = 0; $difference >= $lengths[$j]; $j++) $difference /= $lengths[$j];
            $difference = round($difference);
            if($difference != 1) $periods[$j].= "";
            $text = "$difference $periods[$j] $ending";
            return $text;
        }else{
            return date('d-m-Y H:i:s',$timestamp);
        }
    } 
    /**
     * Đưa ra ngày tháng hiển thị theo dạng facebook style
     * Nguồn: http://www.lkdeveloper.com/facebook-style-date-in-php-date/
     * @param type $timestamp
     * @return type 
     */
    static public function RelativeTime2($timestamp){
        $difference = time() - $timestamp; // Make different between this time and time value which pass throw $timestamp
        $periods = array("giây", "phút", "giờ", "ngày", "tuần", "tháng", "năm", "thập kỉ");
        $lengths = array("60","60","24","7","4.35","12","10");
        if($difference < 2*24*60*60){
            if ($difference >= 0) { // this was in the past 
                $ending = "trước";
            } else { // this was in the future
                $difference = -$difference;
                $ending = "tới";
            }
            for($j = 0; $difference >= $lengths[$j]; $j++) $difference /= $lengths[$j];
            $difference = round($difference);
            if($difference != 1) $periods[$j].= "";
            $text = "$difference $periods[$j] $ending";
            return $text;
        }else{
            return date('d/m/Y',$timestamp);
        }
    } 
}