<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mc721
 * Date: 11/17/12
 * Time: 6:33 PM
 * To change this template use File | Settings | File Templates.
 */
#[Table('artical')]
#[PrimaryKey('id')]
class Artical extends XPHP_Model
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

    #[Label('Bật/Tắt')]
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

    #[Required(message = 'Danh mục không được để trống')]
    #[Label('Danh mục bài viết')]
    #[Join(table = 'artical_category')]
    public $category_id;

    public $view_count;
    
    #[Label('Box tin mới')]
    public $top_new;

    public function getArticals($limit, $offset = NULL)
    {
        if($offset !== NULL)
            $this->db->offset($offset);

        return $this->db->order_by('created_date', 'desc')
                        ->limit($limit)
                        ->get()
                        ->result();
    }
    
    public function getArticalsByCategory($limit, $cat_id, $offset=NULL){
        if($offset !== NULL)
            $this->db->offset($offset);
        return $this->db->where('category_id',$cat_id)
                
                ->where('sort', 1)
                        ->order_by('created_date', 'desc')
                        ->limit($limit)
                        ->get()
                        ->result();
    }

    public function getCountArtical($cat_id = NULL) {
        if($cat_id !== NULL)
            $this->db->where('category_id',$cat_id);
        
        $this->db->where('sort', 1);
        return $this->db->count_all_results();
    }
    
    public function getArticalById($id)
    {
        return $this->db->where('id',$id)
                ->where('sort', 1)
                        ->get()
                        ->row();
    }
    
    public function getLimitArticals($limit, $cat_id)
    {
        return $this->db->where('category_id',$cat_id)
                ->where('sort', 1)
                        ->order_by('created_date', 'desc')
                        ->limit($limit)
                        ->get()
                        ->result();
    }
    public function changeViewCount($id, $view_count) {
        return $this->db->where('id',$id)
            ->set('view_count', $view_count+1)
            ->update();
    }
}