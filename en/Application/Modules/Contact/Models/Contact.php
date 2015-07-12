<?php
/**
 * Lớp Model Contact
 * @author DungNV
 */
#[Table('contact')]
#[PrimaryKey('id')]
class Contact extends XPHP_Model
{

    #[Label('Mã')]
    #[Type('number')]
    public $id;

    #[Label('Họ và tên')]
    #[Required(Họ tên = 'Tiêu đề không được để trống')]
    #[MaxLength(250, message = 'Họ tên có tối đa 250 kí tự')]
    #[Type('string')]
    public $name;

    #[Label('Địa chỉ email')]
    #[Required(message='Mời bạn nhập vào địa chỉ email')]
    #[Email(message='Địa chỉ email chưa chính xác')]
    public $email;

    #[Label('Số điện thoại:')]
    #[Required(message = 'Số điện thoại không được để trống')]
    public $phone;

    #[Label('Địa chỉ')]
    public $address;

    #[Label('Thông tin thêm: ')]
    public $more_infor;

    #[Label('Tiêu đề: ')]
    public $title;

    #[Label('Tin nhắn: ')]
    #[Required(message = 'Tin nhắn không được để trống')]
    public $message;

    public $created_date;

}