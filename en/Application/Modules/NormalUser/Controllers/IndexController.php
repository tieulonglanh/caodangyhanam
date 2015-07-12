<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of IndexController
 *
 * @author Long Nguyen
 */
class IndexController extends XPHP_Controller{
    
    public function loginAction() {
        if(isset($_POST['login'])){
            $username = $this->params['username'];
            $password = $this->params['password'];
            $normalUser = new NormalUser;
            if($normalUser->authorize($username, $password)){
                $_SESSION['username'] = $username;
                $_SESSION['fullname'] = $normalUser->_user->fullname;
                $_SESSION['role_id'] = $normalUser->_user->role_id;
            }else{
                $_SESSION['error'] = "Sai mật khẩu hoặc password";
            }
        }
        return $this->redirectUrl('/');
    }
    
    public function logoutAction() {
            unset($_SESSION['username']);
            unset($_SESSION['fullname']);
            unset($_SESSION['role_id']);
        return $this->redirectUrl('/');
    }
    
    public function registerAction() {
        $this->loadLayout('/default');
        if(isset($_POST['submit'])){
            $username = $this->params['username'];
            $fullname = $this->params['fullname'];
            $password = $this->params['password'];
            $repassword = $this->params['repassword'];
            $email = $this->params['email'];
            $gender = $this->params['gender'];
            if($password != $repassword) {
                echo '<script>alert("Password không khớp nhau, xin hãy đăng ký lại!"); window.location.href="/NormalUser/Index/register"</script>';
            }
            
            $model = new NormalUser();
            if (!$model->hasUsername($username)) {
                $data = array();
                $data['username'] = $username;
                $data['fullname'] = $fullname;
                $data['password'] = XPHP_String::md5Encode($password . $username);
                $data['email'] = $email;
                $data['status'] = 0;
                $data['role_id'] = 2;
                $data['gender'] = $gender;
                $data['register'] = date("Y-m-d h:i:s");
                
                $model->insert($data);
                echo "<script>alert('Bạn đã đăng ký thành công! Sau khi kiểm duyệt, chúng tôi sẽ thông báo cho bạn qua Email.'); window.location.href='/'</script>";
                
            }else{
                echo '<script>alert("Tài khoản đã tồn tại, xin hãy đăng ký lại!"); window.location.href="/NormalUser/Index/register";</script>';
            }
        }
        return $this->view();
    }
    
}

?>
