<?php
#[Table('user')]
#[PrimaryKey('id')]
class User extends XPHP_Model
{
    public $id;

    public $xf_user_id;
    
    #[Label('Họ và tên')]
    public $fullname;

    #[Label('Tên')]
    //public $lastname;
    
    #[Label('Đăng nhập')]
    #[Required(message = 'Đăng nhập là bắt buộc nhập')]
    #[MaxLength(300, message = 'Đăng nhập có tối đa 300 kí tự')]
    public $username;
    
    #[Label('Mật khẩu')]
    #[MaxLength(300, message = 'Mật khẩu có tối đa 300 kí tự')]
    public $password;
    
    #[Label('Email')]
    #[Required(message = 'Email là bắt buộc nhập')]
    #[Email(message = 'Email chưa đúng định dạng')]
    public $email;
    
    #[Label('Nam')]
    public $gender;
        
    #[Label('Ngày sinh')]
    #[Required(message = 'Ngày sinh bắt buộc nhập')]
    #[Type('date')]
    public $birthdate;
    #[Label('SĐT người dùng')]
    public $phone;
    
    #[Label('Yahoo')]
    public $yahoo;
   #[Label('Ảnh đại diện')]
    public $avatar;
    #[Label('Nội dung')]
    public $content;
    #[Label('Active')]
    public $status;
    
    public $register;
        
    public $lastupdate;
    
    public $images;
        
    #[Label('Nhóm')]
    #[Join(table = 'role')] <=> (table = 'role', field = 'id', relation = 'one-one')
    public $role_id;
    
    #[Label('Học viên tiêu biểu')]
    public $good_student;

	/**
	 * Kiem tra xem da ton tai user nay trong he thong hay chua
	 * @param string $username
	 */
	public function hasUsername($username)
	{
		$result = $this->db->where('username', $username)
						   ->get()
						   ->result();
		return isset($result[0]);
	}
	
	/**
	 * Cập nhật avatar của người dùng
	 * @param string $url
	 */
	public function updateAvatar($url, $approval = 0)
	{
		return $this->db->set('avatar', $url)
						->set('approval', $approval)
					    ->where('id', $this->id)
					    ->update();
	}
	
	/**
	 * Cập nhật vị trí của người dùng
	 * @param int $user_id
	 */
	public function updateLocation($user_id = NULL)
	{
		if($user_id === NULL)
			$this->db->where('id', $this->id);
		else
			$this->db->where('id', $user_id);
		return $this->db->set('region', $this->region)
				 		->set('province', $this->province)
				 		->set('district', $this->district)
				 		->set('within', $this->within)
				 		->update();
	}
	
	/**
	 * @param unknown_type $username
	 * @return User
	 */
	public function findByUsername($username=NULL)
	{
		if($username === NULL)
			$this->db->where('username', $this->username);
		else
			$this->db->where('username', $username);
		$result = $this->db->where('status IS NOT NULL')
		                   ->get()
						   ->result();
		if(isset($result[0]))
		{
			$vars = get_object_vars($result[0]);
			foreach ($vars as $o => $v)
			{
				$this->$o = $v;
			}
		}
		return $this;
	}
	
	/**
	 * @param int $username
	 * @param bool $info Lấy đầy đủ thông tin
	 * @return User
	 */
	public function getUserById($id, $info=false)
	{
	    $this->db->where('user.id', $id);
	    //Lay day du thong tin
	    if($info)
	    {
	        $this->db->join('user_date', 'user.id = user_date.user_id')
                     ->join('user_life','user.id=user_life.user_id')
                     ->join('user_bg', 'user.id=user_bg.user_id')
	                 ->join('user_profile', 'user.id = user_profile.user_id')
	                 ->select('user_date.gender as seeking_gender, user_date.agemin as seeking_agemin, user_date.agemax as seeking_agemax')
	                 ->select('user_profile.headline as headline, user_profile.description as description, user_profile.relationship as relationship, user_profile.body as body, user_profile.tall as height')
                     ->select('user_bg.ethnic as ethnic, user_bg.religious as religious')
                     ->select('user_life.children as children, user_life.occupation as occupation, user_life.wantchildren as wantchildren, user_life.smoke as smoke, user_life.drink as drink');
	    }
	    $result = $this->db->select('user.*')
	                       ->get()
	                       ->result();
	    return isset($result[0]) ? $result[0] : false;
	}
        
        public function getUsers($info=false)
        {
            if($info)
	    {
	        $this->db->join('user_date', 'user.id = user_date.user_id')
                     ->join('daily5_dislike', 'user.id = daily5_dislike.user_id','left')
	                 ->select('user_date.gender as seeking_gender, user_date.agemin as seeking_agemin, user_date.agemax as seeking_agemax, user_date.*')
	                 ->select('daily5_dislike.no_interested as dislike');
	    }
	    $result = $this->db->select('user.*')
	                       ->get()
	                       ->result();
	    return isset($result) ? $result : false;
        }
        
