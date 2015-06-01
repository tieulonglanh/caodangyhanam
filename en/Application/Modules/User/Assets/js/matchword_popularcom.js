function addPopularMatchWordToMyProfile(t, e)
{
    $.ajax({
        url: $(e).attr('href'),
        dataType: 'json',
        type: 'post',
        data: { tag: t },
        success: function(res){
            if(!res.success)
                alert(res.message);
        }
    });
    return false;
}
$(function(){
	$('.addpop').tipsy();
});