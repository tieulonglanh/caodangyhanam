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
 * @category    XPHP
 * @package        XPHP_Rest
 * @author        http://www.gen-x-design.com/archives/create-a-rest-api-with-php/
 * @copyright    Copyright (c) 2010-2011 XWEB. (http://xweb.vn)
 * @license        http://xphp.xweb.vn/license.html     GNU GPL License
 * @version        $Id: Utils.php 201110 2011-12-10 23:44:09 Mr.UBKey $
 */
/**
 * XPHP_Rest_Request
 *
 * @see XPHP_Rest_Request
 */
require_once 'XPHP/Rest/Request.php';
/**
 * XPHP_Rest_Utils Hỗ trợ giao tiếp giữa Service và Client
 *
 * @category    XPHP
 * @package        XPHP_Rest
 * @author        http://www.gen-x-design.com/archives/create-a-rest-api-with-php/
 * @link        http://xphp.xweb.vn/user_guide/xphp_rest_utils.html
 */
class XPHP_Rest_Utils
{
    /**
     * Xử lý yêu cầu
     * @return XPHP_Rest_Request
     */
    public static function processRequest()
    {
        // get our verb
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);
        $return_obj = new XPHP_Rest_Request();
        // we'll store our data here
        $data = array();

        switch ($request_method)
        {
            // gets are easy...
            case 'get':
                $data = $_GET;
                break;
            // so are posts
            case 'post':
                $data = $_POST;
                break;
            // here's the tricky bit...
            case 'put':
                // basically, we read a string from PHP's special input location,
                // and then parse it out into an array via parse_str... per the PHP docs:
                // Parses str  as if it were the query string passed via a URL and sets
                // variables in the current scope.
                parse_str(file_get_contents('php://input'), $put_vars);
                $data = $put_vars;
                break;
        }

        // store the method
        $return_obj->setMethod($request_method);

        // set the raw data, so we can access it if needed (there may be
        // other pieces to your requests)
        $return_obj->setRequestVars($data);

        if (isset($data['data'])) {
            // translate the JSON to an Object for use however you want
            $return_obj->setData(json_decode($data['data']));
        }
        return $return_obj;
    }

    /**
     * Đáp ứng yêu cầu
     * @param int $status
     * @param string $body
     * @param string $content_type
     */
    public static function sendResponse($status = 200, $body = '', $content_type = 'text/html')
    {
        $status_header = 'HTTP/1.1 ' . $status . ' ' . XPHP_Rest_Utils::getStatusCodeMessage($status);
        // set the status
        header($status_header);
        // set the content type
        header('Content-type: ' . $content_type);
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        header('Access-Control-Max-Age: 1000');
        if (array_key_exists('HTTP_ACCESS_CONTROL_REQUEST_HEADERS', $_SERVER)) {
            header('Access-Control-Allow-Headers: '
                   . $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']);
        } else {
            header('Access-Control-Allow-Headers: *');
        }
        if ("OPTIONS" == $_SERVER['REQUEST_METHOD']) {
            exit(0);
        }
        // pages with body are easy
        if ($body != '') {
            // send the body
            echo $body;
            exit;
        }
            // we need to create the body if none is passed
        else
        {
            // create some body messages
            $message = '';

            // this is purely optional, but makes the pages a little nicer to read
            // for your users.  Since you won't likely send a lot of different status codes,
            // this also shouldn't be too ponderous to maintain
            switch ($status)
            {
                case 401:
                    $message = 'You must be authorized to view this page.';
                    break;
                case 404:
                    $message = 'The requested URL ' . $_SERVER['REQUEST_URI'] . ' was not found.';
                    break;
                case 500:
                    $message = 'The server encountered an error processing your request.';
                    break;
                case 501:
                    $message = 'The requested method is not implemented.';
                    break;
            }

            // servers don't always have a signature turned on (this is an apache directive "ServerSignature On")
            $signature = ($_SERVER['SERVER_SIGNATURE'] == '')
                    ? $_SERVER['SERVER_SOFTWARE'] . ' Server at ' . $_SERVER['SERVER_NAME'] . ' Port ' . $_SERVER['SERVER_PORT']
                    : $_SERVER['SERVER_SIGNATURE'];

            // this should be templatized in a real-world solution
            $body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
						<html>
							<head>
								<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
								<title>' . $status . ' ' . XPHP_Rest_Utils::getStatusCodeMessage($status) . '</title>
							</head>
							<body>
								<h1>' . XPHP_Rest_Utils::getStatusCodeMessage($status) . '</h1>
								<p>' . $message . '</p>
								<hr />
								<address>' . $signature . '</address>
							</body>
						</html>';

            echo $body;
            exit;
        }
    }

    /**
     * Lấy ra thông báo từ status code
     * @param int $status
     */
    public static function getStatusCodeMessage($status)
    {
        // these could be stored in a .ini file and loaded
        // via parse_ini_file()... however, this will suffice
        // for an example
        $codes = Array(
            100 => 'Continue',
            101 => 'Switching Protocols',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => '(Unused)',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported'
        );

        return (isset($codes[$status])) ? $codes[$status] : '';
    }
}