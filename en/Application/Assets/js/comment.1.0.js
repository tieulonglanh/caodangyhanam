var commentSocket = io.connect(config.service + ':8800/comment');

commentSocket.on('connect', function(){
	commentSocket.emit('authenticate', user);
	commentSocket.on('comment connect', function (status) {
		if(status.success)
		{
			
		}
    });
});

function postComment(e){
	eStatus = $(e).parents('.stt-content');
	data = {
		url: eStatus.data('url'),
		message: $(e).find('[name=txtComment]').val()
	};
	commentSocket.emit('comment post', data, function (res) {
		if(res.success)
		{
			//Update count
			eStatus.find('.comment-count').html(res.count);
			//Insert content
			res.comments['cdate']   = formatDateTime(res.comments.created_date);
			res.comments['user_id'] = changeAvatar(res.comments.user_id);
			var output = Mustache.to_html($('#comment_template').html(), res);
			eStatus.find('.comment-list').append(output);
			jQuery("abbr.timeago").timeago();
			$('.comment .comment-mess').emotion();
			eStatus.find('.comment-list .hidden').show();
			//Cap nhat lai thoi gian lan cuoi là thoi gian post bài
			eStatus.find('.comment-list').data('update', res.comments.created_date);
			//Dat lenh cap nhat comment sau moi 5s
			updateNewComment(eStatus, 5000);
		}
		else
			alert(res.message);
	});
	$(e).find('[name=txtComment]').val('');
	return false;
}

function showAllComment(e)
{
	eStatus = $(e).parents('.stt-content');
	data = {
		url: eStatus.data('url'),
		update: null,
		page: parseInt(eStatus.find('.comment-list').data('page'))
	};
	commentSocket.emit('comment get', data, function (res) {
		if(res.success)
		{
			for(i=0; i<res.comments.length; i++)
			{
				res.comments[i]['cdate']   = formatDateTime(res.comments[i].created_date);
				res.comments[i]['user_id'] = changeAvatar(res.comments[i].user_id);
			}
			var output = Mustache.to_html($('#comment_template').html(), res);
			if(data.page == 1)
			{
				eStatus.find('.comment-list').html(output);
				var date = new Date();
				eStatus.find('.comment-list').data('update', date.toUTCString());
				//Dat lenh cap nhat comment sau moi 5s
				updateNewComment(eStatus, 5000);
			}
			else
			{
				eStatus.find('.comment-list').prepend(output);
			}
			jQuery("abbr.timeago").timeago();
			$('.comment .comment-mess').emotion();
			eStatus.find('.comment-list .hidden').show();
			//Kiem tra tong so hien thi tren tong so comment
			if(res.end > 0)
			{
				var eCountDown = eStatus.find('.comment-count-down');
				eCountDown.find('.show-count').html(res.count - res.end);
				eCountDown.find('.comment-count').html(res.count);
				eCountDown.show();
				eStatus.find('.comment-cal').remove();
			}
			//Tang page len 1
			eStatus.find('.comment-list').data('page', data.page + 1);
			//Bo nut show all
			if(res.end == 0)
			{
				eStatus.find('.comment-cal').remove();
				eStatus.find('.comment-count-down').remove();
			}
		}
		else
			alert(res.message);
	});
}

function updateNewComment(e, t)
{
	eStatus = $(e);
	//Return false if has interval
	if(eStatus.data("interval"))
		return false;
	//Set interval with flag
	eStatus.data('interval', '1');
	setInterval(function() {
		//Socket
		data = {
			url: eStatus.data('url'),
			update: eStatus.find('.comment-list').data('update')
		};
		commentSocket.emit('comment get', data, function (res) {
			if(res && res.success)
			{
				var output = "";
				for(i=0; i<res.comments.length; i++)
				{
					res.comments[i]['cdate']   = formatDateTime(res.comments[i].created_date);
					res.comments[i]['bla_id_sub'] = changeAvatar(res.comments[i].bla_id_sub);
					renderData = {
						comments: res.comments[i]
					};
					if($('.comment#' + res.comments[i]._id).length == 0)
					{
						output += Mustache.to_html($('#comment_template').html(), renderData);
					}
				}
				output = $(output);
				output.filter('.comment').addClass('new').prepend('<div class="new-flag"></div>');
				//Dua vao list va hien thi len
				eStatus.find('.comment-list').append(output);
				jQuery("abbr.timeago").timeago();
				$('.comment .comment-mess').emotion();
				eStatus.find('.comment-list .hidden').show();
				//Update count
				eStatus.find('.comment-count').html(res.count);
			}
			//Update time
			var date = new Date();
			eStatus.find('.comment-list').data('update', date.toUTCString());
		});
	}, t);
}