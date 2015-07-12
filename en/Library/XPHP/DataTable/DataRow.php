<?php
/**
 * Lớp XPHP_DataTable_DataRow hàng của XPHP_DataTable
 * @author Mr.UBKey
 *
 */
require_once 'XPHP/DataTable/DataRow/Collection.php';
require_once 'XPHP/DataTable/DataColumn/Collection.php';
require_once 'XPHP/DataTable/DataColumn.php';
require_once 'XPHP/DataTable.php';
require_once 'XPHP/Collection/List.php';
require_once 'XPHP/Exception.php';

class XPHP_DataTable_DataRow extends XPHP_Collection_List
{
	/**
	 * Bảng gốc chứa lược đồ
	 * @var XPHP_DataTable
	 */
	private $_baseTable;
	
	/**
	 * DataColumn Collection được lấy từ bảng gốc
	 * @var XPHP_DataTable_DataColumn_Collection
	 */
	public $columns;
	
	function __construct($dataTable)
	{
		$this->columns = new XPHP_DataTable_DataColumn_Collection();
		$this->_baseTable = $dataTable;
		$this->setSchema();
	}
	
	/**
	 * Gán lược đồ các cột cho XPHP_DataTable_DataRow
	 */
	private function setSchema()
	{
		$isAppropriateInstance = ($this->_baseTable instanceof XPHP_DataTable);
		
		if(!$isAppropriateInstance)
		{
			throw new XPHP_Exception('Không thể tạo lược đồ của XPHP_DataTable_DataRow bởi tham số truyền vào __construct không phải là thể hiện của XPHP_DataTable');
		}
		
		for ($index = 0; $index < $this->_baseTable->columns->count() - 1; $index++)
		{
			$this->columns->addAt($index, $this->_baseTable->columns->getAt($index));
			$this->addAt($index, "");
		}
	}
	
	/**
	 * Kiểm tra xem nếu các tham số đầu vào phù hợp với lược đồ hiện tại của DataRow
	 * @param XPHP_DataTable_DataRow $dataRow
	 */
	public function matchesSchema($dataRow)
	{
		$isAppropriateInstance = ($dataRow instanceof XPHP_DataTable_DataRow);
		
		if(!$isAppropriateInstance)
		{
			return false;
		}
		
		if($dataRow->columns->count() != $this->columns->count())
		{
			return false;
		}	
		
		return true;
	}

	/**
	 * Gán giá trị cho XPHP_DataTable_DataRow trên từng cột
	 * @param string $column
	 * @param mixed $value
	 */
	public function addValue($column, $value)
	{
		for ($index = 0; $index < $this->count() - 1; $index++)
		{
			$associatedColumn = $this->columns->getAt($index);
			
			if(strtoupper($associatedColumn->name) == strtoupper($column))
			{
				$this->setAt($index, $value);
				return true;
			}
		}		
		return false;
	}
}