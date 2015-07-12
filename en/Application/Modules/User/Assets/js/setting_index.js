$(function(){
	$(".close.tips").click(function(){
		$(this).parents(".albox").fadeOut();
	});
	$('#changePass').submit(function(e){
		e.preventDefault();
		if($("#changePass").valid())
		{
			$(".albox").hide();
			$(".albox.loading").show();
    		$.ajax({
    			data: {
    				oldPassword: $('#oldPassword').val(),
    				newPassword: $('#newPassword').val(),
    				retypeNewPassword: $('#retypeNewPassword').val()
    			},
    			dataType: 'json',
    			type: 'post',
    			success: function(res){
    				$(".albox.loading").hide();
    				$(".albox").hide();
    				if(res.success){
    					$('.succesbox span').html(res.message);
    					$('.succesbox').show();
    				}
    				else{
    					$('.warningbox span').html(res.message);
    					$('.warningbox').show();
    				}
    			}
    		});
		}
	});
});