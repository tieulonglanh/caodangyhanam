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

    #[Label('Mã dự án')]
    public $project_id;

    #[Label('Ngày đăng')]
    #[Command(update = false)]
    public $created_date;


    public $file;
    public $sponsor;
    public $region;
    public $date_start;
    public $download;

    public function getCountDocument() {
        return $this->db->count_all_results();
    }

    public function getDocument($limit, $offset = NULL)
    {
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
}