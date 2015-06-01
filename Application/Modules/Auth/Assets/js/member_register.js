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
});
function begin(){
}
function success(res){
	$(".albox").fadeOut();
	if(res.success)
	{
		window.location.href = res.url;
	}
	else
	{
		$('.warningbox span').html(res.message);
		$('.warningbox').fadeIn();
	}
}