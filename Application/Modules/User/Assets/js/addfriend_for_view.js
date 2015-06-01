function addFriendBox(e){
	e = $(e);
	$.ajax({
        url: e.data('url'),
        type: 'post',
        dataType: 'json',
        data: {
        	id: e.data('id')
        },
        success: function(res){
            if(res.success)
            	var html = $("<div><img src='/Content/HiU/images/box/popup_friend_success.png' /></div>");
            else
            	var html = $("<div><img src='/Content/HiU/images/box/popup_fail.png' /></div>");
            $.lightbox(html, {
            	modal   : false,
            	width   : 556,
                height  : 400
            });
        }
    });  
	return false;
}