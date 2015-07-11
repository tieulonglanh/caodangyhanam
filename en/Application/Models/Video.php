<?php
#[Table('video')]
#[PrimaryKey('id')]
class Video extends XPHP_Model
{
	public $id;

	#[Label('Tiêu đề')]
	#[Required(message = 'Tiêu đề là bắt buộc nhập')]
	public $title;

	#[Label('Mô tả')]
	public $description;

	#[Label('Ảnh đại diện')]
	#[Required(message = 'Ảnh đại diện là bắt buộc nhập')]
	public $avatar;

	#[Label('Đường dẫn')]
	#[Required(message = 'Đường dẫn là bắt buộc nhập')]
	public $url;

	#[Label('Sắp xếp')]
	#[Type('number')]
	public $sort;

	public $hit;

	public $rate_count;

	public $rate_point;

	public $rate_avg;

	#[Label('Ngày đăng')]
	#[Type('date')]
	#[Command(update = false)]
	public $created_date;

	public $seo_url;

	public $seo_keyword;

	public $seo_description;

	#[Label('Danh mục')]
	#[Join(table='video_category')]
	public $category_id;

	/**
	 * Lấy ra danh sách các video mới nhất
	 *
	 * @param int $number
	 */
	public function getLastestVideo($number)
	{
		return $this->db->join('user', 'video.author_id = user.id')
			->select('video.*, user.fullname as author_fullname')
			->order_by('created_date', 'desc')
			->limit($number)
			->get()
			->result();
	}

	/**
	 * Lấy ra danh sách các video
	 *
	 * @param int $cat    Danh muc
	 * @param int $limit  Limit
	 * @param int $offset Offset
	 */
	public function getVideos($cat = false, $limit = null, $offset = null)
	{
		if ($cat) {
			$this->db->where('category_id', $cat);
		}
		if ($limit) {
			if ($offset) {
				$this->db->limit($limit, $offset);
			}
			else {
				$this->db->limit($limit);
			}
		}
		return $this->db->get()
			->result();
	}

	public function getMostViewVideo($number)
	{
		return $this->db->order_by('hit', 'desc')
			->limit($number)
			->get()
			->result();
	}

	/**
	 * Tìm kiếm hợp âm theo type
	 *
	 * @param $keywords
	 * @param $type
	 */
	public function searchVideo($keywords, $limit = NULL, $offset = NULL)
	{

		$this->db->like('LOWER(`title`)', $keywords);

		if ($limit !== NULL) {
			if ($offset !== NULL) {
				$this->db->limit($limit, $offset);
			}
			else {
				$this->db->limit($limit);
			}
		}
		return $this->db->order_by('`title`', 'asc')
						->get()
						->result();
	}

	public function countSearchVideo($keywords)
	{
		$this->db->like('LOWER(`title`)', $keywords);

		return $this->db->count_all_results();
	}
        public function getNewVideos($number)
	{
		return $this->db->order_by('created_date', 'desc')
			->limit($number)
			->get()
			->result();
	}
}