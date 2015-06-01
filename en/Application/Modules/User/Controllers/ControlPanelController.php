<?php

class ControlPanelController extends XPHP_Controller {

    public function _init() {
        $this->loadLayout('ControlPanel/XAdmin');
    }

    #[Authorize]
    public function indexAction() {
        //Lấy ra toàn bộ danh sách người dùng
        return $this->view();
    }

    #[Authorize]
    #[DataTable]
    public function indexAjax() {
        $user = new User();        
        $dataSource = new XPHP_DataSource($user, array('id',
                                                       'avatar',
                                                       'status',
                                                       'username',
                                                       'role_id',
                                                       'register'));
        $dataSource->where('role_id', 4);
        return $dataSource;
    }

    #[Authorize]
    public function createAction() {
        //Mag option dua vao selectFor
        $roleOptions = array();
        //Lay ra danh sach cac role
        $role = new Role();
        $roles = $role->getRoles();
        foreach ($roles as $r) {
            $roleOptions[$r->id] = $r->title;
        }
        $this->view->roleOptions = $roleOptions;

        return $this->view(new User());
    }
    #[Authorize]
    public function createPost(User $model) {
        if ($model->validate()) {
            //Kiem tra xem ten dang nhap da ton tai hay chua
            if (!$model->hasUsername($model->username)) {
                $model->register = date("Y-m-d H:i:s");
                if ($model->status)
                $model->status = 1;
                else
                    $model->status = 0;

                if ($model->gender)
                    $model->gender = 1;
                else
                $model->gender = 0;
                
                $model->password = XPHP_String::md5Encode($model->password.$model->username);
                if ($model->insert())
                    return $this->json(
                                    array('success' => true,
                                        'message' => 'Thêm người dùng mới thành công'));
                else
                    return $this->json(
                                    array('success' => false,
                                        'message' => 'Xảy ra lỗi khi thêm người dùng mới'));
            }
            else
                return $this->json(
                                array('success' => false,
                                    'message' => "Tên đăng nhập {$model->username} đã tồn tại trong hệ thống."));
        } else
            return $this->json(
                            array('success' => false,
                                'message' => 'Thông tin nhập vào chưa hợp lệ'));
    }

    #[Authorize]
    public function editAction() {
        //Mag option dua vao selectFor
        $roleOptions = array();
        //Lay ra danh sach cac role
        $role = new Role();
        $roles = $role->getRoles();
        foreach ($roles as $r) {
            $roleOptions[$r->id] = $r->title;
        }
        
        $this->view->roleOptions = $roleOptions;
        //$this->view->status = $status;
        $id = $this->params[0];
        $model = new User($id);
        $model->password = "";
        return $this->view($model);
    }
    #[Authorize]
    public function editPost(User $model) {
        if($model->role_id==4)
            $url = $this->params['saveType'] == '1' ? $this->url->action('index') : NULL;
        elseif($model->role_id==3)
            $url = $this->params['saveType'] == '1' ? $this->url->action('xfList') : NULL;
        else
            $url = $this->url->action('index', 'ControlPanel', 'Dashboard');
        if ($model->validate()) {
            $model->lastupdate = date("Y-m-d H:i:s");
            
            if ($model->status)
                $model->status = 1;
            else
                $model->status = 0;
            
            if ($model->good_student)
                $model->good_student = 1;
            else
                $model->good_student = 0;
            
            if ($model->gender)
                $model->gender = 1;
            else
                $model->gender = 0;
            //Lấy ra mật khẩu cũ
            $user = new User($model->id);
            if (empty($model->password))
                $model->password = $user->password;
            else
                $model->password = XPHP_String::md5Encode($model->password.$model->username);
            //Su dung username cu
            $model->username = $user->username;
            if ($model->update())
                return $this->json(
                                array('success' => true,
                                    'message' => 'Cập nhật thông tin người dùng mới thành công',
                                    'url'=>$url));
            else
                return $this->json(
                                array('success' => false,
                                    'message' => 'Xảy ra lỗi khi cập nhật thông tin người dùng mới'));
        } else
            return $this->json(
                            array('success' => false,
                                'message' => 'Thông tin nhập vào chưa hợp lệ'));
    }

