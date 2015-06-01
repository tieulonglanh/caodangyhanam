<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mc721
 * Date: 11/17/12
 * Time: 6:33 PM
 * To change this template use File | Settings | File Templates.
 */
#[Table('partner')]
#[PrimaryKey('id')]
class Partner extends XPHP_Model
{
    public $id;

    #[Label('Tiêu đề')]
    #[Required(message = 'Tiêu đề không được để trống')]
    #[MaxLength(250, message = 'Tiêu đề có tối đa 250 kí tự')]
    public $name;
    
    #[Label('Mô tả')]
    public $description;

    #[Label('Ảnh đại diện')]
    public $file;

    #[Label('Sắp xếp')]
    public $sort;

    #[Label('Ngày đăng')]
    #[Command(update = false)]
    public $created_date;

        
    public function getLimitOffsetImageAlbums($limit, $offset)
    {
        return $this->db->order_by('sort','desc')
                ->order_by('created_date', 'desc')
                        ->limit($limit)
                        ->offset($offset)
                        ->get()
                        ->result();
    }
    
    public function getAlbumById($id)
    {
        return $this->db->where('id', $id)
                        ->get()
                        ->result();
    }
}