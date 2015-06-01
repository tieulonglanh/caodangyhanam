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
 * @package		XPHP_Xml
 * @author		XWEB Dev Team
 * @copyright	Copyright (c) 2010-2011 XWEB. (http://xweb.vn)
 * @license		http://xphp.xweb.vn/license.html     GNU GPL License
 * @version		$Id: Xml.php 20112 2011-22-08 02:05:09 Mr.UBKey $
 */
/**
 * Lớp hỗ trợ xử lý XML
 *
 * @category	XPHP
 * @package		XPHP_Xml
 * @author		Mr.UBKey
 * @link		http://xphp.xweb.vn/user_guide/xphp_xml.html
 */
class XPHP_Xml
{
    /**
     * DOMDocument
     * @var DOMDocument
     */
    public $dom;
    public function __construct ($xml)
    {
        $this->dom = new DOMDocument();
        if (is_file($xml))
            $this->dom->load($xml);
        else 
            if (is_string($xml))
                $this->dom->loadXML($xml);
        return false;
    }
    /**
     * XMLNode
     * @param DOMNode $node
     */
    private function _processToArray ($node)
    {
        $result = array();
        $occurance = array();
        if ($node->hasChildNodes()) {
            foreach ($node->childNodes as $child) {
                if (isset($occurance[$child->nodeName]))
                    $occurance[$child->nodeName] ++;
                else
                    $occurance[$child->nodeName] = 1;
            }
        }
        if ($node->nodeType == XML_TEXT_NODE) {
            $result = html_entity_decode(
            htmlentities($node->nodeValue, ENT_COMPAT, 'UTF-8'), ENT_COMPAT, 
            'UTF-8');
        } else {
            if ($node->hasChildNodes()) {
                $children = $node->childNodes;
                for ($i = 0; $i < $children->length; $i ++) {
                    $child = $children->item($i);
                    if ($child->nodeName != '#text') {
                        if ($occurance[$child->nodeName] > 1) {
                            $result[$child->nodeName][] = $this->_processToArray(
                            $child);
                        } else {
                            $result[$child->nodeName] = $this->_processToArray(
                            $child);
                        }
                    } else 
                        if ($child->nodeName == '#text') {
                            $text = $this->_processToArray($child);
                            if (trim($text) != '') {
                                $result[$child->nodeName] = $this->_processToArray(
                                $child);
                            }
                        }
                }
            }
            if ($node->hasAttributes()) {
                $attributes = $node->attributes;
                if (! is_null($attributes)) {
                    foreach ($attributes as $key => $attr) {
                        $result["@" . $attr->name] = $attr->value;
                    }
                }
            }
        }
        return $result;
    }
    /**
     * Lấy ra mảng từ xml
     */
    function getArray ()
    {
        return $this->_processToArray($this->dom->documentElement);
    }
    /**
     * Phương thức chuyển file xml thành mảng.
     * @param string $xmlUrl
     */
    public static function toArray ($xmlUrl, $arrSkipIndices = array())
    {
        $arrData = array();
        //Nếu dữ liệu truyền vào là kiểu chuỗi
        if (is_string($xmlUrl)) {
            $xmlStr = file_get_contents($xmlUrl);
            $xmlUrl = simplexml_load_string($xmlStr);
        }
        //Neu du lieu nhap vao la object
        if (is_object($xmlUrl)) {
            $xmlUrl = get_object_vars($xmlUrl);
        }
        if (is_array($xmlUrl)) {
            foreach ($xmlUrl as $index => $value) {
                if (is_object($value) || is_array($value)) {
                    $value = XPHP_Array::xmlToArray($value); // Gọi đệ quy
                }
                if (in_array($index, $arrSkipIndices)) {
                    continue;
                }
                $arrData[$index] = $value;
            }
        }
        return $arrData;
    }
}