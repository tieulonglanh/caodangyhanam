var likeSocket = io.connect(config.service + ':8800/like');

likeSocket.on('connect', function(){
	likeSocket.emit('authenticate', user);
	likeSocket.on('like connect', function (status) {
		if(status.success)
		{
			
		}
    });
});

function postLike(e)
{
	eStatus = $(e).parents('.stt-content');
	data = {
		url: eStatus.data('url')
	};
	likeSocket.emit('like post', data, function (res) {
		if(res.success)
		{
			eStatus.parents('.status').find('.button-like').addClass('active');
			if(res.count == 0)
			{
				eStatus.find('.like-cal').html('Bạn ' + eStatus.find('.like-cal').html());
			}
			else
			{
				//Thêm và vào trước số người
				if(parseInt(eStatus.find('.f-more .like-count').html()) > 0)
				{
					eStatus.find('.f-more').html('và ' + eStatus.find('.f-more').html());
				}
				if(eStatus.find('.f-list a').length > 0)
				{
					eStatus.find('.like-cal').html('Bạn, ' + eStatus.find('.like-cal').html());
					$(v).find('.f-list').show();
				}
				else
				{
					eStatus.find('.like-cal').html('Bạn ' + eStatus.find('.like-cal').html());
				}
				if(res.count)
				{
					eStatus.find('.like-cal .like-count').html(numberFormat(res.count, 0, '.', '.'));
					eStatus.find('.f-more').show();
				}
			}	
			eStatus.find('.like-cal').show();
		}
		else
			alert(res.message);
	});
	return false;
}

//Show like if has
$(function(){
	$.each($('.like-cal'), function(k, v){
		if(parseInt($(v).find('.like-count').html()) > 0)
		{
			$(v).find('.f-more').show();
			$(v).show();
		}
		if($(v).html().indexOf('Bạn') >= 0)
		{
			$(v).show();
		}
		if($(v).find('.f-list a').length > 0)
		{
			$(v).find('.f-list').show();
			$(v).show();
		}
	});
});