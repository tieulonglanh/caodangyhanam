<?php
class KendoUI_Grid extends XPHP_Widget_Abstract
{
    /**
     * XPHP_DataSource
     * @var XPHP_DataSource
     */
    public $dataSource;
    
    /**
     * (non-PHPdoc)
     * @see XPHP_Widget_Interface::init()
     */
    public function init()
    {
        if(isset($this->option['id']))
        {
            $this->id = $this->option['id'];
            unset($this->option['id']);
        }
        else 
            $this->id = XPHP_String::randomString(5);
        //Unset
        unset($this->option['dataSource']);
    }
    
    /**
     * (non-PHPdoc)
     * @see XPHP_Widget_Interface::render()
     */
    public function render()
    {
        //Html
        echo "<div id='{$this->id}'></div>";
        //Code block
        //Datasource
        $dataSource = new stdClass();
        $dataSource->schema = $this->dataSource->schema;
        $dataSource->schema->data = "data";
        $dataSource->schema->total = "total";
        $dataSource->pageSize = $this->dataSource->pageSize;
        $dataSource->serverPaging = $this->dataSource->pageable;
        $dataSource->serverSorting = $this->dataSource->sortable;
        $dataSource->serverFiltering = $this->dataSource->filterable;
        //Remote url
        if(!isset($this->remote) || $this->remote === null)
            $dataSource->transport->read->url = XPHP_Url::getCurrentUrl();
        else if(is_string($this->remote))
            $dataSource->transport->read->url = $this->remote;
        else
            $dataSource->transport->read->url = XPHP_Url::getActionUrl($this->remote, true);
        //Remote datatype
        if(!isset($this->dataType) || $this->dataType === null)
            $dataSource->transport->read->dataType = "json";
        else
            $dataSource->transport->read->dataType = $this->dataType;
        //Datasource type
        if(isset($this->type) && $this->type !== null)
            $dataSource->type = $this->type;
        //Widget options
        $joptions = array("dataSource" => $dataSource);
        //Columns
        $columns = array();
        foreach ($this->dataSource->columns as $name => $op)
        {
            $column = array();
            //Định nghĩa column từ người dùng
            if(isset($this->columns) && isset($this->columns[$name]))
                $column = $this->columns[$name];
            //Định nghĩa từ model 
            $column = array_merge(array('field' => $name,
                               		    'title' => $op['title']), $column);
            $columns[] = $column;
        }
        //Unset định nghĩa column từ người dùng
        if(isset($this->option['columns']))
            unset($this->option['columns']);
        //Option columns hiển thị trong scripts
        $joptions['columns'] = $columns;
        //Options
        if(is_array($this->option))
            $joptions = array_merge($joptions, $this->option);

        //Asset
        $asset = new XPHP_Asset();
        
        //Script block
        $script  = "$(document).ready(function() {";
        $script  .= "$('#{$this->id}').kendoGrid(";
        $script  .= json_encode($joptions);
        $script  .= ");";
        $script  .= "});";
        $asset->registerScript($script);
            
        //Assets JS and CSS
        if(! $asset->hasAsset("KendoUI", 'js'))
        {
            $asset->name("KendoUI")->loadJs(array(
            					"Assets/js/kendo.core.js",
                                "Assets/js/kendo.fx.js",
                                "Assets/js/kendo.model.js",
                                "Assets/js/kendo.data.odata.js",
                                "Assets/js/kendo.data.js",
                                "Assets/js/kendo.pager.js",
                                "Assets/js/kendo.sortable.js",
                                "Assets/js/kendo.popup.js",
                                "Assets/js/kendo.list.js",
                                "Assets/js/kendo.dropdownlist.js",
                                "Assets/js/kendo.calendar.js",
                                "Assets/js/kendo.datepicker.js",
                                "Assets/js/kendo.numerictextbox.js",
                                "Assets/js/kendo.filtermenu.js",
                                "Assets/js/kendo.draganddrop.js",
                                "Assets/js/kendo.groupable.js",
                                "Assets/js/kendo.grid.js"));
            $asset->getJs();
        }
        //Assets css
        if(! $asset->hasAsset("KendoUI", 'css'))
        {
            $asset->loadCss(array("Assets/css/kendo.common.css"));
            $asset->getCss();
        }
    }
}