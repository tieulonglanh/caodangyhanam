//Broadcast socket
var broadcastSocket = io.connect(config.service + ':8800/broadcast');

broadcastSocket.on('connect', function(){
	broadcastSocket.emit('authenticate', user);
	broadcastSocket.on('broadcast connect', function (status) {
		if(status.success){
			broadcastSocket.emit('broadcast get', {}, function(r){
			    setInterval(function(){
				    var data = {
						    ftime: $('.broadcast-list').data('ftime')
				    };
			    	broadcastSocket.emit('broadcast get', data, function(r){
				    });
				}, 5000);
			});
		}
    });
});
broadcastSocket.on('broadcast receive', function(res){
	if(res.success)
	{
		e = $('.sidebar');
		var output = "";
		for(i=0; i<res.broadcasts.length; i++)
		{
			res.broadcasts[i]['cdate']   = formatDateTime(res.broadcasts[i].created_date);
			res.broadcasts[i]['bla_id_sub'] = changeAvatar(res.broadcasts[i].bla_id_sub);
			renderData = {
					broadcasts: res.broadcasts[i]
			};
			if($('.broadcast-item#' + res.broadcasts[i]._id).length == 0){
				output += Mustache.to_html($('#broadcast_message_template').html(), renderData);
			}
		}
		output = $(output);
		output.filter('.broadcast-item').addClass('new').prepend('<div class="new-flag"></div>');
		if(res.direct == 1)
		    e.find('.broadcast-list').prepend(output);
		else
			e.find('.broadcast-list').append(output);
		jQuery("abbr.timeago").timeago();
		e.find('.mess').emotion();
		e.find('.broadcast-list .hidden').slideDown();
		//Cập nhật lại ftime và ltime;
		if(res.ftime)
		    e.find('.broadcast-list').data('ftime', res.ftime);
		if(res.ltime)
		    e.find('.broadcast-list').data('ltime', res.ltime);
	    //Scroller
		$('.broadcast-list-wrapper').nanoScroller();
	}
	else
		$.msgbox(res.message, {type:"alert"});
});
broadcastSocket.on('broadcast live', function(res){
	if(res.ready){
	    n = res.broadcast;
	    res.broadcast['bla_id_sub'] = changeAvatar(res.broadcast['bla_id_sub']);
	    var title = "<b>" + n.bla_id_sub.firstname + " " + n.bla_id_sub.lastname + "</b>: " + n.message;
	    var image = 'http://static.bla.vn/Avatar/get/' + n.bla_id_sub.avatar + '/48/48';
	    var link  = 'http://bla.vn/' + n.bla_id_sub.username;
		$.gritter.add({
			title	: 'Đài phát thanh',
			text	: title,
			image	: image,
			sticky	: false,
			time	: 6000,
			link    : link,
			before_open: function(){
                if($('.gritter-item-wrapper').length == 5)
                {
                    // Returning false prevents a new gritter from opening
                    return false;
                }
            }
		});
	}
});

function postBroadcast(e){
	e = $(e).parents('.sidebar');
	data = {
		message: e.find('[id=txtBroadcastMessage]').val()
	};
	broadcastSocket.emit('broadcast post', data, function (res) {
		if(res.success)
		{
			//Insert content
			res.broadcasts['cdate']   = formatDateTime(res.broadcasts.created_date);
			res.broadcasts['bla_id_sub'] = changeAvatar(res.broadcasts.bla_id_sub);
			var output = Mustache.to_html($('#broadcast_message_template').html(), res);
			e.find('.broadcast-list').prepend(output);
			jQuery("abbr.timeago").timeago();
			e.find('.mess').emotion();
			e.find('.broadcast-list .hidden').show();
			//Scroller
			$('.broadcast-list-wrapper').nanoScroller();
		}
		else
			$.msgbox(res.message, {type:"alert"});
	});
	e.find('[id=txtBroadcastMessage]').val('');
	return false;
}