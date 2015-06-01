<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mc721
 * Date: 1/14/13
 * Time: 9:32 PM
 * To change this template use File | Settings | File Templates.
 */
class MemberRegister extends XPHP_Model
{
    #[Label('Tên đăng nhập:')]
    #[Required(message = 'Tên đăng nhập không được để trống')]
    #[RangeLength(min = 5, max = 20, message = 'Tên đăng nhập có 5 ~ 20 kí tự')]
    public $register_username;

    #[Label('Mật khẩu:')]
    #[Required(message = 'Mật khẩu không được để trống')]
    #[RangeLength(min = 6, max = 20, message = 'Mật khẩu có 6 ~ 20 kí tự')]
    public $register_password;

    #[Label('Nhập lại mật khẩu:')]
    #[Required(message = 'Nhập lại mật khẩu không được để trống')]
    #[RangeLength(min = 6, max = 20, message = 'Nhập lại mật khẩu có 6 ~ 20 kí tự')]
    public $register_repassword;

    #[Label('Email:')]
    #[Required(message = 'Email không được để trống')]
    #[Email(message = 'Định dạng email chưa chính xác')]
    public $register_email;

    #[Label('Họ và tên:')]
    #[Required(message = 'Họ và tên không được để trống')]
    public $register_fullname;

    #[Label('Số điện thoại:')]
    #[Required(message = 'Số điện thoại không được để trống')]
    public $register_phone;
}
