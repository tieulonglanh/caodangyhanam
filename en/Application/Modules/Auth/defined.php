<?php
$country = array(
'1' => "Việt Nam"
);

$province = array(
	'1' => array(
	    0 =>    "Tỉnh / Thành phố",	
		1 =>	"Hà Nội",
		2 =>	"Hồ Chí Minh",
		3 =>	"An Giang",
		4 =>	"Bà Rịa-Vũng Tàu",
		5 =>	"Bắc Giang",
		6 =>	"Bắc Kạn",
		7 =>	"Bạc Liêu",
		8 =>	"Bắc Ninh",
		9 =>	"Bến Tre",
		10 =>	"Bình Ðịnh",
		11 =>	"Bình Dương",
		12 =>	"Bình Phước",
		13 =>	"Bình Thuận",
		14 =>	"Cà Mau",
		15 =>	"Cần Thơ",
		16 =>	"Cao Bằng",
		17 =>	"Ðà Nẵng",
		18 =>	"Ðắc Lắk",
		19 =>	"Ðồng Nai",
		20 =>	"Ðồng Tháp",
		21 =>	"Gia Lai",
		22 =>	"Hà Giang",
		23 =>	"Hà Nam",
		24 =>	"Hà Tĩnh",
		25 =>	"Hải Dương",
		26 =>	"Hải Phòng",
		27 =>	"Hòa Bình",
		28 =>	"Hưng Yên",
		29 =>	"Khánh Hòa",
		30 =>	"Kiên Giang",
		31 =>	"Kon Tum",
		32 =>	"Lai Châu",
		33 =>	"Lâm Ðồng",
		34 =>	"Lạng Sơn",
		35 =>	"Lào Cai",
		36 =>	"Long An",
		37 =>	"Nam Ðịnh",
		38 =>	"Nghệ An",
		39 =>	"Ninh Bình",
		40 =>	"Ninh Thuận",
		41 =>	"Phú Thọ",
		42 =>	"Phú Yên",
		43 =>	"Quảng Bình",
		44 =>	"Quảng Nam",
		45 =>	"Quảng Ngãi",
		46 =>	"Quảng Ninh",
		47 =>	"Quảng Trị",
		48 =>	"Sóc Trăng",
		49 =>	"Sơn La",
		50 =>	"Tây Ninh",
		51 =>	"Thái Bình",
		52 =>	"Thái Nguyên",
		54 =>	"Thanh Hóa",
		55 =>	"Thừa Thiên-Huế",
		56 =>	"Tiền Giang",
		57 =>	"Trà Vinh",
		58 =>	"Tuyên Quang",
		59 =>	"Vĩnh Long",
		60 =>	"Vĩnh Phúc",
		61 =>	"Yên Bái"
	)	
);

//Ngày
$date = array();
$date[0] = "Ngày";
for($i=1; $i<32; $i++)
{
	$date[$i] = $i;
}

//Tháng
$month = array(
		'0'	 => "Tháng",
		'1' => "Tháng Một",
		'2' => "Tháng Hai",
		'3' => "Tháng Ba",
		'4' => "Tháng Tư",
		'5' => "Tháng Năm",
		'6' => "Tháng Sáu",
		'7' => "Tháng Bảy",
		'8' => "Tháng Tám",
		'9' => "Tháng Chín",
		'10' => "Tháng Mười",
		'11' => "Tháng Mười Một",
		'12' => "Tháng Mười Hai"
);
		
//Năm
$year = array();
$year[0] = "Năm";
for($i=1975; $i<1998; $i++)
{
	$year[$i] = $i;
}