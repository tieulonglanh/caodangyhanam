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
 * @package		XPHP_Config
 * @author		XWEB Dev Team
 * @copyright	Copyright (c) 2010-2011 XWEB. (http://xweb.vn)
 * @license		http://xphp.xweb.vn/license.html     GNU GPL License
 * @version		$Id: Config.php 20109 2011-22-08 02:05:09 Mr.UBKey $
 */
/**
 * XPHP_Config_Adapter.
 *
 * @see XPHP_Config_Adapter
 */
require_once 'XPHP/Config/Adapter.php';
/**
 * XPHP_DataType.
 *
 * @see XPHP_DataType
 */
require_once 'XPHP/DataType.php';
/**
 * XPHP_Array.
 *
 * @see XPHP_Array
 */
require_once 'XPHP/Array.php';
/**
 * Lớp cấu hình hệ thống
 *
 * @category	XPHP
 * @package		XPHP_Config
 * @author		Mr.UBKey
 * @link		http://xphp.xweb.vn/user_guide/xphp_config.html
 */
class XPHP_Config
{
    public static function load (&$object, $key)
    {
        //Lấy ra mảng các cấu hình
        $configs = self::getConfig($key);
        //Nếu configs là false thì trả về false không tìm thấy dữ liệu trong cấu hình
        if (! $configs)
            return $configs;
             //Nếu có configs
        //Xử lý gán các giá trị tương ứng
        foreach ($configs as $k => $c) {
            //Trim tên của thuộc tính
            $prop = trim($k);
            //Kiểm tra và gán các giá trị tương ứng
            if (is_array($c) && sizeof($c) > 0) {
                //Nếu thuộc tính là một chuỗi
                if (isset($c['#text'])) {
                    //Kiểm tra xem có định nghĩa kiểu không ?
                    if (isset($c['@type'])) {
                        $ntext = trim($c['#text']);
                        $ntext = XPHP_DataType::convert($ntext, 
                        trim($c['@type']));
                        $object->$prop = $ntext;
                    } else
                        $object->$prop = trim($c['#text']);
                } else 
                    if ($prop == 'helper' && isset($c['@name']) &&
                     isset($c['@class'])) {
                        //Nếu thuộc tính là một object helper của lớp
                        //Tên thuộc tính là object helper
                        $pname = $c['@name'];
                        //Tên lớp helper
                        $cname = $c['@class'];
                        $object->$pname = new $cname();
                    } else 
                        if (is_array($c) && ! isset($c['#text'])) {
                            //Nếu thuộc tính là một object và gán giá trị vào object đấy
                            //Object Sub Property
                            $propsubObj = new stdClass();
                            foreach ($c as $ksub => $csub) {
                                //Tên sub property
                                $propsub = trim(
                                $ksub);
                                //Nếu là node chứa text
                                if (is_array(
                                $csub) && isset($csub['#text']) &&
                                 sizeof($csub) == 1) {
                                    //Kiểm tra xem có định nghĩa kiểu không?
                                    if (isset(
                                    $csub['@type'])) {
                                        $ntextsub = trim($csub['#text']);
                                        $ntextsub = XPHP_DataType::convert(
                                        $ntextsub, trim($csub['@type']));
                                        $object->$prop->$propsub = $ntextsub;
                                    } else
                                        $object->$prop->$propsub = trim(
                                        $csub['#text']);
                                } else {
                                    //Nếu $csub là mảng các object
                                    if (is_array(
                                    $csub)) {
                                        //Nếu là node chứa attribute
                                        $arrPropSubObj = new stdClass();
                                        //Each từng thuộc tính đưa vào stdClass
                                        foreach ($csub as $ks => $cs) {
                                            $ks = str_replace("@", "", $ks);
                                            $arrPropSubObj->$ks = $cs;
                                        }
                                        $object->$prop->$propsub = $arrPropSubObj;
                                    } else 
                                        if (is_string($csub)) {
                                            //Nếu là chuỗi thì đưa vào stdClass(trước foreach)
                                            $ksub = str_replace(
                                            "@", "", $ksub);
                                            $propsubObj->$ksub = $csub;
                                        }
                                }
                            }
                            //Nếu stdClass trước foreach con có thuộc tính
                            if (sizeof(
                            get_object_vars($propsubObj)) > 0)
                                $object->$prop = $propsubObj;
                        }
            } else 
                if (is_string($c)) {
                    $k = str_replace("@", "", $k);
                    $object->$k = $c;
                }
        }
    }
    public static function get ($key)
    {
        //Tạo một stdObject và load cấu hình vào object trả về object
        $ob = new stdClass();
        self::load($ob, $key);
        return $ob;
    }
    /**
     * Lấy cấu hình và trả về mảng
     * 
     * @param mixed $key
     * @return array
     */
    public static function getConfig ($key)
    {
        //Lấy ra mảng các cấu hình
        $configs = array();
        /*
         * Kiểm tra xem trong key có chứa các dấy kí tự đặc biệt > hay không?
         * Nếu không chọn key làm node
         * Nếu có trỏ config đến node tương ứng
         */
        $arrKey = explode('>', $key);
        if (sizeof($arrKey) == 1) {
            if (XPHP_Config_Adapter::hasConfig($key))
                $configs = XPHP_Config_Adapter::getConfig($key);
        } else {
            $pNode = array();
            for ($i = 0; $i < sizeof($arrKey); $i ++) {
                //Trim khoảng trắng
                $arrKey[$i] = trim($arrKey[$i]);
                //Nếu là node gốc
                if ($i == 0)
                    $pNode = XPHP_Config_Adapter::getConfig($arrKey[$i]);
                else {
                    if (isset($pNode[$arrKey[$i]]))
                        $pNode = $pNode[$arrKey[$i]];
                }
            }
            $configs = $pNode;
        }
        return $configs;
    }
}