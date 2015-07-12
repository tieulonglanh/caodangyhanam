<?php
/**
 * Lớp Model Register
 * @author Mr.UBKey
 */
class Register extends XPHP_Model
{
	#[Required(message='Mời bạn nhập vào địa chỉ email')]
	#[Email(message='Địa chỉ email chưa chính xác')]
	public $email;

	#[Required(message='Mời bạn nhập vào mật khẩu')]
	#[RangeLength(min = 6, max = 50, message = 'Mật khẩu có 6 ~ 50 kí tự')]
	public $password;

	#[Required(message='Mời bạn nhập vào họ')]
	#[MaxLength(10, message = 'Họ có tối đa 10 kí tự')]
	public $firstname;
	
	#[Required(message='Mời bạn nhập vào tên')]
	#[MaxLength(25, message = 'Tên có tối đa 25 kí tự')]
	public $lastname;

	public $gender;
	
	#[Required]
	public $date;
	
	#[Required]
	public $month;
	
	#[Required]
	public $year;
	
	#[Required]
	public $province;
}