        public function getMatchDetailByUserId($id, $info=true)
        {
            $this->db->where('user.id', $id);
    	    //Lay day du thong tin
    	    if($info)
    	    {
    	        $this->db->join('user_date', 'user.id = user_date.user_id')
    	                 ->join('user_profile', 'user.id = user_profile.user_id')
                         ->join('user_bg', 'user.id=user_bg.user_id')
                         ->join('user_life', 'user.id=user_life.user_id')
                         ->join('user_interest', 'user.id=user_interest.user_id')
                         ->select('user_interest.sports as sports')
                         ->select('user_life.smoke as smoke, user_life.drink as drink, user_life.occupation as occupation, user_life.income as income, 
                                   user_life.wantchildren as wantkid, user_life.children as children, user_life.eating as eating, user_life.exercise as exercise, user_life.pets as pets')
                         ->select('user_bg.ethnic as ethnic, user_bg.outlook as outlook, user_bg.religious as religious, user_bg.speak as language, user_bg.edu as education')
    	                 ->select('user_date.gender as seeking_gender, user_date.agemin as seeking_agemin, user_date.agemax as seeking_agemax, user_date.heightmin as heightmin, user_date.heightmax as heightmax')
    	                 ->select('user_profile.headline as headline, user_profile.eyes as eyes, user_profile.hair as hair, user_profile.tall as height, 
                            user_profile.body as body, user_profile.relationship as relationship');
    	    }
    	    $result = $this->db->select('user.*')
    	                       ->get()
    	                       ->result();
    	    return isset($result[0]) ? $result[0] : false;            
        }
        
        public function getCompareUsers($userInfo, $userArray)
        {
            $this->db->where('user.province', $userInfo->province);
            
            if($userInfo->seeking_agemin!=""&&$userInfo->seeking_agemax!="")
            {
                $yearmin = date('Y') - $userInfo->seeking_agemax;
                $yearmax = date('Y') - $userInfo->seeking_agemin;
                $this->db->where("user.birthdate BETWEEN '{$yearmin}-01-01' AND '{$yearmax}-12-30'");
            }else{
                $age = (int)date('Y') - (int)date('Y', strtotime($userInfo->birthdate));
                $agemin = $age - 3;
                $agemax = $age + 3;
                $yearmin = date('Y') - $agemax;
                $yearmax = date('Y') - $agemin;
                $this->db->where("user.birthdate BETWEEN '{$yearmin}-01-01' AND '{$yearmax}-12-30'");
            }
                    
            $this->db->where("user.gender", $userInfo->seeking_gender);
            //$result = $this->db->get()->result();
            if(is_array($userArray))
            {
                $this->db->where_not_in('user.id',$userArray);
            }
            $this->db->join('user_profile', 'user.id = user_profile.user_id')
                    ->join('user_life', 'user.id = user_life.user_id')
                    ->join('user_bg', 'user.id = user_bg.user_id')
                    ->where('dt_user.status IS NOT NULL')
                    ->where("(dt_user.avatar IS NOT NULL AND dt_user.avatar != '' AND dt_user.approval IS NOT NULL AND dt_user.approval = 1)")
                    ->select('user_bg.ethnic as background, user_bg.religious as religious, user_bg.edu as education, user_bg.speak as speak')
                    ->select('user.id, user.username, user.avatar, user.birthdate, user.province')
                    ->select('user_profile.body as body, user_profile.tall as height, user_profile.eyes as eyes, user_profile.hair as hair, user_profile.relationship as relationship')
                    ->select('user_life.drink as drink, user_life.occupation as occupation, user_life.income as income, user_life.smoke as smoke, user_life.children as children, user_life.wantchildren as wantchildren');
            $result = $this->db->get()
                               ->result();
            return $result;
        }
        
    public function getNewestUser($dayago=7, $info=false, $limit=false, $offset=false)
    {
        //Limit
        if($limit)
        {
            if($offset)
                $this->db->limit($limit, $offset);
            else 
                $this->db->limit($limit);
        }
        
        //Gioi han thoi gian tim kiem
        $this->db->where("DATEDIFF(CURDATE(), dt_user.register) <= {$dayago}");
        
	    //Lay day du thong tin
	    if($info)
	    {
	        $this->db->join('user_date', 'user.id = user_date.user_id')
                     ->join('user_life','user.id=user_life.user_id')
                     ->join('user_bg', 'user.id=user_bg.user_id')
	                 ->join('user_profile', 'user.id = user_profile.user_id')
	                 ->select('user_date.gender as seeking_gender, user_date.agemin as seeking_agemin, user_date.agemax as seeking_agemax')
	                 ->select('user_profile.headline as headline, user_profile.relationship as relationship, user_profile.body as body, user_profile.tall as height')
                     ->select('user_bg.ethnic as ethnic, user_bg.religious as religious')
                     ->select('user_life.children as children, user_life.occupation as occupation, user_life.wantchildren as wantchildren, user_life.smoke as smoke, user_life.drink as drink');
	    }
	    $result = $this->db->select('user.*')
                           ->where('dt_user.status IS NOT NULL')
	                       ->where("(dt_user.avatar IS NOT NULL AND dt_user.avatar != '' AND dt_user.approval IS NOT NULL AND dt_user.approval = 1)")
	                       ->order_by('register', 'desc')
	                       ->get()
	                       ->result();
	    return $result; 
    }
    
    public function countNewestUser($dayago=7)
    {
        //Gioi han thoi gian tim kiem
        $this->db->where("DATEDIFF(CURDATE(), dt_user.register) <= {$dayago}")
                 ->where('dt_user.status IS NOT NULL')
                 ->where("(dt_user.avatar IS NOT NULL AND dt_user.avatar != '' AND dt_user.approval IS NOT NULL AND dt_user.approval = 1)");
    
        return $this->db->count_all_results();
    }
    
    public function getNewestMatchUsers($userInfo, $total)
    {
            if($userInfo->seeking_agemin != "" && $userInfo->seeking_agemax != "")
            {
                $yearmin = date('Y') - $userInfo->seeking_agemax;
                $yearmax = date('Y') - $userInfo->seeking_agemin;
                $this->db->where("user.birthdate BETWEEN '{$yearmin}-01-01' AND '{$yearmax}-12-30'");
            }else{
                $age = (int)date('Y') - (int)date('Y', strtotime($userInfo->birthdate));
                $agemin = $age - 3;
                $agemax = $age + 3;
                $yearmin = date('Y') - $agemax;
                $yearmax = date('Y') - $agemin;
                $this->db->where("user.birthdate BETWEEN '{$yearmin}-01-01' AND '{$yearmax}-12-30'");
            }
            if($userInfo->seeking_gender==null)
                $userInfo->seeking_gender = 0;
            
            $this->db->where("user.gender", $userInfo->seeking_gender);
            
            $this->db->where_not_in('user.id',array($userInfo->id));
            
            $this->db->join('user_profile', 'user.id = user_profile.user_id')
                    ->join('user_life', 'user.id = user_life.user_id')
                    ->join('user_bg', 'user.id = user_bg.user_id')
                    ->join('user_date', 'user.id = user_date.user_id')
                    ->where('dt_user.status IS NOT NULL')
                    ->where("(dt_user.avatar IS NOT NULL AND dt_user.avatar != '' AND dt_user.approval IS NOT NULL AND dt_user.approval = 1)")
                    ->select('user_bg.ethnic as background, user_bg.religious as religious, user_bg.edu as education, user_bg.speak as speak')
                    ->select('user_date.gender as seeking_gender, user_date.agemin as seeking_agemin, user_date.agemax as seeking_agemax')
                    ->select('user_profile.body as body, user_profile.headline as headline, user_profile.description as description, user_profile.tall as height, user_profile.eyes as eyes, user_profile.hair as hair, user_profile.relationship as relationship')
                    ->select('user_life.drink as drink, user_life.occupation as occupation, user_life.income as income, user_life.smoke as smoke, user_life.children as children, user_life.wantchildren as wantchildren');
            $this->db->limit($total);
            $this->db->order_by('user.register', 'desc');
            $result = $this->db->select('user.*')
                               ->get()
                               ->result();
            return $result;
    }
    
    public function updateFullname()
    {
        $this->db->set('firstname', $this->firstname)
                 ->set('lastname', $this->lastname)
                 ->where('id', $this->id);
        return $this->db->update();
    }

    public function getUsersForPlay($gender, $white_list, $limit=NULL, $offset=NULL)
    {
        //Limit
        if($limit)
        {
            if($offset)
                $this->db->limit($limit, $offset);
            else 
                $this->db->limit($limit);
        }
        $result = $this->db->select('*')
                           ->where('user.gender', $gender)
                           ->where_not_in('user.id', $white_list)
                           ->where('user.status IS NOT NULL')
                           ->where("(dt_user.avatar IS NOT NULL AND dt_user.avatar != '' AND dt_user.approval IS NOT NULL AND dt_user.approval = 1)")
                           ->order_by('RAND()')
                           ->get()
                           ->result();
        return $result;
    }

    public function getTeachers()
    {
        return $this->db->where('role_id', 3)
                        ->get()
                        ->result();
    }

    public function xfUserExists($xf_user_id)
    {
        $result = $this->db->where('xf_user_id', $xf_user_id)
                            ->get()
                            ->result();
        return isset($result[0]) ? $result[0] : false;
    }

    public function updateRoleForUser($id, $role)
    {
        return $this->db->where('id', $id)
                        ->set('role_id', $role)
                        ->update();
    }
}