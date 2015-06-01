<?php
/**
 * XPHP_Widget_Attribute_DataTable BASE ON Ignited Datatables
 *
 * This is a wrapper class/library based on the native Datatables server-side implementation by Allan Jardine
 * found at http://datatables.net/examples/data_sources/server_side.html for CodeIgniter
 *
 * @package    CodeIgniter
 * @subpackage libraries
 * @category   library
 * @version    0.7
 * @author     Vincent Bambico <metal.conspiracy@gmail.com>
 *             Yusuf Ozdemir <yusuf@ozdemir.be>
 *             XWeb Dev Team <mr.ubkey@gmail.com>
 * @link       http://codeigniter.com/forums/viewthread/160896/
 */
class XPHP_Widget_Attribute_DataTable extends XPHP_Action_Attribute_Abstract
{
    /**
     * Các columns
     * @var array
     */
    private $_columns;

    private $_name;

    protected $filter = array();

    /**
     * @var XPHP_DataSource
     */
    private $_dataSource;
    /**
     * Gán tham số truyền vào cho Attribute
     * @throws XPHP_Attribute_Exception
     */
    public function init ($properties)
    {
        if(isset($properties['name']))
            $this->_name = $properties['name'];
        if(isset($properties['columns']))
            $this->_columns = $properties['columns'];
    }

    /**
     * Thực thi XPHP_Widget_DataTable và trả về kết quả
     * @param XPHP_Model | XPHP_DataSource | NULL $result
     * @throws XPHP_Exception
     */
    public function onActionExecuted (&$result=NULL)
    {
        if($result instanceof XPHP_DataSource)
            $dataSource = $result;
        else if($result instanceof XPHP_Model)
        {
            $dataSource = new XPHP_DataSource($result, NULL);
        }
        else
        {
            if(empty($this->_name))
                throw new XPHP_Exception("Bạn phải gán giá trị name cho DataTable hoặc action trả về XPHP_DataSource");
            else
                $dataSource = new XPHP_DataSource($this->_name);
        }
        if($this->_columns)
            $dataSource->select(str_replace(' ', ',', $this->_columns));
        //Set datasource
        $this->_dataSource = $dataSource;
        //Set isAsso
        $this->_dataSource->isAssociative = $this->check_mDataprop();        
        $this->_dataSource->bind();    
        echo $this->generate();
        die;
    }

    /**
     * Builds all the necessary query segments and performs the main query based on results set from chained statements
     *
     * @param string charset
     * @return string
     */
    public function generate($charset = 'UTF-8')
    {
        $this->get_paging();
        $this->get_ordering();
        $this->get_filtering();
        return $this->produce_output($charset);
    }

    /**
     * Generates the LIMIT portion of the query
     *
     * @return mixed
     */
    protected function get_paging()
    {
        $iStart = $this->_request('iDisplayStart');
        $iLength = $this->_request('iDisplayLength');
        $this->_dataSource->limit(($iLength && $iLength != '' && $iLength != '-1')? $iLength : 100, ($iStart)? $iStart : 0);
    }

    /**
     * Generates the ORDER BY portion of the query
     *
     * @return mixed
     */
    protected function get_ordering()
    {
        if($this->check_mDataprop())
            $mColArray = $this->get_mDataprop();
        else if($this->_request('sColumns'))
            $mColArray = explode(',', $this->_request('sColumns'));
        else
            $mColArray = $this->_dataSource->columnNames;

        $mColArray = array_values(array_diff($mColArray, $this->_dataSource->unsetColumns));
        $columns = array_values(array_diff($this->_dataSource->columnNames, $this->_dataSource->unsetColumns));

        if($this->_request('iSortingCols'))
            $this->_dataSource->resetOrderBy(); #Xoá các sắp xếp mặc định
        for($i = 0; $i < intval($this->_request('iSortingCols')); $i++)
        {
            if(isset($mColArray[intval($this->_request('iSortCol_' . $i))])
                && in_array($mColArray[intval($this->_request('iSortCol_' . $i))], $columns)
                && $this->_request('bSortable_'.intval($this->_request('iSortCol_' . $i))) == 'true')
            {
                $this->_dataSource->order_by($mColArray[intval($this->_request('iSortCol_' . $i))], $this->_request('sSortDir_' . $i));
            }
        }
    }

