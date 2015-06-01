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
 * @package		XPHP_Rest
 * @author		Mr.UBKey
 * @author		https://github.com/philsturgeon/codeigniter-restserver
 * @copyright	Copyright (c) 2010-2011 XWEB. (http://xweb.vn)
 * @license		http://xphp.xweb.vn/license.html     GNU GPL License
 * @version		$Id: Controller.php 201110 2011-12-10 23:44:09 Mr.UBKey $
 */
/**
 * XPHP_Controller_Abstract
 * 
 * @see XPHP_Controller_Abstract
 */
require_once 'XPHP/Controller/Abstract.php';
/**
 * XPHP_Rest_Utils
 * 
 * @see XPHP_Rest_Utils
 */
require_once 'XPHP/Rest/Utils.php';
/**
 * XPHP_Rest_Request
 * 
 * @see XPHP_Rest_Request
 */
require_once 'XPHP/Rest/Request.php';
/**
 * XPHP_String.
 *
 * @see XPHP_String
 */
require_once 'XPHP/String.php';
/**
 * XPHP_Cache.
 *
 * @see XPHP_Cache
 */
require_once 'XPHP/Cache.php';
/**
 * XPHP_Session
 * 
 * @see XPHP_Session
 */
require_once 'XPHP/Session.php';
/**
 * XPHP_Cookie
 * 
 * @see XPHP_Cookie 
 */
require_once 'XPHP/Cookie.php';
/**
 * XPHP_Rest_Controller Controller của RestService
 *
 * @category	XPHP
 * @package		XPHP_Rest
 * @author		Mr.UBKey
 * @author		https://github.com/philsturgeon/codeigniter-restserver
 * @link		http://xphp.xweb.vn/user_guide/xphp_rest_request.html
 */
class XPHP_Rest_Controller extends XPHP_Controller_Abstract
{
    /**
     * Hỗ trợ cache
     * @var XPHP_Cache
     */
    protected $cache;
    /**
     * Xử lý resource
     * @var XPHP_Resource
     */
    protected $resource;
    /**
     * Hỗ trợ session
     * @var XPHP_Session
     */
    protected $session;
    /**
     * Hỗ trợ cookie
     */
    protected $cookie;
    /**
     * XPHP_Rest_Request
     * @var XPHP_Rest_Request
     */
    protected $request;
    public function __construct ($router)
    {
        parent::__construct($router);
        //Khởi tạo lớp hỗ trợ cache
        $this->cache = new XPHP_Cache();
        //Khởi tạo lớp hỗ trợ Session
        $this->session = XPHP_Session::getInstance();
        //Khởi tạo lớp hỗ trợ Cookie
        $this->cookie = new XPHP_Cookie();
        //Xử lý resource
        if (XPHP_Registry::isRegistered("DefaultResource"))
            $this->resource = XPHP_Registry::get("DefaultResource");
             //Xử lý request
        $this->request = XPHP_Rest_Utils::processRequest();
        $this->params = array_merge($this->params, 
        (array) $this->request->getData());
    }
    public function response ($data = array(), $http_code = 200)
    {
        // If the format method exists, call and return the output in that format
        if (method_exists($this, 
        '_format_' . $this->request->getFormat())) {
            // Set the correct format header
            $contentType = $this->request->getFormatContentType();
            $formatted_data = $this->{'_format_' . $this->request->getFormat()}(
            $data);
            $output = $formatted_data;
        } else {
            // Format not supported, output directly
            $output = $data;
        }
        XPHP_Rest_Utils::sendResponse($http_code, $output, $contentType);
    }
    // Force it into an array
    private function _force_loopable ($data)
    {
        // Force it to be something useful
        if (! is_array($data) and ! is_object($data)) {
            $data = (array) $data;
        }
        return $data;
    }
    // FORMATING FUNCTIONS ---------------------------------------------------------
    // Format XML for output
    private function _format_xml ($data = array(), $structure = NULL, 
    $basenode = 'xml')
    {
        // turn off compatibility mode as simple xml throws a wobbly if you don't.
        if (ini_get('zend.ze1_compatibility_mode') == 1) {
            ini_set('zend.ze1_compatibility_mode', 0);
        }
        if ($structure == NULL) {
            $structure = simplexml_load_string(
            "<?xml version='1.0' encoding='utf-8'?><$basenode />");
        }
        // loop through the data passed in.
        $data = $this->_force_loopable($data);
        foreach ($data as $key => $value) {
            // no numeric keys in our xml please!
            if (is_numeric($key)) {
                // make string key...
                //$key = "item_". (string) $key;
                $key = "item";
            }
            // replace anything not alpha numeric
            $key = preg_replace('/[^a-z_]/i', '', $key);
            // if there is another array found recrusively call this function
            if (is_array($value) || is_object($value)) {
                $node = $structure->addChild($key);
                // recrusive call.
                $this->_format_xml($value, $node, $basenode);
            } else {
                // Actual boolean values need to be converted to numbers
                is_bool($value) and $value = (int) $value;
                // add single node.
                $value = htmlspecialchars(
                html_entity_decode($value, ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 
                "UTF-8");
                $UsedKeys[] = $key;
                $structure->addChild($key, $value);
            }
        }
        // pass back as string. or simple xml object if you want!
        return $structure->asXML();
    }
    // Format Raw XML for output
    private function _format_rawxml ($data = array(), $structure = NULL, 
    $basenode = 'xml')
    {
        // turn off compatibility mode as simple xml throws a wobbly if you don't.
        if (ini_get('zend.ze1_compatibility_mode') == 1) {
            ini_set('zend.ze1_compatibility_mode', 0);
        }
        if ($structure == NULL) {
            $structure = simplexml_load_string(
            "<?xml version='1.0' encoding='utf-8'?><$basenode />");
        }
        // loop through the data passed in.
        $data = $this->_force_loopable($data);
        foreach ($data as $key => $value) {
            // no numeric keys in our xml please!
            if (is_numeric($key)) {
                // make string key...
                //$key = "item_". (string) $key;
                $key = "item";
            }
            // replace anything not alpha numeric
            $key = preg_replace('/[^a-z0-9_-]/i', '', $key);
            // if there is another array found recrusively call this function
            if (is_array($value) || is_object($value)) {
                $node = $structure->addChild($key);
                // recrusive call.
                $this->_format_rawxml($value, $node, $basenode);
            } else {
                // Actual boolean values need to be converted to numbers
                is_bool($value) and $value = (int) $value;
                // add single node.
                $value = htmlspecialchars(
                html_entity_decode($value, ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 
                "UTF-8");
                $UsedKeys[] = $key;
                $structure->addChild($key, $value);
            }
        }
        // pass back as string. or simple xml object if you want!
        return $structure->asXML();
    }
    // Format HTML for output
    private function _format_html ($data = array())
    {
        /*
        // Multi-dimentional array
        if (isset($data[0])) {
            $headings = array_keys($data[0]);
        } // Single array
else {
            $headings = array_keys($data);
            $data = array($data);
        }
        $this->load->library('table');
        $this->table->set_heading($headings);
        foreach ($data as &$row) {
            $this->table->add_row($row);
        }
        return $this->table->generate();
        */
        return "DOES NOT SUPPORT IN CURRENT VERSION";
    }
    // Format HTML for output
    private function _format_csv ($data = array())
    {
        // Multi-dimentional array
        if (isset($data[0])) {
            $headings = array_keys($data[0]);
        } // Single array
else {
            $headings = array_keys($data);
            $data = array($data);
        }
        $output = implode(',', $headings) . "\r\n";
        foreach ($data as &$row) {
            $output .= '"' . implode('","', $row) . "\"\r\n";
        }
        return $output;
    }
    // Encode as JSON
    private function _format_json ($data = array())
    {
        return json_encode($data);
    }
    // Encode as JSONP
    private function _format_jsonp ($data = array())
    {
        return $this->params['callback'] . '(' . json_encode($data) . ')';
    }
    // Encode as Serialized array
    private function _format_serialize ($data = array())
    {
        return serialize($data);
    }
    // Encode raw PHP
    private function _format_php ($data = array())
    {
        return var_export($data, TRUE);
    }
}