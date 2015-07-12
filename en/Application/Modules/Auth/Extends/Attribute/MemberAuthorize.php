<?php
require_once 'XPHP/Array.php';
require_once 'XPHP/Session.php';
/**
 * Lớp Attribute xác thực quyền Administrator
 * @author Mr.UBKey
 */
class Application_Modules_Auth_Extends_Attribute_MemberAuthorize  extends XPHP_Action_Attribute_Abstract
{
    private $_roles = array();
    
    private $_users = array();
    
	/**
     * Gán tham số truyền vào cho Attribute
     */
    public function init ($properties)
    {
        if(isset($properties['roles']))
        {
            $roles = explode('|', $properties['roles']);
            foreach ($roles as $role)
                $this->_roles[] = trim($role);
        }
        if(isset($properties['users']))
        {
            $users = explode('|', $properties['users']);
            foreach ($users as $user)
                $this->_users[] = trim($user);
        }
    }
    /**
     * Sự kiện này được thực thi khi trước khi action được thực thi
     */
    public function onActionExecute ()
    {
        //Url
        $url = new XPHP_Url($this->controller->getRouter());
        $currentUrl = $url->current();
        $encodeUrl = $url->encode($currentUrl);
    			
        //Session
        $session = XPHP_Session::getInstance();
        
        //Kiểm tra theo csdl
        if(isset($session->member) && isset($session->mrole) && isset($session->mpermissions))
        {
            $valid = false;
            //Kiểm tra trong csdl
            $router = $this->controller->getRouter();
            foreach ($session->mpermissions as $permission)
            {
                if($permission->module == "*")
                    $valid = true;
                else if($permission->module == $router->module)
                {
                    if($permission->controller == "*")
                        $valid = true;
                    else if($permission->controller == $router->controller)
                    {
                        if($permission->action == "*")
                            $valid = true;
                        else if($permission->action == $router->action)
                            $valid = true;
                    }
                }
            }
            //Nếu kiểm tra quyền trong csdl không hợp lệ
            if(! $valid)
                $url->redirectUrl($url->action("denied", "Member", "Auth", array($encodeUrl)));
        }
        else if(isset($session->member) && isset($session->mrole))
        {
            /* Kiểm tra theo attribute */
            
            $validRole = false;
            $validUser = false;
            
            //Lấy ra toàn bộ các roles của user hiện tại và kiểm tra
            $roles = $session->mrole;
            foreach ($roles as $role)
            {
                foreach ($this->_roles as $r)
                {
                    if($role->name == $r)
                        $validRole = true;
                }
            }
            
            //Lấy ra thông tin của user hiện tại
            $user = $session->member;
            foreach ($this->_users as $u)
            {
                if($u == $user->username)
                    $validUser = true;
            }
            
            if(! $validRole && ! $validUser)
            {                
    			$url->redirectUrl($url->action("denied", "Member", "Auth", array($encodeUrl)));
            }
        }
        else 
            $url->redirectUrl($url->action("login", "Member", "Auth", array($encodeUrl)));
    }
    
    static function __set_state(array $array)
    {
        $tmp = new self();
        foreach($array as $k => $v) {
            $tmp->$k = $v;
        }
        return $tmp;
    }
}