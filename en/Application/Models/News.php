<?php
#[Table('news')]
#[PrimaryKey('id')]
class News extends XPHP_Model
{
	public $id;
    #[Label('Tiêu đề')]
    #[Required(message = 'Tiêu đề là bắt buộc nhập')]
    #[MaxLength(300, message = 'Tiêu đề có tối đa 300 kí tự')]
    public $title;
    #[Label('Mô tả ngắn')]
    public $brief;
    #[Label('Nội dung')]
    #[Required(message = 'Nội dung không được để trống')]
    public $content;
    #[Label('Trạng thái tin bài')]
    public $status;
    #[Label('Avatar')]
    public $avatar;
    #[Label('Đường dẫn video')]
    public $video_path;
    #[Label('Danh mục tin bài')]
    public $category_id;
    #[Label('Ngày tạo')]
    public $create_date;
    #[Label('Người tạo')]
    public $create_by;
    #[Label('Ngày cập nhật')]
    public $update_date;
    #[Label('Người cập nhật')]
    public $update_by;
    #[Label('Ngày duyệt xuất bản')]
    public $publish_date;
    #[Label('Người duyệt xuất bản')]
    public $publish_by;
    #[Label('Cho phép comment')]
    public $hascomment;
    #[Label('Đánh giá')]
    public $rating;
    #[Label('Số lần đọc')]
    public $hits;
    #[Label('Loại tin bài')]
    public $type_id;
    #[Label('Ngôn ngữ')]
    public $language_id;
    #[Label('Danh mục khác')]
    public $other_category;
    #[Label('Tác giả bài viết')]
    public $author_id;
    
    /**
     * Lấy ra danh mục tin tức
     * @param int $newsId
     * @return int | bool
     */
    public function getCategoryIdOfNews($newsId)
    {
        $result = $this->db->select('category_id')
                           ->where('id', $newsId)
                           ->get()
                           ->result();
        return isset($result[0]) ? $result[0]->category_id : false;
    }
    
    /**
     * Lấy ra 5 tin tức mới nhất của danh muc 
     * @param int $catId
     * @param int $number
     */
    public function getTopNewsOfCat($catId, $number)
    {
    	$catList = array();
    	$catList[] = $catId;
    	$catModel = new Category();
    	$cats = $catModel->getAllSubCategories($catId);
    	foreach ($cats as $cat)
    	{
    		$catList[] = $cat->id;
    	}
    	return  $this->db->where_in('category_id', $catList)
    					 ->where('status', 3)
    					 ->order_by('create_date', 'DESC')
    					 ->limit($number)
    					 ->get()
    					 ->result();
    }
    
    /**
     * Lay ra danh sach tin tuc moi nhat
     * @param int $number
     */
    public function getNewest($number)
    {
    	return  $this->db->select('news.id, news.title, news.brief, news.avatar, news.category_id, news_category.name as catname, news.create_date')
                ->join('news_category', 'news.category_id=news_category.id')
    					 ->where('news.status', 3)
    					 ->order_by('news.create_date', 'DESC')
				    	 ->limit($number)
				    	 ->get()
				    	 ->result();
    }
    
    /**
     * Lay ra danh sach tin tuc rate lon nhat
     * @param int $number
     */
    public function getRating($number)
    {
    	return  $this->db->select('title, id, brief, avatar, category_id')
    					 ->where('status', 3)
				    	 ->order_by('rating', 'DESC')
				    	 ->limit($number)
				    	 ->get()
				    	 ->result();
    }
    
    /**
     * Lay ra danh sach tin tuc
     * @param int $number
     */
    public function getHits($number)
    {
    	return  $this->db->select('title, id, brief, avatar, category_id')
    					 ->where('status', 3)
				    	 ->order_by('hits', 'DESC')
				    	 ->limit($number)
				    	 ->get()
				    	 ->result();
    }
    
    /**
     * Lấy ra danh sách tin tức của user
     */
    public function getUserNews($userId, $categoryId = 0, $status = null, $limit = 0)
    {
    	//Lấy danh sách tin tức thuộc danh mục user có quyền
    	$where = '(';
    	$first = 1;
    	if ($categoryId > 0)
    	{
    		$where .= "category_id = $categoryId";
    		$first = 0;
    	}
    	if ( $status != null)
    		if ($first == 1)
    		{
    			$where .= "status=$status";
    			$first = 0;
    		}
    		elseif ($first == 0)
    		{
    			$where .= " AND status=$status";
    			$first = 0;
    		}
    	if ($first == 0)
    		$where .= ") AND (create_by = $userId OR publish_by = $userId)";
    	else 
    		$where .= "create_by=$userId or publish_by=$userId)";
    	return $this->db->where($where)
    				    ->order_by('create_date', 'DESC')
    					->get()
                        ->result();
    }
    
    //Lấy danh sách tin tức trang bên ngoài.
    public function getArrNews($where = null, $limit = 0)
    {
    	$this->db->select('news.id, news.title, news.brief, news.avatar, news.category_id, news_category.name as catname, news.create_date')
    			->join('news_category', 'news.category_id=news_category.id');
    	if ($where != null)
    		$this->db->where($where);
    	if ($limit > 0)
    		$this->db->limit($limit);
    	return $this->db->order_by('create_date', 'DESC')
    					->get()->result();
    }
}