<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Mr.UBKey
 * Date: 1/9/13
 * Time: 10:36 AM
 * To change this template use File | Settings | File Templates.
 */
class XFLogin
{
    public $url;

    public function __construct()
    {
        $xenforo = XPHP_Config::get('xenforo');
        $this->url = $xenforo->url;
    }

    public function login($username, $password)
    {
        $url = $this->url . '/login/login';
        $data = array('login'       => $username,
                      'password'    => $password,
                      'register'    => 0);
        //Sử dụng curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($ch);
        curl_close($ch);

        preg_match_all('#Set-Cookie: (.*);#U', $result, $matches);
        $cookies = array();
        foreach($matches[1] as $m)
        {
            $arr = explode('=', $m);
            if(count($arr) == 2)
                $cookies[$arr[0]] = $arr[1];
        }

        //Gán cookie
        $cookie = new XPHP_Cookie();
        foreach($cookies as $k => $v)
        {
            $cookie->$k = $v;
        }
    }

    public function logout()
    {
        $cookie = new XPHP_Cookie();
        unset($cookie->xf_session);
    }
}