    #[Authorize]
    public function hiddenPost(User $model) {
        $userModel = new User($model->id);
        if($userModel->status==1)
            $userModel->status = NULL;
        else
            $userModel->status = 1;
        if ($userModel->update()) {
            return $this->json(
                            array('success' => true, 'message' => 'Ẩn người dùng thành công'));
        } else
            return $this->json(
                            array('success' => false,
                                'message' => 'Xảy ra lỗi khi cố Ẩn người dùng'));
    }
    
    #[Authorize]
    public function deletePost(User $model)
    {
        if($model->delete())
            return $this->json(
                array('success' => true,
                      'message' => 'Xóa người dùng thành công'));
        else
            return $this->json(
                array('success' => false,
                      'message' => 'Xảy ra lỗi khi xóa người dùng'));
    }

    #[Authorize]
    public function lockPost(User $model) {
        $userModel = new User($model->id);
        if ($userModel->status != 2)
            $userModel->status = 2;
        else
            $userModel->status = 0;

        if ($userModel->update()) {
            return $this->json(
                            array('success' => true, 'message' => 'Khóa người dùng thành công'));
        } else
            return $this->json(
                            array('success' => false,
                                'message' => 'Xảy ra lỗi khi cố khóa người dùng'));
    }

    /**
     * Lấy ra tên role
     * @param string $roleId
     * @return string
     */
    public static function getRoleName($roleId) {
        $role = new Role($roleId);
        return $role->name;
    }

    #[Authorize]
    public function viewPost() {
        //Ngừng load layout
        $this->unloadLayout();
        //lấy file defined
        require_once $this->router->getModulePath('User') . '/defined.php';

        //Khởi tạo các model
        $packageModel = new Package();
        $roleModel = new Role();
        $model = new User();

        //Lấy các quyền và các package
        $roles = $roleModel->getRoles();
        $package = $packageModel->getPackages();

        //Lấy dữ liệu theo id
        $data = $model->db->where('id', $this->params['id'])->get()->result();

        //Gán các biến ra view
        $this->view->provinceOptions = $province;
        $this->view->status = $status;
        $this->view->package = $package;
        $this->view->roles = $roles;
        $this->view->data = $data[0];

        return $this->view();
    }

    #[Authorize]
    public function backgroundPost() {
        $this->unloadLayout();
        require_once $this->router->getModulePath('User') . '/defined.php';

        $model = new Background();
        $data = $model->db->where('user_id', $this->params['id'])->get()->result();

        $this->view->ethnic = $ethnic;
        $this->view->religious = $religious;
        $this->view->education = $education;
        $this->view->data = $data[0];

        return $this->view();
    }

    #[Authorize]
    public function datePost() {
        $this->unloadLayout();
        require_once $this->router->getModulePath('User') . '/defined.php';

        $model = new Date();
        $data = $model->db->where('user_id', $this->params['id'])->get()->result();

        $this->view->data = $data[0];

        return $this->view();
    }

    #[Authorize]
    public function interestPost() {
        $this->unloadLayout();
        require_once $this->router->getModulePath('User') . '/defined.php';

        $model = new Interest();
        $data = $model->db->where('user_id', $this->params['id'])->get()->result();

        $this->view->data = $data[0];
        return $this->view();
    }

    #[Authorize]
    public function lifePost() {
        $this->unloadLayout();
        require_once $this->router->getModulePath('User') . '/defined.php';

        $model = new UserLife();

        $data = $model->db->where('user_id', $this->params['id'])->get()->result();

        $this->view->exercise = $exercise;
        $this->view->smoke = $smoke;
        $this->view->drink = $drink;
        $this->view->occupation = $occupation;
        $this->view->income = $income;
        $this->view->children = $children;
        $this->view->wantchildren = $wantchildren;
        $this->view->data = $data[0];

        return $this->view();
    }

