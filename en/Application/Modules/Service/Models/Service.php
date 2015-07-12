<?php
/**
 * Lớp model Page
 * @author Mr.UBKey
 *
 */
#[Table('service')]
#[PrimaryKey('id')]
class Service extends XPHP_Model
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

    public $view_count;

    /**
     * Lấy ra danh sachs tin tức
     * @param array $options Điều kiện
     */
    public function getPages ($options = array())
    {
        //Set where
        foreach ($options as $field => $value) {
            $this->db->where($field, $value);
        }
        //Order
        $this->db->order_by('created_date', 'desc');
        return $this->db->get()->result();
    }

    public function getActical($id) {
        return $this->db->where('id',$id)
            ->get()->row();
    }

    public function changeStatus($id, $status)
    {
        return $this->db->where('id',$id)
                        ->set('status', (int)$status)
                        ->update();
    }

    public function changeViewCount($id, $view_count) {
        return $this->db->where('id',$id)
                ->set('view_count', $view_count+1)
                ->update();
    }
}