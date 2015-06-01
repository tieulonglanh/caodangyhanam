$(function(){
	$(".close.tips").click(function(){
		$(this).parents(".albox").hide();
	});
	
	$('form').submit(function(e){
		e.preventDefault();
		if($("form").valid())
		{
			$(".albox").hide();
			$(".albox.loading").show();
    		$.ajax({
    			data: {
    				newPassword: $('#newPassword').val(),
    				retypeNewPassword: $('#retypeNewPassword').val(),
    				token: $('#token').val()
    			},
    			dataType: 'json',
    			type: 'post',
    			success: function(res){
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