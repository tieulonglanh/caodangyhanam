function removeVisitor(vobj)
{
	$.ajax({
	    url: '/user/visitor/delete',
	    data: { id : $(vobj).attr('data') },
	    dataType: 'json',
	    type: 'post',
	    success: function (res){
	        if(res.success)
	        {
		        $(vobj).parents('.visitorbox').fadeOut();
		    }
	        else
		        alert(res.message);
	    }
	});

	return false;
}