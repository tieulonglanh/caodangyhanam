<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mc721
 * Date: 11/17/12
 * Time: 6:33 PM
 * To change this template use File | Settings | File Templates.
 */
#[Table('project')]
#[PrimaryKey('id')]
class Project extends XPHP_Model
{
    public $id;

    #[Label('Tiêu đề')]
    #[Required(message = 'Tiêu đề không được để trống')]
    #[MaxLength(250, message = 'Tiêu đề có tối đa 250 kí tự')]
    public $title;

    #[Label('Mô tả ngắn')]
    public $description;

    #[Label('Ảnh đại diện')]
    public $image;

    #[Label('Nội dung')]
    public $content;

    #[Label('Sắp xếp')]
    public $sort;

    #[Label('Ngày đăng')]
    #[Command(update = false)]
    public $created_date;

    #[Label('SEO đường link')]
    public $seo_url;

    #[Label('SEO từ khóa')]
    public $seo_keyword;

    #[Label('SEO mô tả')]
    public $seo_description;

    public $file;
    public $sponsor;
    public $region;
    public $date_start;

    public function getProject($limit, $offset = NULL)
    {
        if($offset !== NULL)
            $this->db->offset($offset);

        return $this->db->order_by('created_date', 'desc')
                        ->limit($limit)
                        ->get()
                        ->result();
    }
    


    public function getCountProject() {
        return $this->db->count_all_results();
    }
    
    public function getProjectById($id)
    {
        return $this->db->where('id',$id)
                        ->get()
                        ->row();
    }
    

}