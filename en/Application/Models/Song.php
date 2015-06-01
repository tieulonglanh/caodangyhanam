<?php
#[Table('song')]
#[PrimaryKey('id')]
class Song extends XPHP_Model
{
    public $id;

    #[Label('Tiêu đề')]
    #[Required(message = 'Tiêu đề là bắt buộc nhập')]
    public $title;

    public $artist;

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

    #[Label('Nguời huớng dẫn')]
    #[Join(table='user')]
    public $leader_id;
    #[Label('Được phép truy cập')]
    #[Join(table='role')]
    public $role_id;
    
    #[Label('Tập tin')]
    public $file;

    /**
     * Lấy ra danh sách các video
     *
     * @param int $cat    Danh muc
     * @param int $limit  Limit
     * @param int $offset Offset
     */
    public function getSongs($k, $cat = false, $limit = null, $offset = null)
    {
        if($k !== 0)
            $this->db->like('LOWER(`title`)', $k, 'after');
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
        return $this->db->join('user', 'song.author_id = user.id')
                        ->select('song.*, user.fullname as author_fullname')
                        ->get()
                        ->result();
    }

    public function countSongs($k, $cat = false)
    {
        if($k !== 0)
            $this->db->like('LOWER(`title`)', $k, 'after');
        if($cat)
            $this->db->where('category_id', $cat);
        return $this->db->count_all_results();
    }

    public function getSongById($id)
    {
        $result = $this->db->where('song.id', $id)
                           ->join('user', 'song.author_id = user.id')
                           ->join('user as user_leader', 'song.leader_id = user_leader.id')
                           ->select('song.*, user.fullname as author_fullname, user_leader.fullname as leader_fullname')
                           ->get()
                           ->result();
        return isset($result[0]) ? $result[0] : false;
    }

    /**
     * Lấy ra top bản nhạc mới nhất
     * @param $number
     *
     * @return mixed
     */
    public function getTopNew($number)
    {
        return $this->db->order_by('created_date', 'desc')
                        ->limit($number)
                        ->get()
                        ->result();
    }

    /**
     * Tìm kiếm hợp âm theo type
     * @param $keywords
     * @param $type
     */
    public function searchSong($keywords, $type, $limit=NULL, $offset=NULL)
    {
        if($type == 0)
            $this->db->where("(LOWER(`title`) LIKE '%{$keywords}%' OR LOWER(`artist`) LIKE '%{$keywords}%')");
        else if($type == 1)
            $this->db->like('LOWER(`title`)', $keywords);
        else if($type == 2)
            $this->db->like('LOWER(`artist`)', $keywords);
        if($limit !== NULL)
        {
            if($offset !== NULL)
                $this->db->limit($limit, $offset);
            else
                $this->db->limit($limit);
        }
        return $this->db->order_by('`title`', 'asc')
			            ->get()
			            ->result();
    }

    public function countSearchSong($keywords, $type)
    {
        if($type == 0)
            $this->db->where("(LOWER(`title`) LIKE '%{$keywords}%' OR LOWER(`artist`) LIKE '%{$keywords}%')");
        else if($type == 1)
            $this->db->like('LOWER(`title`)', $keywords);
        else if($type == 2)
            $this->db->like('LOWER(`artist`)', $keywords);
        return $this->db->count_all_results();
    }
}