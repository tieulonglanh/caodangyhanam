<?php
/**
 * Interface của collection
 * @author Mr.UBKey
 *
 */
interface XPHP_Collection_Interface
{
	/**
	 * Lấy ra phần tử tại vị trí $index
	 * @param int $index
	 */
	public function getAt($index);
	
	/**
	 * Đếm số phần tử trong collection
	 */
	public function count();
	
	/**
	 * Kiểm tra xem một phần tử có nằm trong collection hay không ?
	 * @param mixed $object
	 */
	public function contains($object);
	
	/**
	 * Trả về mảng được chuyển từ collection
	 * @return array
	 */
	public function toArray();
	
	/**
	 * Xóa toàn bộ các phần tử của collection
	 */
	public function clear();
}