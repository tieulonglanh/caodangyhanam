<?php
class ControlPanelController extends XPHP_Controller
{
    public function indexAction()
    {
        //$permission = new Permission();
        //$permissionList = $permission->getPermissionOfRole(4);
        //var_dump($permissionList);
        //return $this->redirect("login");
        //$user->role_id = 1;
        //var_dump($this->_getUserRole($user));
        if(isset($this->session->user))
            return $this->redirect('index', "ControlPanel", "Dashboard");
        else
            return $this->redirect('login');
    }
    
    public function loginAction()
    {
        $this->model = new ControlPanelLogin();
        /*
		 * Kiểm tra xem có cookie được lưu không?
		 * Nếu có thì thử đăng nhập với tài khoản được lưu trong cookie
		 * Đăng nhập thành công redirect tới url cũ 
		 * Nếu không hiển thị
		 */
        if (isset($this->cookie->username) && isset($this->cookie->password)) {
            $this->model->username = $this->cookie->username;
            $this->model->password = $this->cookie->password;
            if ($this->model->authorize()) {
                //Thông tin của user
                $user = $this->model->getAuthorizeUser();
                //Kiểm tra xem roles có hợp hệ để truy cập hay không
                if ($permissions = $this->_permissionAuthValidate($user)) {
                    //Gán session user và role
                    $this->session->user = $user;
                    $this->session->role = $this->_getUserRole($user);
                    $this->session->permissions = $permissions;
                    //Redirect về url cũ
                    if (isset($this->params[0]))
                    {
                        $url = $this->url->decode($this->params[0]);
                    }
                    else{                        
                            $url = $this->url->action("index");
                    }
                }
                else 
                    $url = $this->url->action("denied");
                //Redirect
                return $this->redirectUrl($url);
            }
        }
        return $this->view();
    }

    public function loginPost(ControlPanelLogin $model)
    {
        if ($this->model->validate()) {
            if ($this->model->authorize()) {
                //Thông tin của user
                $user = $this->model->getAuthorizeUser();
                //Kiểm tra xem roles có hợp hệ để truy cập hay không
                if ($permissions = $this->_permissionAuthValidate($user)) {
                    //Gán session user và role
                    $this->session->user = $user;
                    $this->session->role = $this->_getUserRole($user);
                    $this->session->permissions = $permissions;
                    if(isset($this->params['remember']))
                    {
                        //Gán cookie username, password
                        $this->cookie->username = $this->model->username;
                        $this->cookie->password = $this->model->password;
                    }
                    //Redirect về url cũ
                    if (isset($this->params[0]))
                        $url = $this->url->decode($this->params[0]);
                    else{
                            $url = $this->url->action("index");
                    }
                    return $this->json(
                        array("success" => true, "url" => $url));
                } else
                    return $this->json(
                        array("success" => false,
                             "message" => "Tài khoản không có quyền truy cập!"));
            } else {
                return $this->json(
                    array("success" => false,
                         "message" => "Tên đăng nhập hoặc mật khẩu không chính xác!"));
            }
        } else {
            return $this->json(
                array("success" => false,
                     "message" => "Dữ liệu nhập vào không đúng!"));
        }
    }

    public function logoutAction()
    {
        unset($this->session->user);
        unset($this->session->role);
        unset($this->session->permissions);
        unset($this->cookie->username);
        unset($this->cookie->password);
        return $this->redirectUrl("/ControlPanel/login");
    }

    public function deniedAction()
    {
        return $this->view();
    }

    /**
     * Kiểm tra xem user có quyền đăng nhập vào vùng quản trị không
     * Nếu có trả về danh sách các quyền của user
     * @param object $user
     */
    private function _permissionAuthValidate($user)
    {
        //Lấy ra toàn bộ quyền của user hiện tại
        $permission = new Permission();
        $permissionList = $permission->getPermissionOfRole($user->role_id);
        foreach ($permissionList as $per)
        {
            if($per->module == "*")
                return $permissionList;
            if($per->module == 'Auth' && ($per->controller == 'ControlPanel' || $per->controller == '*'))
                return $permissionList;
        }
        
        return false;
    }
    
    /**
     * Lấy ra thông tin role của người dùng
     * @param object $user
     * @return Role
     */
    private function _getUserRole($user)
    {
        $role = new Role();
        return $role->getRole($user->role_id);
    }
    
}