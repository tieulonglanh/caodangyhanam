<?php
/**
 * XPHP Framework
 *
 * LICENSE
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * @category	XPHP
 * @package		XPHP_DataTable
 * @author		XWEB Dev Team
 * @copyright	Copyright (c) 2010-2011 XWEB. (http://xweb.vn)
 * @license		http://xphp.xweb.vn/license.html     GNU GPL License
 * @version		$Id: DataTable.php 20116 2011-22-08 02:05:09 Mr.UBKey $
 */
/**
 * XPHP_DataTable_DataRow_Collection.
 *
 * @see XPHP_DataTable_DataRow_Collection
 */
require_once 'XPHP/DataTable/DataRow/Collection.php';
/**
 * XPHP_DataTable_DataColumn_Collection.
 *
 * @see XPHP_DataTable_DataColumn_Collection
 */
require_once 'XPHP/DataTable/DataColumn/Collection.php';
/**
 * XPHP_DataTable_DataColumn.
 *
 * @see XPHP_DataTable_DataColumn
 */
require_once 'XPHP/DataTable/DataColumn.php';
/**
 * XPHP_DataTable_DataRow.
 *
 * @see XPHP_DataTable_DataRow
 */
require_once 'XPHP/DataTable/DataRow.php';
/**
 * XPHP_Collection_List.
 *
 * @see XPHP_Collection_List
 */
require_once 'XPHP/Collection/List.php';
/**
 * XPHP_Exception.
 *
 * @see XPHP_Exception
 */
require_once 'XPHP/Exception.php';
/**
 * XPHP_DataTable
 *
 * @category	XPHP
 * @package		XPHP_DataTable
 * @author		Mr.UBKey
 * @link		http://xphp.xweb.vn/user_guide/xphp_datatable.html
 */
class XPHP_DataTable
{
    /**
     * Tên của DataTable
     * @var string
     */
    public $name;
    /**
     * Collection DataRow của DataTable
     * @var XPHP_DataTable_DataRow_Collection
     */
    public $rows;
    /**
     * Collection DataColumn của DataTable
     * @var XPHP_DataTable_DataColumn_Collection
     */
    public $columns;
    function __construct ()
    {
        $this->name = "";
        $this->rows = new XPHP_DataTable_DataRow_Collection();
        $this->columns = new XPHP_DataTable_DataColumn_Collection();
    }
    /**
     * Trả về một XPHP_DataTable_DataRow mới với lược đồ của bảng hiện tại
     * @throws XPHP_Exception
     */
    public function newRow ()
    {
        if ($this->columns->count() == 0) {
            throw new XPHP_Exception(
            'Không thể tạo một XPHP_DataTable_DataRow vì chưa có cột nào trong bảng');
        }
        $newRow = new XPHP_DataTable_DataRow($this);
        for ($index = 0; $index < $this->columns->count() - 1; $index ++) {
            $newRow->add("");
        }
        return $newRow;
    }
    /**
     * Thêm một XPHP_DataTable_DataRow vào bảng
     * @param XPHP_DataTable_DataRow $dataRow
     * @throws XPHP_Exception
     */
    public function addRow ($dataRow)
    {
        $tempRow = $this->newRow();
        if (! $dataRow->matchesSchema($tempRow)) {
            throw new XPHP_Exception(
            'Không có thể thêm hàng vào bảng bởi vì lược đồ của XPHP_DataTable_DataRow truyền vào không phù hợp với lược đồ của bảng');
        }
        unset($tempRow);
        $this->rows->add($dataRow);
    }
    /**
     * Thêm một cột vào XPHP_DataTable_DataColumn_Collection
     * @param string $columnName Tên cột
     */
    public function addColumn ($columnName)
    {
        $column = new XPHP_DataTable_DataColumn();
        $column->name = $columnName;
        if (! $this->columns->contains($column))
            $this->columns->add($column);
    }
}