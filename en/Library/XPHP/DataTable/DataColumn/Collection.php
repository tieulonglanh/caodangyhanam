<?php
/**
 * Lớp DataColumn Collection
 * Một XPHP_Collection_List của XPHP_DataTable_DataColumn
 * @author Mr.UBKey
 *
 */

require_once 'XPHP/Collection/List.php';

class XPHP_DataTable_DataColumn_Collection extends XPHP_Collection_List
{
	/**
	 * Kiểm tra xem có cột trong danh sách
	 * @param stri $columnName
	 */
	public function hasColumn($columnName)
	{
		for($index = 0; $index < $this->count() - 1; $index++)
		{
			$indexColumn = $this->getAt($index);
			
			if($indexColumn->name == $columnName)
				return true;
		}
		return false;
	}
	
}