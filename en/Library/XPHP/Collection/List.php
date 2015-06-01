<?php
/**
 * Lớp collection list
 * @author Mr.UBKey
 */

require_once 'XPHP/Collection/Interface.php';
require_once 'XPHP/Exception.php';

class XPHP_Collection_List
{
	/**
	 * Mảng chứa các phần tử
	 * @var array
	 */
	private $list;
	
	public function __construct()
	{
		$this->list = array();
	}
	
	/**
	 * Trả về số lượng phần tử của List
	 * @return int
	 */
	public function count()
	{
		return count($this->List);
	}
	
	/**
	 * Thêm một phần tử tiếp theo vào List
	 * @param mixed $value
	 */
	public function add($value)
	{
		$this->list[$this->count()] = $value;
	}
	
	/**
	 * Hỗ trợ thêm một collection vào List
	 * @param XPHP_Collection_Interface $inputCollection
	 */
	public function insertRange($inputCollection)
	{
		$isAppropriateInstance = ($inputCollection instanceof XPHP_Collection_Interface);
		if(!$isAppropriateInstance)
		{
			throw new XPHP_Exception('Tham số truyền vào không phải thể hiện của XPHP_Collection_Interface.  Không thể thêm vào List');
		}
		
		for($i = 0; $i < count($inputCollection); $i++)
		{
			$this->Add($inputCollection->getAt($i));
		}
	}
	
	/**
	 * Xóa toàn bộ các phần tử của List
	 */
	public function clear()
	{
		unset($this->list);
		$this->list = array();
	}
	
	/**
	 * Thêm một phần tử đặc biệt vào List
	 * @param int $index
	 * @param mixed $value
	 * @throws XPHP_Exception
	 */
	public function addAt($index, $value)
	{
		if($this->count() < $index - 1)
		{
			throw new XPHP_Exception('Chỉ mục không nằm trong phạm vi cho phép');
		}
		
		//Thêm vào mảng list
		array_splice($this->list, $index, 0, $value);
	}
	
	/**
	 * Gán một giá trị vào một phần tử trong List
	 * @param int $index
	 * @param mixed $value
	 * @throws XPHP_Exception
	 */
	public function setAt($index, $value)
	{
		if($this->count() < $index)
		{
			throw new XPHP_Exception('Không thể gán giá trị vào List vì chỉ mục không nằm trong phạm vi cho phép');
		}
		
		$this->list[$index] = $value;
	}
	
	/**
	 * Xóa một phần tử trong list
	 * @param int $index
	 * @throws XPHP_Exception
	 */
	public function removeAt($index)
	{
		if($this->count() < $index - 1)
		{
			throw new XPHP_Exception('Chỉ mục không nằm trong phạm vi cho phép');
		}
		
		if($this->count() >= $index)
		{
			if($this->list[$index])
			{
				//Xóa khỏi mảng
				array_splice($this->list, $index, 1);
			}
		}
	}
	
	/**
	 * Lấy ra giá trị tại một phần tử trong List
	 * @param int $index
	 * @throws XPHP_Exception
	 */
	public function getAt($index)
	{
		if($this->count() < $index - 1)
		{
			throw new XPHP_Exception('Chỉ mục không nằm trong phạm vi cho phép');
		}
		
		return $this->list[$index];
	}

	/**
	 * Kiểm tra xem một phần tử có nằm trong list hay không ?
	 * @param mixed $object
	 */
	public function contains($object)
	{
		return in_array($object, $this->list);
	}
	
	/**
	 * Xóa một phần tử trong List nếu nó có tồn tại
	 * Trả về true nếu xóa thành công và ngược lại return false
	 * @param mixed $object
	 */
	public function remove($object)
	{
		for ($index = 0; $index < $this->count() - 1; $index++)
		{
			if($this->list[$index] == $object)
			{
				//Xóa phần tử với index
				array_splice($this->list, $index, 1);
				return true;
			}
		}
	
		return false;
	}
	

	/**
	 * 
	 * Kiểm tra một đối tượng nhập vào với List xem có giống nhau hay không?
	 * @param XPHP_Collection_Interface $object
	 */
	public function equals($object)
	{
		$isInstance = ($object instanceof XPHP_Collection_Interface);
		
		if(!$isInstance)
		{
			return false;
		}
		
		if($this->count() != $object->count())
		{
			return false;
		}
		
		for($index = 0; $index < $this->count() - 1; $index++)
		{
			if(!$object->contains($this->getAt($index)))
				return false;
		}
		
		return true;		
	}
	
	/**
	 * Trả về mảng của các phần tử của List
	 * @return array
	 */
	public function toArray()
	{
		return $this->list;
	}
	
	/**
	 * Lấy ra chỉ mục của một phần tử trong mảng
	 * @param mixed $item
	 * @return int chỉ mục
	 */
	public function indexOf($item)
	{
		if(($index = array_search($item, $this->list, true)) !== false)
			return $index;
		else
			return -1;
	}
	
	
}