    /**
     * Tạo bộ lọc FILTER
     *
     * @param mixed $key_condition
     * @param string $val
     * @param bool $backtick_protect
     * @return XPHP_DataSource
     */
    public function filter($key_condition, $val = NULL, $backtick_protect = TRUE)
    {
        $this->filter[] = array($key_condition, $val, $backtick_protect);
        return $this;
    }

    /**
     * Generates the LIKE portion of the query
     *
     * @return mixed
     */
    protected function get_filtering()
    {
        if($this->check_mDataprop())
            $mColArray = $this->get_mDataprop();
        else if($this->_request('sColumns'))
            $mColArray = explode(',', $this->_request('sColumns'));
        else
            $mColArray = $this->_dataSource->columnNames;

        $sWhere = '';
        $sSearch = mysql_real_escape_string($this->_request('sSearch'));
        $mColArray = array_values(array_diff($mColArray, $this->_dataSource->unsetColumns));
        $columns = array_values(array_diff($this->_dataSource->columnNames, $this->_dataSource->unsetColumns));

        if($sSearch != '')
            for($i = 0; $i < count($mColArray); $i++)
                if($this->_request('bSearchable_' . $i) == 'true' && in_array($mColArray[$i], $columns))
                    $sWhere .= $mColArray[$i] . " LIKE '%" . $sSearch . "%' OR ";

        $sWhere = substr_replace($sWhere, '', -3);

        if($sWhere != '')
            $this->_dataSource->where('(' . $sWhere . ')');

        for($i = 0; $i < intval($this->_request('iColumns')); $i++)
        {
            if($this->_request('sSearch_' . $i) && $this->_request('sSearch_' . $i) != '' && in_array($mColArray[$i], $columns))
            {
                $miSearch = explode(',', $this->_request('sSearch_' . $i));

                foreach($miSearch as $val)
                {
                    if(preg_match("/(<=|>=|=|<|>)(\s*)(.+)/i", trim($val), $matches))
                        $this->_dataSource->where($mColArray[$i].' '.$matches[1], $matches[3]);
                    else
                        $this->_dataSource->where($mColArray[$i].' LIKE', '%'.$val.'%');
                }
            }
        }

        foreach($this->filter as $val)
            $this->_dataSource->where($val[0], $val[1], $val[2]);
    }

    /**
     * Builds a JSON encoded string data
     *
     * @param string charset
     * @return string
     */
    protected function produce_output($charset)
    {
        $aaData = $this->_dataSource->getDataTable();
        $iTotal = $this->get_total_results();
        $iFilteredTotal = $this->get_total_results(true);

        $sColumns = array_diff($this->_dataSource->columnNames, $this->_dataSource->unsetColumns);
        $sColumns = array_merge_recursive($sColumns, array_merge(array_keys($this->_dataSource->addColumns), array_keys($this->_dataSource->addTemplateColumns)));

        $sOutput = array
        (
            'sEcho'                => intval($this->_request('sEcho')),
            'iTotalRecords'        => $iTotal,
            'iTotalDisplayRecords' => $iFilteredTotal,
            'aaData'               => $aaData,
            'sColumns'             => implode(',', $sColumns)
        );

        if(strtolower($charset) == 'utf-8')
            return json_encode($sOutput);
        else
            return $this->jsonify($sOutput);
    }

    /**
     * Get result count
     *
     * @return integer
     */
    protected function get_total_results($filtering = false)
    {
        if($filtering)
            $this->get_filtering();

        return $this->_dataSource->countResults($filtering);
    }

    /**
     * Check mDataprop
     *
     * @return bool
     */
    protected function check_mDataprop()
    {
        if(!$this->_request('mDataProp_0'))
            return false;

        for($i = 0; $i < intval($this->_request('iColumns')); $i++)
            if(!is_numeric($this->_request('mDataProp_' . $i)))
                return true;

        return false;
    }

    /**
     * Get mDataprop order
     *
     * @return mixed
     */
    protected function get_mDataprop()
    {
        $mDataProp = array();

        for($i = 0; $i < intval($this->_request('iColumns')); $i++)
            $mDataProp[] = $this->_request('mDataProp_' . $i);

        return $mDataProp;
    }

    private function _request($name)
    {
        return isset($_REQUEST[$name]) ? $_REQUEST[$name] : false;
    }
}