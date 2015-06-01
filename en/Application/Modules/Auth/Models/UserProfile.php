<?php
/**
 * Lớp Model của User Profile
 * @author Mr.UBKey
 */
#[Table('user_profile')]
#[PrimaryKey('id')]
class UserProfile extends XPHP_Model
{
	public $id;
	
	public $bla_id;
}