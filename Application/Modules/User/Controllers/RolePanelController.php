<?php
class RolePanelController extends XPHP_Controller
{
    public function _init()
    {
        $this->loadLayout('ControlPanel/XAdmin');
    }
    
    #[Authorize]
    public function indexAction()
    {
        //Lấy ra toàn bộ danh sách người dùng
        $dataSource = new XPHP_DataSource('Role');
        $dataSource->setData(false);
        $dataSource->pageSize = 10;
        $dataSource->columns = array('name', 
        							 'title', 
        							 'description',
                                     'id');
        $dataSource->bind();
        $this->view->dataSource = $dataSource;
        return $this->view();
    }
    
    #[KendoUI_Attribute_GridAction]
    #[Authorize]
    public function indexAjax()
    {
        $dataSource = new XPHP_DataSource('Role');
        $dataSource->columns = array('name', 
        							 'title', 
        							 'description',
                                     'id');
        return $dataSource;
    }
    
    #[Authorize]
    public function createAction()
    {
    	return $this->view(new Role());
    }
    
    #[Authorize]
    public function createPost(Role $model)
    {
        if ($model->validate()) {
        	//Kiem tra xem ten dang nhap da ton tai hay chua
        	if(!$model->hasName($model->name))
        	{	
	            if ($model->insert())
	                return $this->json(
	                    array('success' => true,
	                         'message' => 'Thêm nhóm mới thành công'));
	            else
	                return $this->json(
	                    array('success' => false,
	                         'message' => 'Xảy ra lỗi khi thêm nhóm mới'));
        	}
        	else
        		return $this->json(
        				array('success' => false,
        						'message' => "Tên nhóm {$model->name} đã tồn tại trong hệ thống."));
        } else
            return $this->json(
                array('success' => false,
                     'message' => 'Thông tin nhập vào chưa hợp lệ'));
    }
    
    #[Authorize]
    public function editAction()
    {
    	return $this->view(new Role($this->params[0]));
    }
    
    #[Authorize]
    public function editPost(Role $model)
    {
    	if ($model->validate()) {
    		if ($model->update())
    			return $this->json(
    					array('success' => true,
    							'message' => 'Cập nhật thông tin nhóm thành công'));
    		else
    			return $this->json(
    					array('success' => false,
    							'message' => 'Xảy ra lỗi khi cập nhật thông tin nhóm'));
    	} else
    		return $this->json(
    				array('success' => false,
    						'message' => 'Thông tin nhập vào chưa hợp lệ'));
    }
    
    #[Authorize]
    public function permissionAction()
    {
    	$id = $this->params[0];
    	//Lay ra thong tin nhom
    	$role = new Role($id);
    	//Lay ra toan bo danh sach cac permission
    	$permission = new Permission();
    	$permissionList = $permission->getPermissions();
    	//Lay ra danh sach permission cua role
    	$rolePermission = $permission->getPermissionOfRole($id);
    	//Kiem tra xem permission nao co trong danh sach
    	foreach ($permissionList as $k => $per)
    	{
    		$permissionList[$k]->active = false;
    		foreach ($rolePermission as $rp)
    		{
    			if($per->id == $rp->id)
    				$permissionList[$k]->active = true;
    		}
    	}
    	$this->view->permissions = $permissionList;
    	$this->view->role = $role;
    	return $this->view();
    }
    
    #[Authorize]
    public function permissionPost()
    {
    	$role_id = $this->params['id'];
    	//Xoa toan bo cac quyen cu cua nguoi dung di
    	$rolePermission = new RolePermission();
    	$rolePermission->deleteAllPermissionOfRole($role_id);
    	//Lay ra danh sach cac  post len
    	$select = $this->params['select'];
    	foreach($select as $perId => $val)
    	{
    		$per = new RolePermission();
    		$per->permission_id = $perId;
    		$per->role_id = $role_id;
    		$per->insert();
    	}
    }
    
    #[Authorize]
    public function deletePost(Role $model)
    {
    	if ($model->delete()) {
    		return $this->json(
    				array('success' => true, 'message' => 'Xóa nhóm thành công'));
    	} else
    		return $this->json(
    				array('success' => false,
    						'message' => 'Xảy ra lỗi khi cố xóa nhóm'));
    }
}