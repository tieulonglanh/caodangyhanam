<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Image
 *
 * @author longnh
 */
#[Table('image')]
#[PrimaryKey('id')]
class Image extends XPHP_Model {
    //put your code here
    
    public $id;
    
    #[Label('Tiêu đề')]
    #[Required(message = 'Tiêu đề không được để trống')]
    #[MaxLength(250, message = 'Tiêu đề có tối đa 250 kí tự')]
    public $name;
    
    #[Label('Album')]
    #[Join(table = 'image_category')]
    public $cat_id;
    
    #[Label('File')]
    public $file;
    
    public $download;
    
    
    public function getImagesByAlbum($id, $limit, $offset)
    {
        return $this->db->where('cat_id', $id)
                        ->limit($limit)
                        ->offset($offset)
                        ->get()
                        ->result();
    }
}

?>
