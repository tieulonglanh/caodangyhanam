//Activity socket
var activitySocket = io.connect(config.service + ':8800/activity');

activitySocket.on('connect', function(){
	activitySocket.emit('authenticate', user);
	activitySocket.on('activity connect', function (status) {
		if(status.success){
			activitySocket.emit('activity get', {}, function(r){
			    setInterval(function(){
				    var data = {
						    ftime: $('.activity-list').data('ftime')
				    };
				    activitySocket.emit('activity get', data, function(r){
				    });
				}, 5000);
			});
		}
    });
});
activitySocket.on('activity receive', function(res){
	if(res.success)
	{
		e = $('.sidebar');
		var output = "";
		for(var i=0; i<res.activities.length; i++)
		{
			res.activities[i]['cdate']   = formatDateTime(res.activities[i].created_date);
			res.activities[i]['bla_id_sub'] = changeAvatar(res.activities[i].bla_id_sub);
			renderData = {
					activities: res.activities[i]
			};
			if($('.activity-item#' + res.activities[i]._id).length == 0){
				output += Mustache.to_html($('#activity_message_template').html(), renderData);
			}
		}
		output = $(output);
		output.filter('.activity-item').addClass('new').prepend('<div class="new-flag"></div>');
		if(res.direct == 1)
		    e.find('.activity-list').prepend(output);
		else
			e.find('.activity-list').append(output);
		jQuery("abbr.timeago").timeago();
		e.find('.mess').emotion();
		e.find('.activity-list .hidden').slideDown();
		//Cập nhật lại ftime và ltime;
		if(res.ftime)
		    e.find('.activity-list').data('ftime', res.ftime);
		if(res.ltime)
		    e.find('.activity-list').data('ltime', res.ltime);
	    //Scroller
		$('.activity-list-wrapper').nanoScroller();
	}
	else
		$.msgbox(res.message, {type:"alert"});
});