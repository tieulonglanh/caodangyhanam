<?php
#[Table('video')]
#[PrimaryKey('id')]
class Video extends XPHP_Model
{
    public $id;

    #[Label('Tiêu đề')]
    #[Required(message = 'Tiêu đề là bắt buộc nhập')]
    public $title;

    #[Label('Mô tả')]
    public $description;

    #[Label('Lời bài hát')]
    public $lyric;

    #[Label('Ảnh đại diện')]
    #[Required(message = 'Ảnh đại diện là bắt buộc nhập')]
    public $avatar;

    #[Label('Đường dẫn')]
    #[Required(message = 'Đường dẫn là bắt buộc nhập')]
    public $url;

    #[Label('Sắp xếp')]
    #[Type('number')]
    public $sort;
    
    public $hit;
    
    public $fav_count;
    
    public $rate_count;
    
    public $rate_point;
    
    public $rate_avg;

    #[Label('Ngày đăng')]
    #[Type('date')]
    #[Command(update = false)]
    public $created_date;

    public $seo_url;

    public $seo_keyword;

    public $seo_description;

    #[Label('Danh mục')]
    #[Join(table='video_category')]
    public $category_id;

    #[Label('Nguời đăng')]
    #[Join(table='user')]
    public $author_id;

    /**
     * Lấy ra danh sách các video mới nhất
     *
     * @param int $number
     */
    public function getLastestVideo($number, $offset=NULL)
    {
        if($offset !== NULL)
            $this->db->offset($offset);
        return $this->db->join('user', 'video.author_id = user.id')
                        ->select('video.*, user.fullname as author_fullname')
                        ->order_by('created_date', 'desc')
                        ->limit($number)
                        ->get()
                        ->result();
    }
    /**
     * Lấy ra tổng số video mới nhất
     */
    public function countLastestVideo()
    {
        return $this->db->count_all_results();
    }
    /**
     * Lấy ra danh sách các video
     *
     * @param int $cat    Danh muc
     * @param int $limit  Limit
     * @param int $offset Offset
     */
    public function getVideos($cat = false, $limit = null, $offset = null)
    {
        if ($cat) {
            $this->db->where('category_id', $cat);
        }
        if ($limit) {
            if ($offset) {
                $this->db->limit($limit, $offset);
            } else {
                $this->db->limit($limit);
            }
        }
        return $this->db->join('user', 'video.author_id = user.id')
                        ->select('video.*, user.fullname as author_fullname')
                        ->get()
                        ->result();
    }

    /**
     * Lấy ra danh sách video xem nhiều nhất
     * @param int $number
     * @param int|null $offset
     *
     * @return mixed
     */
    public function getMostViewVideo($number, $offset=NULL)
    {
        if($offset !== NULL)
            $this->db->offset($offset);
        return $this->db->join('user', 'video.author_id = user.id')
                        ->select('video.*, user.fullname as author_fullname')
                        ->order_by('hit', 'desc')
                        ->limit($number)
                        ->get()
                        ->result();
    }
    /**
     * Lấy ra tổng số video xem nhiều nhất
     *
     * @return int
     */
    public function countMostViewVideo()
    {
        return $this->db->count_all_results();
    }

    /**
     * Lấy ra danh sách video bình chọn nhiều nhất
     * @param int $number
     * @param int|null $offset
     *
     * @return mixed
     */
    public function getMostFavVideo($number, $offset=NULL)
    {
        if($offset !== NULL)
            $this->db->offset($offset);
        return $this->db->join('user', 'video.author_id = user.id')
                        ->select('video.*, user.fullname as author_fullname')
                        ->order_by('fav_count', 'desc')
                        ->limit($number)
                        ->get()
                        ->result();
    }
    /**
     * Lấy ra tổng số video bình chọn nhiều nhất
     *
     * @return int
     */
    public function countMostFavVideo()
    {
        return $this->db->count_all_results();
    }

    public function getAllVideos()
    {
        return $this->db->join('user', 'video.author_id = user.id')
                        ->select('video.*, user.fullname as author_fullname')
                        ->order_by('created_date', 'desc')
                        ->get()
                        ->result();
    }

    public function plusHitVideo($id, $count=NULL)
    {
        if($count === NULL)
        {
            //Lay ra so luot count hien tai
            $v = $this->db->where('id', $id)
                          ->get()
                          ->result();
            if(isset($v[0]))
            {
                $count = $v[0]->hit;
            }
        }

        return $this->db->set('hit', $count + 1)
                        ->where('id', $id)
                        ->update();
    }

    public function getRelativeVideo($cat_id, $id, $limit=NULL, $offset=NULL)
    {
        if($limit !== NULL)
        {
            if($offset !== NULL)
                $this->db->limit($limit, $offset);
            else
                $this->db->limit($limit);
        }
        return $this->db->where('category_id', $cat_id)
                        ->where_not_in('id', array($id))
                        ->join('user', 'video.author_id = user.id')
                        ->select('video.*, user.fullname as author_fullname')
                        ->get()
                        ->result();
    }

    /*public function getVideoById($id)
    {
        $result = $this->db->where('video.id', $id)
                           ->join('user', 'video.author_id = user.id')
                           ->join('user as user_leader', 'video.leader_id = user_leader.id')
                           ->select('video.*, user.fullname as author_fullname, user_leader.fullname as leader_fullname')
                           ->get()
                           ->result();
        return isset($result[0]) ? $result[0] : false;
    }*/

    public function getVideoById($id)
    {
        return $this->db->where('id',$id)->get()->row();
    }

    public function getVideoByLeaderId($leader_id)
    {
        $result = $this->db->where('video.leader_id', $leader_id)
                           ->join('user', 'video.author_id = user.id')
                           ->select('video.*, user.fullname as author_fullname')
                           ->get()
                           ->result();
        return $result;
    }

    public function getVideoByCateogoryId($category_id, $limit=NULL, $offset=NULL)
    {
        if($limit !== NULL)
            $this->db->limit($limit);
        if($offset !== NULL)
            $this->db->offset($offset);

        $result = $this->db->where('video.category_id', $category_id)
                           ->join('user', 'video.author_id = user.id')
                           ->select('video.*, user.fullname as author_fullname')
                ->order_by('created_date', 'desc')
                           ->get()
                           ->result();
        return $result;
    }

    public function countVideoByCateogoryId($category_id)
    {
        return $this->db->where('video.category_id', $category_id)
                        ->count_all_results();
    }
}