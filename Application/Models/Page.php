<?php
/**
 * Lớp model Page
 * @author Mr.UBKey
 *
 */
#[Table('page')]
#[PrimaryKey('id')]
class Page extends XPHP_Model
{
    #[Label('Mã')]
    #[Type('number')]
	public $id;
	
	#[Label('Tiêu đề')]
	#[Required(message = 'Tiêu đề không được để trống')]
    #[MaxLength(250, message = 'Tiêu đề có tối đa 250 kí tự')]
    #[Type('string')]
	public $title;
	
	#[Label('Dòng mô tả')]
	#[Type('string')]
	public $headline;
	
	#[Label('Nội dung')]
	#[Type('string')]
	public $content;
	
	#[Label('Ảnh')]
	#[Type('string')]
	public $avatar;

    #[Label('SEO đường link')]
    public $seo_url;

    #[Label('SEO từ khóa')]
    public $seo_keyword;

    #[Label('SEO mô tả')]
    public $seo_description;
	
	#[Type('date')]
	public $created_date;

    #[Label('Trạng thái')]
    public $status;

    public function getNewestPage(){
        $this->db->order_by('created_date', 'desc');
        $this->db->limit(1);
        return $this->db->get()->result();
    }
}