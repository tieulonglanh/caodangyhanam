<?php
#[Usage(method = true)]
class KendoUI_Attribute_GridAction extends XPHP_Action_Attribute_Abstract
{
    /**
     * (non-PHPdoc)
     * @see XPHP_Action_Attribute_Abstract::onActionExecuted()
     * @param $result XPHP_DataSource
     */
    public function onActionExecuted(&$result)
    {
        if($result instanceof XPHP_DataSource||$result instanceof XPHP_NoSQLDataSource)
        {
            //PAGESIZE
            if(isset($_REQUEST['pageSize']))
                $result->pageSize = (int)$_REQUEST['pageSize'];
            //PAGE
            if(isset($_REQUEST['page']))
                $result->page = (int)$_REQUEST['page'];
            //SORT
            if(isset($_REQUEST['sort']))
            {
                for($i=0; $i<count($_REQUEST['sort']); $i++)
                {
                    $result->model->db->order_by($_REQUEST['sort'][$i]['field'], $_REQUEST['sort'][$i]['dir']);
                }
            }
            //FILTER
            if(isset($_REQUEST['filter']['filters']) && $_REQUEST['filter'] != 'null')
            {
                $this->_setFilter($_REQUEST['filter']['filters'], $result);
            }

            $result->bind();
            $result = new XPHP_Action_Result_Json(array('total' => $result->count, 'data' => $result->getData()));
        }
        else 
            throw new XPHP_Exception("Tham số trả về của GridAction là một DataSource");
    }
    
    /**
     * Set filter
     * @param array $filters Mảng các filters
     * @param XPHP_DataSource $result
     */
    private function _setFilter($filters, &$result)
    {
        for($i=0; $i<count($filters); $i++)
        {
            $filter = $filters[$i];
            if(isset($filter['filters']))
            {
                $this->_setFilter($filter['filters'], $result);          
            }
            else
            {
                if($i == 0)
                    $mt = "where";
                else
                    if($filter['logic'] == "or")
                        $mt = "or_where";
                    else 
                        $mt = "where";
                switch ($filter['operator'])
                {
                    case 'contains':
                        $result->model->db->$mt("{$filter['field']} LIKE", "%" . trim($filter['value']) . "%");
                        break;
                    case 'eq':
                        $result->model->db->$mt($filter['field'], trim($filter['value']));
                        break;
                    case 'startswith':
                        $result->model->db->$mt("{$filter['field']} LIKE", trim($filter['value']) . "%");
                        break;
                    case 'neq':
                        $result->model->db->$mt("{$filter['field']} != ", trim($filter['value']));
                        break;
                    case 'endswith':
                        $result->model->db->$mt("{$filter['field']} LIKE", "%" . trim($filter['value']));
                        break;
                    case 'gte':
                        $result->model->db->$mt("{$filter['field']} >= ", trim($filter['value']));
                        break;
                    case 'gt':
                        $result->model->db->$mt("{$filter['field']} > ", trim($filter['value']));
                        break;
                    case 'lte':
                        $result->model->db->$mt("{$filter['field']} <= ", trim($filter['value']));
                        break;
                    case 'lt':
                        $result->model->db->$mt("{$filter['field']} < ", trim($filter['value']));
                        break;
                        
                }
            }
        }
    }
    
    static function __set_state(array $array)
    {
        $tmp = new self();
        foreach($array as $k => $v) {
            $tmp->$k = $v;
        }
        return $tmp;
    }
}