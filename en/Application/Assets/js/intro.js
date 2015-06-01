$.tools.validator.localize("vi", {
	  ':email'  		: 'Thông tin email nhập chưa chính xác',
	  ':number' 		: 'Giá trị nhập vào bắt buộc là số',
	  ':radio'			: 'Bạn hãy lựa chọn',
	  '[max]'	 		: 'Giá trị nhập vào không quá $1',
	  '[min]'	 		: 'Giá trị nhập vào không dưới $1',
	  '[required]' 		: 'Bạn hãy nhập thông tin vào ô',
	  '[pattern]'		: 'Chỉ cho phép các chữ ở đầu và sau đó là số',
	  '[maxlength]'	    : 'Hãy nhập vào tối đa $1 kí tự',
});
$.tools.validator.fn("[minlength]", function(input, value) {
    var min = input.attr("minlength");
    return value.length >= min ? true : {
    	vi: "Hãy nhập vào tối thiểu " +min+ " kí tự",
    	en: "Please provide at least " +min+ " character" + (min > 1 ? "s" : "")
    };
});
$.tools.validator.fn("[name=lastname]", function(el, value){
	if($("[name='firstname']").val().length == 0)
		return "Hãy nhập họ của bạn";
	if($("[name='lastname']").val().length == 0)
		return "Hãy nhập tên của bạn";
	return true;
});
$.tools.validator.fn("[name=email]", function(el, value){
	valid = false;
	$.ajax({
		async: false,
		url: $(el).attr('validUrl'),
		type: 'POST',
		dataType: 'json',
		data:{
			email: value
		},
		success: function(data){
			if(data.success)
				valid = true;
			else
				valid = data.message;
		}
	});
	return valid;
});
$.tools.validator.fn("select[name=province]", function(el, value){
	return parseInt(value) > 0 ? true : "Hãy lựa chọn nơi ở của bạn";
});
$.tools.validator.fn("select[name=year]", function(el, value){
	y = $(el);
	m = el.parents('form').find('select[name=month]');
	d = el.parents('form').find('select[name=date]');
	if(parseInt(y.val()) > 0 && parseInt(m.val()) > 0 && parseInt(d.val()) > 0)
		return true;
	else
		return "Hãy lựa chọn ngày sinh của bạn";
});
$(function(){
	setInterval(changeSlideBackground, 5000);
	$('input[placeholder], textarea[placeholder]').placeholder();
	$('.login-circuit .login-content #formLogin').validator({lang: 'vi'}).submit(function(e){
		var form = $(this);
		// client-side validation OK.
		if (!e.isDefaultPrevented()) {
			form.find('.submit-area').hide();
			form.find('.loading-area').show();
			$.ajax({
				url: form.attr('action'),
				type: 'POST',
				dataType: 'json',
				data: form.serialize(),
				success: function(res){
					form.find('.loading-area').hide();
					form.find('.submit-area').show();
					if(res.success){
						window.location.href = res.url;
					}
					else if(typeof(res.message) != 'undefined'){
						alert(res.message);
					}
					else{
						form.data("validator").invalidate(res.errors);
					}	
				}
			});
			// prevent default form submission logic
			e.preventDefault();
		}
	});
});
function changeSlideBackground()
{
	currentSlide = $('.slide-background .slide.active');
	currentRel = parseInt(currentSlide.attr('rel'));
	if(currentRel == 4)
		nextRel = 1;
	else
		nextRel = currentRel + 1;
	nextSlide = $('.slide-background .slide.s' + nextRel);
	//Fadeout slide cu
	currentSlide.fadeOut('slow', function(){
		currentSlide.css({ zIndex: parseInt(currentSlide.css('zIndex')) - 4, display: 'block'});
		currentSlide.removeClass('active');
	});
	//Gan slide moi trang thai active
	nextSlide.addClass('active');
}
function openRegisterBox()
{
	$('.error').remove();
	$('.register-circuit, .login-circuit').fadeOut('slow', function(){
		$('.register-circuit .register-content, .login-circuit .login-content').html('').fadeOut();
		$('.register-circuit').css({backgroundColor: '#fff', top: '210px', right: '220px', width: '0px', height: '0px'});
		$('.register-circuit').fadeIn();
		$('.register-circuit').animate({
			width: "440px",
			height: "440px",
			top: "60px",
			right: "71px",
			zIndex: 120
		}, 500, function(){
			$('.register-circuit').animate({
				width: "400px",
				height: "400px",
				top: "80px",
				right: "90px",
				zIndex: 120
			}, 500, function(){
				//show content register
				$('.register-circuit .register-content').html($('.register-box-visible').html()).fadeIn();
				$('input[placeholder], textarea[placeholder]').placeholder();
				$('.register-circuit .register-content #formRegister').validator({lang: 'vi'}).submit(function(e){
					var form = $(this);
					// client-side validation OK.
					if (!e.isDefaultPrevented()) {
						form.find('.submit-area').hide();
						form.find('.loading-area').show();
						$.ajax({
							url: form.attr('action'),
							type: 'POST',
							dataType: 'json',
							data: form.serialize(),
							success: function(res){
								form.find('.loading-area').hide();
								form.find('.submit-area').show();
								if(res.success){
									window.location.href = res.url;
								}
								else if(typeof(res.message) != 'undefined'){
									alert(res.message);
								}
								else{
									form.data("validator").invalidate(res.errors);
								}	
							}
						});
						// prevent default form submission logic
						e.preventDefault();
					}
				});
			});
			//Show login box
			$('.login-circuit').css({backgroundColor: '#ff4600', top: '140px', right: '90px', width: '0px', height: '0px'});
			$('.login-circuit').fadeIn();
			$('.login-circuit').animate({
				width: "90px",
				height: "90px",
				top: "100px",
				right: "50px",
				zIndex: 110
			}, 500, function(){
				$('.login-circuit .login-content').html($('.login-box-invisible').html()).fadeIn();
			});
		});
	});
}
function openLoginBox()
{
	$('.error').remove();
	$('.register-circuit, .login-circuit').fadeOut('slow', function(){
		$('.register-circuit .register-content, .login-circuit .login-content').html('').fadeOut();;
		$('.login-circuit').css({backgroundColor: '#fff', top: '240px', right: '260px', width: '0px', height: '0px'});
		$('.login-circuit').fadeIn();
		$('.login-circuit').animate({
			width: "380px",
			height: "380px",
			top: "61px",
			right: "80px",
			zIndex: 120
		}, 500, function(){
			$('.login-circuit').animate({
				width: "340px",
				height: "340px",
				top: "80px",
				right: "100px",
				zIndex: 120
			}, 500, function(){
				//show content login
				$('.login-circuit .login-content').html($('.login-box-visible').html()).fadeIn();
				$('input[placeholder], textarea[placeholder]').placeholder();
				$('.login-circuit .login-content #formLogin').validator({lang: 'vi'}).submit(function(e){
					var form = $(this);
					// client-side validation OK.
					if (!e.isDefaultPrevented()) {
						form.find('.submit-area').hide();
						form.find('.loading-area').show();
						$.ajax({
							url: form.attr('action'),
							type: 'POST',
							dataType: 'json',
							data: form.serialize(),
							success: function(res){
								form.find('.loading-area').hide();
								form.find('.submit-area').show();
								if(res.success){
									window.location.href = res.url;
								}
								else if(typeof(res.message) != 'undefined'){
									alert(res.message);
								}
								else{
									form.data("validator").invalidate(res.errors);
								}	
							}
						});
						// prevent default form submission logic
						e.preventDefault();
					}
				});
			});
			//Show register box
			$('.register-circuit').css({backgroundColor: '#ff4600', top: '140px', right: '90px', width: '0px', height: '0px'});
			$('.register-circuit').fadeIn();
			$('.register-circuit').animate({
				width: "90px",
				height: "90px",
				top: "100px",
				right: "50px",
				zIndex: 110
			}, 500, function(){
				$('.register-circuit .register-content').html($('.register-box-invisible').html()).fadeIn();
			});
		});
	});
}
function getRandomNumber(min, max)
{
	return Math.floor( Math.random() * ( 1 + max - min ) ) + min;
}