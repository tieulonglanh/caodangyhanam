<?php
class MemberController extends XPHP_Controller
{
    public function indexAction()
    {
        $this->loadLayout('/LNGuitar');
        return $this->view(new MemberLogin());
    }

    public function indexPost(MemberLogin $model)
    {
        //Redirect về url cũ
        if (isset($this->params[0]))
            $url = $this->url->decode($this->params[0]);
        else{
            $url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->url->action('index', 'Index', '');
        }

        if($model->validate())
        {
            $this->loadModuleExtends('XenForo');
            $xfUser = new XFUser();
            $error  = "";
            $ketqua = $xfUser->validateAuthentication($model->username, $model->password, $error);
            if(empty($error) && !empty($ketqua))
            {
                //Login thành công
                $xfUserInfo = $xfUser->getUser();
                //Kiểm tra xem có trong bảng user chưa? nếu chưa thì insert vào
                $user = new User();
                $xfCheck = $user->xfUserExists($xfUserInfo->user_id);
                if(!$xfCheck)
                {
                    $newUser = new User();
                    $newUser->xf_user_id = $xfUserInfo->user_id;
                    $newUser->username = $xfUserInfo->username;
                    $newUser->email = $xfUserInfo->email;
                    $newUser->status = 1;
                    $newUser->role_id = 2;
                    $id = $newUser->insert();
                    $xfCheck = $newUser->getUserById($id);
                }
                $this->session->member = $xfUserInfo;
                $this->session->memberInfo = $xfCheck;

                //Login xen
                $xfLogin = new XFLogin();
                $xfLogin->login($model->username, $model->password);

                return $this->json(array('success' => true, 'url' => $url));
            }
            else
                return $this->json(array('success' => false, 'url' => $url, 'message' => $error));
        }
        else
            return $this->json(array('success' => false, 'url' => $url, 'message' => 'Thông tin nhập vào chưa chính xác'));
    }

    public function loginAction()
    {
        return $this->view(new MemberLogin());
    }

    public function loginPost(MemberLogin $model)
    {
        $url = isset($this->params['url']) ? $this->params['url'] : (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/');

        if($model->validate())
        {
            $this->loadModuleExtends('XenForo');
            $xfUser = new XFUser();
            $error  = "";
            $ketqua = $xfUser->validateAuthentication($model->username, $model->password, $error);
            if(empty($error) && !empty($ketqua))
            {
                //Login thành công
                $xfUserInfo = $xfUser->getUser();
                //Kiểm tra xem có trong bảng user chưa? nếu chưa thì insert vào
                $user = new User();
                $xfCheck = $user->xfUserExists($xfUserInfo->user_id);
                if(!$xfCheck)
                {
                    $newUser = new User();
                    $newUser->xf_user_id = $xfUserInfo->user_id;
                    $newUser->username = $xfUserInfo->username;
                    $newUser->email = $xfUserInfo->email;
                    $newUser->status = 1;
                    $newUser->role_id = 2;
                    $id = $newUser->insert();
                    $xfCheck = $newUser->getUserById($id);
                }
                $this->session->member = $xfUserInfo;
                $this->session->memberInfo = $xfCheck;

                //Login xen
                $xfLogin = new XFLogin();
                $xfLogin->login($model->username, $model->password);
            }
            else
                echo "<script type='text/javascript'>$(function(){ alert('{$error}'); });</script>";
        }
        else
            echo "<script type='text/javascript'>$(function(){ alert('Thông tin nhập vào chưa chính xác'); });</script>";

        return $this->view();
    }

    public function logoutAction()
    {
        $url = isset($this->params['url']) ? $this->params['url'] : (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/');
        unset($this->session->member);
        unset($this->session->memberInfo);
        $this->loadModuleExtends('XenForo');
        $xfLogin = new XFLogin();
        $xfLogin->logout();
        return $this->redirectUrl($url);
    }

    public function registerAction()
    {
        //Redirect về url cũ
        if (isset($this->params[0]))
            $url = $this->url->decode($this->params[0]);
        else{
            if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != XPHP_Url::getCurrentUrl())
                $url = $_SERVER['HTTP_REFERER'];
            else
                $url = $this->url->action('index', 'Index', '');
        }
        $this->view->url = $url;

        $this->loadLayout('/LNGuitar');
        return $this->view(new MemberRegister());
    }

    public function registerPost(MemberRegister $model)
    {
        $url = $this->params['url'];

        if(isset($this->params['agree']))
        {
            if (empty($_SESSION['captcha']) || trim(strtolower($_REQUEST['captcha'])) != $_SESSION['captcha'])
            {
                return $this->json(array('success' => false, 'message' => 'Mã xác nhận không chính xác'));
            }
            else
            {
                if($model->validate())
                {
                    if(preg_match('/^[a-zA-Z0-9_]+$/', $model->register_username))
                    {
                        $this->loadModuleExtends('XenForo');

                        //XenForo User
                        $xfUser = new XFUser();

                        //Check username
                        if($xfUser->getUserByName($model->register_username) === false)
                        {
                            if($xfUser->getUserByEmail($model->register_email) === false)
                            {
                                $xfUser->username = $model->register_username;
                                $xfUser->email = $model->register_email;
                                $xfUser->register_date = time();
                                $xfUser->last_activity = time();
                                $xf_user_id = $xfUser->insert();

                                $xfUserInfo = $xfUser->getUserByName($model->register_username);

                                //Auth
                                $xfAuth = new XFAuth();
                                $xfAuth->user_id = $xf_user_id;
                                $xfAuth->password = $model->register_password;
                                $xfAuth->remember_key = '536246ce7ed18dd712605996fb96abc4dedc9dd0';
                                $xfAuth->insert();

                                //User
                                $user = new User();
                                $user->xf_user_id = $xf_user_id;
                                $user->username = $xfUserInfo->username;
                                $user->email = $xfUserInfo->email;
                                $user->role_id = 2;
                                $user->status = 1;
                                $user->fullname = $model->register_fullname;
                                $user->phone = $model->register_phone;
                                $user_id = $user->insert();

                                $xfCheck = $user->getUserById($user_id);

                                //Lưu vào session
                                $this->session->member = $xfUserInfo;
                                $this->session->memberInfo = $xfCheck;

                                //Login xen
                                $xfLogin = new XFLogin();
                                $xfLogin->login($model->register_username, $model->register_password);

                                return $this->json(array('success' => true, 'url' => $url));
                            }
                            else
                                return $this->json(array('success' => false, 'message' => 'Email này đã đuợc sử dụng'));
                        }
                        else
                            return $this->json(array('success' => false, 'message' => 'Tên đăng nhập này đã đuợc sử dụng'));
                    }
                    else
                        return $this->json(array('success' => false, 'message' => 'Tên đăng nhập chỉ được dùng chữ và số'));
                }
                else
                {
                    $err = $model->getErrorMessage();
                    $err = ksort($err);
                    if(isset($err[0]))
                        return $this->json(array('success' => false, 'message' => $err[0]));
                    else
                        return $this->json(array('success' => false, 'message' => 'Thông tin bạn nhập vào chưa chính xác'));
                }
            }
        }
        else
            return $this->json(array('success' => false, 'message' => 'Bạn hãy đồng ý với các điều khoản của chúng tôi'));
    }
}