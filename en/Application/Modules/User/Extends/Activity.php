<?php
#[Adapter('bla_social_content')]
#[Table('activities')]
#[PrimaryKey('_id')]
class Activity extends XPHP_Model
{
    public $bla_id_sub;
    
    public $bla_id;
    
    public $message;
    
    public $link;
    
    public $status;
    
    public $created_date;
}