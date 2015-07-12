$(function(){
	$(".albox.loading").ajaxSuccess(function(){
		$(this).hide();
	});
	$(".albox.loading").ajaxStart(function(){
		$(".albox").hide();
		$(".albox.loading").show();
	});
	$(".close.tips").click(function(){
		$(this).parents(".albox").hide();
	});
	
	$('form').submit(function(e){
		e.preventDefault();
		if($("form").valid())
		{
    		$.ajax({
    			data: {
    				email: $('#email').val(),
    				password: $('#password').val(),
    				remember: $('#remember').attr('checked')
    			},
    			dataType: 'json',
    			type: 'post',
    			success: function(res){
    				$(".albox").hide();
    				if(res.success)
    					window.location.href = res.url;
    				else{
    					$('.warningbox span').html(res.message);
    					$('.warningbox').fadeIn();
    				}
    			}
    		});
		}
	});
});