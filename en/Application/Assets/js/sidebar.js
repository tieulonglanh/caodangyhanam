$(function() {
	$('.sidebar .broadcast .post-broadcast textarea').uniform();
	$('.sidebar .broadcast .post-broadcast textarea').placeholder();
    // setup ul.tabs to work as tabs for each div directly under div.panes
    $("ul.activity-tabs").tabs("div.panes > div", {effect: 'fade'});
    //setup height of tab
	$('.tab-wrapper').height($(window).height() - $('.broadcast').height() - 47);
	//setup splitter
	$("#activity").splitter({
		splitHorizontal: true,
		A: $('.broadcast-list-wrapper'),
		B: $('.activity-list-wrapper'),
		closeableto: 100
	});
	//Broadcast nanoscroller
	$('.broadcast-list-wrapper').nanoScroller({
	    preventPageScrolling: true
	});
	$(".broadcast-list-wrapper").bind("scrollend", function(e){
		$('.broadcast-list-wrapper .loading').show();
		var data = {
			    ltime: $('.broadcast-list').data('ltime')
	    };
		broadcastSocket.emit('broadcast get', data, function(r){
			$('.broadcast-list-wrapper .loading').hide();
		});
	});
	//Activity nanoscroller
	$('.activity-list-wrapper').nanoScroller({
	    preventPageScrolling: true
	});
	$('.activity-list-wrapper').bind("scrollend", function(e){
		$('.activity-list-wrapper .loading').show();
		var data = {
			    ltime: $('.activity-list').data('ltime')
	    };
		activitySocket.emit('activity get', data, function(r){
			$('.activity-list-wrapper .loading').hide();
		});
	});
});
