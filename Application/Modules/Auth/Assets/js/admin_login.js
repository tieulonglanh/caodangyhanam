$(function(){
	/* SPECIFIC FOR LOGIN FORM */
	/* when user enters something in form, show login button */
  	$('#login input').keypress(function(){
  		rel_img = $('#login-btn').attr("rel");
  		$('#login-btn').slideDown('fast').css({'background':'url(' + rel_img + ')', 'display':'block'});
  	})
  	/* when input value is the defaul value, hide login button */
  	.blur(function(){
  		if( $(this).val() == this.defaultValue){
  			$('#login-btn').slideUp('fast');
  		}
  	});
	/* Minimal form validation - check if input values are empty */
 	$('#login').submit(function(e){
 		//Cancel event
 		e.preventDefault();
 		
 		rel_img_off = $('#login-btn').attr("reloff");
  		var username = $.trim($('#username').val()),
  		password = $.trim($('#password').val());
  		if ((username == '' ) || (username == 'Tên đăng nhập') || (password == '') || (password == 'Mật khẩu')) {
  			/* if error, change power button background image, shake it and make it disappear after a delay */
  			$('#login-btn').css('background', 'url(' + rel_img_off + ')').effect('shake', {distance: 10, times:5 }, 30);
  			return false;
  		}
  		//Call ajax
  		data = {
  			username: $("#username").val(),
  			password: $("#password").val(),
  			remember: $("#remember").val(),
  			language: $("#language").val()
  		};
  		//Show loading
  		$(".alert-info").slideDown();
  		//Hide error message
  		$(".alert-error").slideUp();
  		$.ajax({
  			type: "POST",
  			dataType: "json",
  			data: data,
  			success: function(res){
  				$(".alert-info").slideUp();
  				if(res.success){
  					window.location.href = res.url;
  				}
  				else{
  					$("#loginMessage").text(res.message);
  					$(".alert-error").slideDown();
  				}
  			}
  		});
  	});

	/* SPECIFIC FOR REGISTRATION FORM */
	/* when user enters something in form, show register button */
  	$('#register input').keypress(function(){
  		$('#register-btn').slideDown('fast').css({'background':'url(img/btn-power-off.png)', 'display':'block'});
  	})
  	/* when input value is the defaul value, hide register button */
  	.blur(function(){
  		if( $(this).val() == this.defaultValue){
  			$('#register-btn').slideUp('fast');
  		}
  	});

	
  	/* Minimal form validation - check if input values are empty */
 	$('#register').submit(function(){
  			var username = $.trim($('#r-username').val()),
  			email = $.trim($('#r-email').val()),
  			password = $.trim($('#r-password').val());
  			if ((username == '' ) || (username == 'Username') || (password == '') || (password == 'Password') || (email == '') || (email == 'Email')) {
  				/* if error, change power button background image and shake it and make it disappear after a delay*/
  				$('#register-btn').css('background', 'url(img/no-entry.png)').effect('shake', {distance: 10, times:5 }, 30);
  				return false;
  			}
  	});
});