<?php
#[Adapter('bla_social_content')]
#[Table('notifications')]
#[PrimaryKey('_id')]
class Notification extends XPHP_Model
{
    public $bla_id_sub;
    
    public $bla_id;
    
    public $title;
    
    public $message;
    
    public $image;
    
    public $link;
    
    public $sticky;
    
    public $time;
    
    public $created_date;
    
    public $alert_read;
    
    public $center_read;
}