    #[Authorize]
    public function profileAction() {
        $this->unloadLayout();
        require_once $this->router->getModulePath('User') . '/defined.php';

        $model = new UserProfile();
        $data = $model->db->where('user_id', $this->params['id'])->get()->result();

        $this->view->relationship = $relationship;
        $this->view->body = $body_type;
        $this->view->hair = $hair;
        $this->view->tall = $height;
        $this->view->eyes = $eyes;
        $this->view->feature = $feature;
        $this->view->sign = $sign;
        $this->view->data = $data[0];

        return $this->view();
    }

    #[Authorize]
    public function imageAction() {
        $this->unloadLayout();

        $model = new User();
        $data = $model->db->where('id', $this->params['id'])->get()->result();

        $this->view->data = $data[0];
        return $this->view();
    }

    #[Authorize]
    public function imageAllowAction() {
        $this->unloadLayout();

        $model = new User();
        $model->get($this->params['id']);

        if ($model->approval == 1) {
            $model->approval = 0;

            if ($model->update())
                $this->view->message = 'Không cho phép hiển thị avatar thành công';
            else
                $this->view->message = 'Không cho phép hiển thị avatar lỗi';
        }else {
            $model->approval = 1;

            if ($model->update())
                $this->view->message = 'Cho phép hiển thị avatar thành công';
            else
                $this->view->message = 'Cho phép hiển thị avatar lỗi';
        }
        return $this->view();
    }
    
    #[Authorize]
    public function imageRemoveAction()
    {
        $this->unloadLayout();

        $model = new User();
        $model->get($this->params['id']);
        
        $model->approval = 0;
        $model->avatar = null;
        
        if ($model->update())
                $this->view->message = 'Xóa avatar thành công';
        else
                $this->view->message = 'Xóa avatar lỗi';
            
        return $this->view();
    }

    #[Authorize]
    public function xfListAction()
    {
        $user = new User();
        $teachers = $user->getTeachers();
        $this->view->teachers = $teachers;

        return $this->view();
    }

    #[Authorize]
    #[DataTable]
    public function xfListAjax()
    {
        $this->loadModuleExtends('XenForo');
        $xfUser = new XFUser();
        $dataSource = new XPHP_DataSource($xfUser,  array('user_id',
                                                          'username',
                                                          'email',
                                                          'register_date'));
        $dataSource->order_by('register_date', 'desc'); //Mặc định sắp xếp theo mới nhất

        return $dataSource;
    }

    #[Authorize]
    public function setTeacherAction()
    {
        $this->loadModuleExtends('XenForo');
        $id = $this->params['id'];
        $username = $this->params['username'];
        $xfUser = new XFUser();
        $xfu = $xfUser->getUserByNameOrEmail($username);
        if($xfu)
        {
            //Kiểm tra xem trong user đã có hay chưa ?
            $user = new User();
            $u = $user->xfUserExists($xfu->user_id);
            if($u)
            {
                if($user->updateRoleForUser($u->id, 3))
                {
                    return $this->json(array('success' => true, 'message' => 'Cập nhật phân quyền giáo viên thành công',
                                             'url' => $this->url->action('edit', array($u->id))));
                }
                else
                    return $this->json(array('success' => false, 'message' => 'Xảy ra lỗi khi cập nhật role'));
            }
            else
            {
                $user = new User();
                $user->xf_user_id = $xfu->user_id;
                $user->username = $xfu->username;
                $user->email = $xfu->email;
                $user->role_id = 3;
                $u_id = $user->insert();
                if($u_id)
                {
                    return $this->json(array('success' => true, 'message' => 'Thêm quyền giáo viên thành công',
                                             'url' => $this->url->action('edit', array($u_id))));
                }
                else
                    return $this->json(array('success' => false, 'message' => 'Xảy ra lỗi khi thêm quyền giáo viên'));
            }
        }
        else
            return $this->json(array('success' => false, 'message' => "Không tìm thấy tài khoản {$username}"));
    }

    public function editTeacherAction()
    {
        $id = $this->params[0];
        //Lấy ra thông tin sau đấy redirect về trang sửa
        $user=  new User();
        $u = $user->xfUserExists($id);
        if($u)
            return $this->redirect('edit', array($u->id));
        else
            return $this->redirect('xfList');
    }
}