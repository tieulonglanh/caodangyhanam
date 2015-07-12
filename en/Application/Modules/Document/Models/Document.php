<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Daniel
 * Date: 6/19/13
 * Time: 9:17 PM
 * To change this template use File | Settings | File Templates.
 */
#[Table('document')]
#[PrimaryKey('id')]
class Document extends XPHP_Model
{
    public $id;

    #[Label('Tiêu đề')]
    #[Required(message = 'Tiêu đề không được để trống')]
    #[MaxLength(250, message = 'Tiêu đề có tối đa 250 kí tự')]
    public $title;


    #[Label('Ngày đăng')]
    #[Command(update = false)]
    public $created_date;

    #[Label('Tập tin')]
    public $file;
    #[Label('Số/Ký hiệu')]
    public $number;
    #[Label('Mô tả')]
    public $description;
    #[Label('Ngày ban hành')]
    public $date_start;
    public $download;
    
    #[Label('Danh mục')]
    public $category_id;

    public function getCountDocument($category_id = false) {
        if($category_id){
            $this->db->where('category_id', $category_id);
        }
        return $this->db->count_all_results();
    }

    public function getDocument($limit, $offset = NULL, $category_id)
    {
        if($category_id){
            $this->db->where('category_id', $category_id);
        }
        
        if($offset !== NULL)
            $this->db->offset($offset);
        
        return $this->db->order_by('created_date', 'desc')
            ->limit($limit)
            ->get()
            ->result();
    }
    
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
    
    public function getDocumentById($id)
    {
        $result = $this->db->where('id', $id)
            ->get()
            ->result();
        return isset($result[0])? $result[0]: false;
    }
}