<?php
/**
 * Lớp Model của đăng nhập
 * @author Mr.UBKey
 */
class MemberLogin extends XPHP_Model
{
	#[Label('Tên đăng nhập')]
	#[Required(message = 'Email không được để trống')]
	#[RangeLength(min = 3, max = 30, message = 'Tên đăng nhập có 3 ~ 30 kí tự')]
	public $username;
	
	#[Label('Mật khẩu')]
	#[Required(message = 'Mật khẩu không được để trống')]
	#[MinLength(3, message='Mật khẩu có tối thiểu 3 kí tự')]
	public $password;
	

	#[Label('Ghi nhớ')]
	public $remember;
}