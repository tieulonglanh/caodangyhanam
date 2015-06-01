<?php
/**
 * Lớp collection dictionary
 * @author Mr.UBKey
 */

require_once 'XPHP/Collection/Interface.php';
require_once 'XPHP/Exception.php';

class XPHP_Collection_Dictionary implements XPHP_Collection_Interface
{
	/**
	 * Mảng chứa các phần tử
	 * @var array
	 */
	private $dictionary;

	public function __construct($data=null,$readOnly=false)
	{
		$this->dictionary = array();
	}

	/**
	 * Trả về số lượng phần tử của List
	 * @return int
	 */
	public function count()
	{
		return count($this->dictionary);
	}

	/**
	 * Lấy ra mảng chứa các key của Dictionary
	 * @return array
	 */
	public function getKeys()
	{
		return array_keys($this->dictionary);
	}

	/**
	 * Lấy ra giá trị tại một phần tử trong Dictionary
	 * @param mixed $key
	 */
	public function getAt($key)
	{
		if(isset($this->dictionary[$key]))
			return $this->dictionary[$key];
		else
			return null;
	}

	/**
	 * Thêm một phần tử tiếp theo vào Dictionary
	 * @param mixed $key key
	 * @param mixed $value value
	 */
	public function add($key, $value)
	{
		if($key === null)
			$this->dictionary[] = $value;
		else
			$this->dictionary[$key] = $value;
	}

	/**
	 * Xóa một phần tử trong list
	 * @param mixed $key key của phần tử cần xóa
	 * @return mixed giá trị của phần tử vừa xóa, null nếu không tìm thấy key.
	 */
	public function remove($key)
	{
		if(isset($this->dictionary[$key]))
		{
			$value=$this->dictionary[$key];
			unset($this->dictionary[$key]);
			return $value;
		}
		else
		{
			// it is possible the value is null, which is not detected by isset
			unset($this->dictionary[$key]);
			return null;
		}
	}

	/**
	 * Xóa toàn bộ các phần tử của Dictionary
	 */
	public function clear()
	{
		foreach(array_keys($this->dictionary) as $key)
			$this->remove($key);
	}

	/**
	 * Kiểm tra xem một phần tử có nằm trong Dictionary hay không ?
	 * @param mixed $key
	 * @return boolean
	 */
	public function contains($key)
	{
		return isset($this->dictionary[$key]) || array_key_exists($key,$this->dictionary);
	}

	/**
	 * Trả về mảng được chuyển từ collection
	 * @return array
	 */
	public function toArray()
	{
		return $this->dictionary;
	}
}