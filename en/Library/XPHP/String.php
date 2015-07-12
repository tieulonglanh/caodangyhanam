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
 * @package		XPHP_String
 * @author		XWEB Dev Team
 * @copyright	Copyright (c) 2010-2011 XWEB. (http://xweb.vn)
 * @license		http://xphp.xweb.vn/license.html     GNU GPL License
 * @version		$Id: String.php 20109 2011-22-08 02:05:09 Mr.UBKey $
 */
/**
 * Lớp hỗ trợ các vấn đề về chuỗi
 *
 * @category	XPHP
 * @package		XPHP_String
 * @author		Mr.UBKey
 * @link		http://xphp.xweb.vn/user_guide/xphp_string.html
 */
class XPHP_String
{
    /**
     * Mã hóa MD5 cho chuỗi theo quy tắc riêng
     * @param string $str Chuỗi cần mã hóa
     */
    public static function md5Encode ($str)
    {
        return md5(md5($str . "xphp.v5.2015"));
    }
    
    /**
     * Mã hóa MD5 cho chuỗi kèm theo tham số
     * @param string $str Chuỗi cần mã hóa
     */
    public static function md5WithSaltEncode ($str, $salt)
    {
    	return md5(md5($str) . $salt);
    }
    
    /**
     * Phương thức tạo chuỗi ngẫu nhiên
     * @param int $num Số kí tự của chuỗi
     */
    public static function randomString ($num)
    {
        $characters = 'QWERTYUIOPLKJHGFDSAZXCVBNM0123456789qwertyuioplkjhgfdsazxcvbnm';
        $string = '';
        for ($p = 0; $p < $num; $p ++) {
            $string .= $characters[mt_rand(0, strlen($characters) - 1)];
        }
        return $string;
    }
    public static function secure ($s_str)
    {
        if((function_exists("get_magic_quotes_gpc") && get_magic_quotes_gpc())
           || (ini_get('magic_quotes_sybase') && (strtolower(ini_get('magic_quotes_sybase')) != "off")))
        {
            if(is_array($s_str))
            {
                foreach($s_str as $k => $v) $s_str[$k] = stripslashes($v);
            }
            else
                $s_str = stripslashes($s_str);
        }
        //HtmlSpecialCharsEncode
        //$s_str = self::HtmlStringEncode($s_str);
        //Script, blocked
        //$s_str = str_ireplace("script", "blocked", $s_str);
        //Sql inject
        //$s_str = mysql_escape_string($s_str);
        return $s_str;
    }
    public static function HtmlStringEncode ($html)
    {
        $out = htmlspecialchars(html_entity_decode($html, ENT_QUOTES, 'UTF-8'), 
        ENT_QUOTES, 'UTF-8');
        return $out;
    }
    public static function HtmlStringDecode ($html)
    {
        $out = html_entity_decode($html, ENT_QUOTES, 'UTF-8');
        return $out;
    }
    public static function HtmlEncode ($html)
    {
        return htmlentities($html);
    }
    public static function HtmlDecode ($html)
    {
        return html_entity_decode($html);
    }
    public static function trim_all ($string)
    {
        $arrChars = array("\t", "\n", "\r");
        foreach ($arrChars as $c) {
            $string = str_replace($c, "", $string);
        }
        return $string;
    }
    public static function removeSign ($str)
    {
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
        $str = preg_replace("/(đ)/", 'd', $str);
        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
        $str = preg_replace("/(Đ)/", 'D', $str);
        //$str = str_replace(" ", "-", str_replace("&*#39;","",$str));
        return $str;
    }
    public static function seo ($str)
    {
        $str = strtolower(self::removeSign($str));
        $str = str_replace("&*#39;", "", $str);
        $str = str_replace(" ", "-", $str);
        $str = str_replace("&", "", $str);
        $str = str_replace('"', "", $str);
        $str = str_replace("'", "", $str);
        $str = str_replace("”", "", $str);
        $str = str_replace("“", "", $str);
        $str = str_replace(".", "", $str);
        $str = str_replace("/", "", $str);
        $str = str_replace("[", "", $str);
        $str = str_replace("]", "", $str);
        $str = str_replace("(", "", $str);
        $str = str_replace(")", "", $str);
        $str = str_replace("{", "", $str);
        $str = str_replace("}", "", $str);
        return $str;
    }
    /**
     * Kiểm tra kí tự bắt đầu chuỗi
     * @param string $haystack Chuỗi cần kiểm tra
     * @param string $needle Chuỗi kiểm tra
     * @param boolean $case Có phân biệt chữ hoa thường hay không ?
     */
    public static function startsWith ($haystack, $needle, $case = true)
    {
        if ($case)
            return (strcmp(substr($haystack, 0, strlen($needle)), $needle) === 0);
        return (strcasecmp(substr($haystack, 0, strlen($needle)), $needle) === 0);
    }
    /**
     * Kiểm tra kí tự kết thúc chuỗi 
     * @param string $haystack Chuỗi cần kiểm tra
     * @param string $needle Chuỗi kiểm tra
     * @param boolean $case Có phân biệt chữ hoa thường hay không ?
     */
    public static function endsWith ($haystack, $needle, $case = true)
    {
        if ($case)
            return (strcmp(
            substr($haystack, strlen($haystack) - strlen($needle)), $needle) === 0);
        return (strcasecmp(
        substr($haystack, strlen($haystack) - strlen($needle)), $needle) === 0);
    }
    /**
     * Xóa các đoạn mã độc tấn công XSS
     * @author: Daniel Morris
     * @copyright: Daniel Morris
     * @license: GNU General Public License (GPL)
     * @param $source string Chuỗi cần clean
     */
    public static function xssClean ($source)
    {
        $tagsMethod = 0;
        $tagsArray = array();
        $tagBlacklist = array('applet', 'body', 'bgsound', 'base', 'basefont', 
        'embed', 'frame', 'frameset', 'head', 'html', 'id', 'iframe', 'ilayer', 
        'layer', 'link', 'meta', 'name', 'object', 'script', 'style', 'title', 
        'xml');
        $preTag = NULL;
        $postTag = $source;
        $tagOpen_start = strpos($source, '<');
        while ($tagOpen_start !== FALSE) {
            $preTag .= substr($postTag, 0, $tagOpen_start);
            $postTag = substr($postTag, $tagOpen_start);
            $fromTagOpen = substr($postTag, 1);
            $tagOpen_end = strpos($fromTagOpen, '>');
            if ($tagOpen_end === false)
                break;
            $tagOpen_nested = strpos($fromTagOpen, '<');
            if (($tagOpen_nested !== false) && ($tagOpen_nested < $tagOpen_end)) {
                $preTag .= substr($postTag, 0, ($tagOpen_nested + 1));
                $postTag = substr($postTag, ($tagOpen_nested + 1));
                $tagOpen_start = strpos($postTag, '<');
                continue;
            }
            $tagOpen_nested = (strpos($fromTagOpen, '<') + $tagOpen_start + 1);
            $currentTag = substr($fromTagOpen, 0, $tagOpen_end);
            $tagLength = strlen($currentTag);
            if (! $tagOpen_end) {
                $preTag .= $postTag;
                $tagOpen_start = strpos($postTag, '<');
            }
            $tagLeft = $currentTag;
            $attrSet = array();
            $currentSpace = strpos($tagLeft, ' ');
            if (substr($currentTag, 0, 1) == "/") {
                $isCloseTag = TRUE;
                list ($tagName) = explode(' ', $currentTag);
                $tagName = substr($tagName, 1);
            } else {
                $isCloseTag = FALSE;
                list ($tagName) = explode(' ', $currentTag);
            }
            if ((! preg_match("/^[a-z][a-z0-9]*$/i", $tagName)) || (! $tagName) ||
             ((in_array(strtolower($tagName), $tagBlacklist)))) {
                $postTag = substr($postTag, ($tagLength + 2));
                $tagOpen_start = strpos($postTag, '<');
                continue;
            }
            while ($currentSpace !== FALSE) {
                $fromSpace = substr($tagLeft, ($currentSpace + 1));
                $nextSpace = strpos($fromSpace, ' ');
                $openQuotes = strpos($fromSpace, '"');
                $closeQuotes = strpos(substr($fromSpace, ($openQuotes + 1)), 
                '"') + $openQuotes + 1;
                if (strpos($fromSpace, '=') !== FALSE) {
                    if (($openQuotes !== FALSE) && (strpos(
                    substr($fromSpace, ($openQuotes + 1)), '"') !== FALSE))
                        $attr = substr($fromSpace, 0, ($closeQuotes + 1));
                    else
                        $attr = substr($fromSpace, 0, $nextSpace);
                } else
                    $attr = substr($fromSpace, 0, $nextSpace);
                if (! $attr)
                    $attr = $fromSpace;
                $attrSet[] = $attr;
                $tagLeft = substr($fromSpace, strlen($attr));
                $currentSpace = strpos($tagLeft, ' ');
            }
            $tagFound = in_array(strtolower($tagName), $tagsArray);
            if ((! $tagFound && $tagsMethod) || ($tagFound && ! $tagsMethod)) {
                if (! $isCloseTag) {
                    $attrSet = self::_filterAttr($attrSet);
                    $preTag .= '<' . $tagName;
                    for ($i = 0; $i < count($attrSet); $i ++)
                        $preTag .= ' ' . $attrSet[$i];
                    if (strpos($fromTagOpen, "</" . $tagName))
                        $preTag .= '>';
                    else
                        $preTag .= ' />';
                } else
                    $preTag .= '</' . $tagName . '>';
            }
            $postTag = substr($postTag, ($tagLength + 2));
            $tagOpen_start = strpos($postTag, '<');
        }
        $preTag .= $postTag;
        return $preTag;
    }
    /**
     * Xóa các đoạn mã tấn công xss trong attribute
     * @author: Daniel Morris
     * @copyright: Daniel Morris
     * @license: GNU General Public License (GPL)
     * @param array $attrSet
     */
    private static function _filterAttr ($attrSet)
    {
        $attrArray = array();
        $attrMethod = 0;
        $attrBlacklist = array('action', 'background', 'codebase', 'dynsrc', 
        'lowsrc');
        $newSet = array();
        for ($i = 0; $i < count($attrSet); $i ++) {
            if (! $attrSet[$i])
                continue;
            $attrSubSet = explode('=', trim($attrSet[$i]));
            list ($attrSubSet[0]) = explode(' ', $attrSubSet[0]);
            if ((! eregi("^[a-z]*$", $attrSubSet[0])) || (((in_array(
            strtolower($attrSubSet[0]), $attrBlacklist)) ||
             (substr($attrSubSet[0], 0, 2) == 'on'))))
                continue;
            if ($attrSubSet[1]) {
                $attrSubSet[1] = str_replace('&#', '', $attrSubSet[1]);
                $attrSubSet[1] = preg_replace('/\s+/', '', $attrSubSet[1]);
                $attrSubSet[1] = str_replace('"', '', $attrSubSet[1]);
                if ((substr($attrSubSet[1], 0, 1) == "'") &&
                 (substr($attrSubSet[1], (strlen($attrSubSet[1]) - 1), 1) == "'"))
                    $attrSubSet[1] = substr($attrSubSet[1], 1, 
                    (strlen($attrSubSet[1]) - 2));
                $attrSubSet[1] = stripslashes($attrSubSet[1]);
            }
            if (((strpos(strtolower($attrSubSet[1]), 'expression') !== false) &&
             (strtolower($attrSubSet[0]) == 'style')) ||
             (strpos(strtolower($attrSubSet[1]), 'javascript:') !== false) ||
             (strpos(strtolower($attrSubSet[1]), 'behaviour:') !== false) ||
             (strpos(strtolower($attrSubSet[1]), 'vbscript:') !== false) ||
             (strpos(strtolower($attrSubSet[1]), 'mocha:') !== false) ||
             (strpos(strtolower($attrSubSet[1]), 'livescript:') !== false))
                continue;
            $attrFound = in_array(strtolower($attrSubSet[0]), $attrArray);
            if ((! $attrFound && $attrMethod) || ($attrFound && ! $attrMethod)) {
                if ($attrSubSet[1])
                    $newSet[] = $attrSubSet[0] . '="' . $attrSubSet[1] . '"';
                else 
                    if ($attrSubSet[1] == "0")
                        $newSet[] = $attrSubSet[0] . '="0"';
                    else
                        $newSet[] = $attrSubSet[0] . '="' . $attrSubSet[0] . '"';
            }
        }
        return $newSet;
    }

    /**
     * Hàm rút ngắn đoạn văn bản
     * @param        $text Chuỗi cần cắt
     * @param int    $maxlength Độ dài tối đa
     * @param string $appendix Kí tự thêm vào sau khi cắt
     *
     * @return string
     */
    public static function shortenText($str, $maxlen = 70, $appendix = "...")
    {
        if (strlen($str) <= $maxlen) return $str;

        $newstr = substr($str, 0, $maxlen);
        if (substr($newstr, -1, 1) != ' ') $newstr = substr($newstr, 0, strrpos($newstr, " "));
    
        $newstr .= $appendix;
        return $newstr;
    }
    
    public static function maskEmail( $email, $mask_char, $percent=50 ) 
    { 

        list( $user, $domain ) = preg_split("/@/", $email ); 

        $len = strlen( $user ); 

        $mask_count = floor( $len * $percent /100 ); 

        $offset = floor( ( $len - $mask_count ) / 2 ); 

        $masked = substr( $user, 0, $offset ) 
                .str_repeat( $mask_char, $mask_count ) 
                .substr( $user, $mask_count+$offset ); 


        return( $masked.'@'.$domain ); 
    } 
    
    public static function hideInput($input, $mask_char, $percent=50) {
        $len = strlen( $input ); 

        $mask_count = floor( $len * $percent /100 ); 

        $offset = floor( ( $len - $mask_count ) / 2 ); 

        $masked = substr( $input, 0, $offset ) 
                .str_repeat( $mask_char, $mask_count ) 
                .substr( $input, $mask_count+$offset ); 
        return $masked;
    }
}