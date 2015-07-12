<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mc721
 * Date: 11/17/12
 * Time: 1:38 AM
 * To change this template use File | Settings | File Templates.
 */
#[Table('document_category')]
#[PrimaryKey('id')]
class DocumentCategory extends XPHP_Model
{
    public $id;

    #[Label('Tên danh mục')]
    #[Required(message = 'Tên danh mục là bắt buộc nhập')]
    #[MaxLength(250, message = 'Tên danh mục có tối đa 250 kí tự')]
    public $name;

    #[Label('Mô tả ngắn')]
    public $description;

    #[Label('Ảnh đại diện')]
    public $image;

    #[Command(update = false)]
    public $created_date;

    #[Label('Sắp xếp')]
    public $sort;

    #[Label('SEO đường link')]
    public $seo_url;

    #[Label('SEO từ khóa')]
    public $seo_keyword;

    #[Label('SEO mô tả')]
    public $seo_description;

    #[Label('Danh mục cha')]
    public $parent_id;

    /**
     * Lấy ra cây danh mục từ danh mục gốc
     * @return array
     */
    public function getCategoryTree ($parent_id = 0)
    {
        return $this->_getCategoryTree($parent_id);
    }

    /**
     * Danh mục category sắp xếp hiển thị theo dạng cây
     * @var string
     */
    private $_catList = array();

    /**
     * Đánh dấu cấp độ của danh mục
     * @var int
     */
    private $_catLevel = 1;

    /**
     * Phương thức đệ quy lấy ra danh mục artical dạng cây
     * @param int $parent_id
     */
    private function _getCategoryTree ($parent_id)
    {
        //Lấy ra toàn bộ danh mục menu con của root
        $cats = $this->db->where('parent_id', $parent_id)
                         ->order_by('sort', 'asc')
                         ->get()
                         ->result();
        //Dấu gạch biểu thị cấp độ
        $prefix = "";
        for ($i = 0; $i < $this->_catLevel; $i ++) {
            $prefix .= " ------ ";
        }
        if (count($cats) > 0) {
            foreach ($cats as $cItem) {
                $cItem->name = $prefix . $cItem->name;
                $this->_catList[] = $cItem;
                if ($this->countSubCat($cItem->id)) {
                    $this->_catLevel ++;
                    $this->_getCategoryTree($cItem->id);
                    $this->_catLevel --;
                }
            }
        }
        return $this->_catList;
    }

    /**
     * Lấy ra danh mục đa cấp
     * @return array
     */
    public function getCategoryMultiLevel ($parent_id = 0)
    {
        return $this->_getCategoryMultiLevel($parent_id);
    }

    /**
     * Phương thức đệ quy lấy ra danh mục đa cấp
     * @param int $parent_id
     */
    private function _getCategoryMultiLevel ($parent_id)
    {
        //Lấy ra toàn bộ menu item của danh mục parent
        $cats = $this->db->where('parent_id', $parent_id)
                         ->order_by('sort', 'asc')
                         ->get()
                         ->result();
        if (count($cats) > 0) {
            for ($i = 0; $i < count($cats); $i ++) {
                if ($this->countSubCat($cats[$i]->id)) {
                    $cats[$i]->subs = $this->_getCategoryMultiLevel($cats[$i]->id);
                }
            }
        }
        return $cats;
    }

    /**
     * Phương thức lấy ra số danh mục con của một danh mục
     * @param int $parent_id
     */
    public function countSubCat ($parent_id)
    {
        //Lấy ra toàn bộ danh mục sản phẩm con của danh mục parent
        $cats = $this->db->where('parent_id', $parent_id)
                         ->count_all_results();
        return $cats;
    }
    
    public function getCatByid($id)
    {
        return $this->db->where('id',$id)
                ->get()
                ->row();
    }
    
    public function getCatBySeoUrl($seo_url)
    {
        return $this->db->where('seo_url',$seo_url)
                ->get()
                ->row();
    }
}
