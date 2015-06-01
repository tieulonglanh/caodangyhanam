$(document).ready(function () {
    onfocus();
    $(".on_off_checkbox").iphoneStyle();
    $('.tip a ').tipsy({gravity: 'sw'});
    $('#login').show().animate({   opacity: 1 }, 2000);
    $('.logo').show().animate({   opacity: 1,top: '32%'}, 800,function(){
        $('.logo').show().delay(1200).animate({   opacity: 1,top: '1%' }, 300,function(){
            $('.formLogin').animate({   opacity: 1,left: '0' }, 300);
            $('.userbox').animate({ opacity: 0 }, 200).hide();
        });
    });
    $('.userload').click(function(e){
        $('.formLogin').animate({   opacity: 1,left: '0' }, 300);
        $('.userbox').animate({ opacity: 0 }, 200,function(){
            $('.userbox').hide();
        });
    });
    $('#btnLogin').click(eventSubmit);
    $('#formLogin').submit(eventSubmit);
});

function eventSubmit(e){
    e.preventDefault();
    if($('#username').val() == "" || $('#password').val() == ""){
        showError("Hãy nhập vào Tên đăng nhập / Mật khẩu");
        $('.inner').jrumble({ x: 4,y: 0,rotation: 0 });
        $('.inner').trigger('startRumble');
        setTimeout('$(".inner").trigger("stopRumble")',500);
        setTimeout('hideTop()',5000);
        return false;
    }
    hideTop();
    loading('Kiểm tra', true);
    login();
}
																 
function login(){
    $.ajax({
        dataType: 'json',
        type: 'post',
        data: $('#formLogin').serialize(),
        success: function(res){
            setTimeout(function(){
                unloading();
                if(res.success){
                    $("#login").animate({   opacity: 1,top: '49%' }, 200,function(){
                        $('.userbox').show().animate({ opacity: 1 }, 500);
                        $("#login").animate({   opacity: 0,top: '60%' }, 500,function(){
                            $(this).fadeOut(200,function(){

                                $(".text_success").slideDown();
                                $("#successLogin").html(res.message);
                                $("#successLogin").animate({opacity: 1,height: "200px"},500);
                                setTimeout( "window.location.href='" + res.url + "'", 3000 );

                            });
                        });
                    });
                }
                else{
                    showError(res.message);
                }
            }, 2500 );
        }
    });
}

$('#alertMessage').click(function(){
	hideTop();
});

function showError(str){
	$('#alertMessage').addClass('error').html(str).stop(true,true).show().animate({ opacity: 1,right: '0'}, 500);
}

function showSuccess(str){
	$('#alertMessage').removeClass('error').html(str).stop(true,true).show().animate({ opacity: 1,right: '0'}, 500);	
}

function onfocus(){
    if($(window).width()>480){
        $('.tip input').tipsy({ trigger: 'focus', gravity: 'w' ,live: true});
    }else{
	    $('.tip input').tipsy("hide");
	}
}

function hideTop(){
	$('#alertMessage').animate({ opacity: 0,right: '-20'}, 500,function(){ $(this).hide(); });	
}	

function loading(name, overlay){
    $('body').append('<div id="overlay"></div><div id="preloader">'+name+'..</div>');
	if(overlay){
		$('#overlay').css('opacity',0.1).fadeIn(function(){  $('#preloader').fadeIn();	});
		return false;
    }
	$('#preloader').fadeIn();
 }
 
function unloading(){
    $('#preloader').fadeOut('fast',function(){ $('#overlay').fadeOut(); });
}