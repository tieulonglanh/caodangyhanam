<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Mr.UBKey
 * Date: 12/29/12
 * Time: 10:35 PM
 */
#[Table('hopam')]
#[PrimaryKey('id')]
class Hopam extends XPHP_Model
{
    public $id;
    #[Label('Tiêu đề')]
    public $title;
    #[Label('Nghệ sĩ')]
    public $artist;
    #[Label('Mô tả')]
    public $description;
    #[Label('Xml')]
    public $lyrics;
    #[Label('Ảnh đại diện')]
    public $image;
    #[Label('Đường dẫn file xem')]
    public $url;
    #[Label('Mã nhúng')]
    public $embed_code;
    #[Label('Sắp xếp')]
    public $sort;
    
    public $hit;
    
    public $fav_count;
    
    public $rate_count;
    
    public $rate_point;
    
    public $rate_avg;
    #[Command(update = false)]
    public $created_date;
    #[Label('SEO Url')]
    public $seo_url;
    #[Label('SEO Keyword')]
    public $seo_keyword;
    #[Label('SEO Description')]
    public $seo_description;
    #[Label('Trạng thái')]
    public $status;
    #[Label('Người gửi')]
    #[Join(table = 'user')]
    #[Command(update = false)]
    public $author_id;
    #[Label('Thể hiện')]    
    #[Join(table = 'user')]
    public $leader_id;
    #[Label('Được quyền xem')]
    #[Join(table = 'role')]
    public $role_id;

    /**
     * Lấy ra hợp âm
     * @param $id
     */
    public function getHopamById($id)
    {
        $result = $this->db->where('hopam.id', $id)
                           ->join('user', 'hopam.author_id = user.id')
                           ->join('user as user_leader', 'hopam.leader_id = user_leader.id')
                           ->select('hopam.*, user.fullname as author_fullname, user_leader.fullname as leader_fullname')
                           ->get()
                           ->result();
        return isset($result[0]) ? $result[0] : false;
    }

    /**
     * Lấy ra danh sách các hợp âm
     * @param int $limit
     * @param int|null $offset
     *
     * @return mixed
     */
    public function getHopam($k, $limit=NULL, $offset=NULL)
    {
        if($limit !== NULL)
            $this->db->limit($limit);
        if($offset !== NULL)
            $this->db->offset($offset);
        if($k !== 0)
            $this->db->like('LOWER(`title`)', $k, 'after');
        return $this->db->order_by('title', 'asc')
                        ->get()
                        ->result();
    }

    /**
     * Lấy ra tổng số hợp âm
     * @param $k
     *
     * @return string
     */
    public function countHopam($k)
    {
        if($k !== 0)
            $this->db->like('LOWER(`title`)', $k, 'after');
        return $this->db->count_all_results();
    }

    /**
     * Lấy ra top hợp âm xem nhiều nhất
     * @param $number
     *
     * @return mixed
     */
    public function getTopHit($number)
    {
        return $this->db->order_by('hit', 'desc')
                        ->limit($number)
                        ->get()
                        ->result();
    }

    /**
     * Lấy ra top hợp âm mới nhất
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
    public function searchHopam($keywords, $type, $limit=NULL, $offset=NULL)
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

        return $this->db->order_by('title', 'asc')
                        ->get()
                        ->result();
    }

    public function countSeachHopam($keywords, $type)
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
