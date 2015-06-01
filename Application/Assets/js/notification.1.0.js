var notificationSocket = io.connect(config.service + ':8800/notification');

notificationSocket.on('connect', function(){
	notificationSocket.emit('authenticate', user);
	notificationSocket.on('notification connect', function (status) {
		if(status.success){
			notificationSocket.emit('notification get', {});
		}
    });
});
notificationSocket.on('notification receive', function(notifications){
	$.each(notifications, function(k, v){
		$.gritter.add({
			title	: v.title,
			text	: v.message,
			image	: v.image,
			sticky	: v.sticky,
			time	: v.time,
			link    : v.link,
			before_open: function(){
                if($('.gritter-item-wrapper').length == 3)
                {
                    // Returning false prevents a new gritter from opening
                    return false;
                }
            },
            after_open: function(e){
            	notificationSocket.emit('notification alert_read', {id: v._id}, function(res){
            	    //if(res.success)
                });
            }
		});
	});
});