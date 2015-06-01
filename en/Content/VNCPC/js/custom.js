
//var timer = setInterval( showNews, 3000);
function showNews($title) {
	if(!$title.hasClass('active')){
		$title.addClass('active');
		$title.animate({ 
			'margin-top': '-146px',
		}, 600,'easeOutCirc');
	}else{
		$title.removeClass('active');
		$title.animate({ 
			'margin-top': '16px',
		}, 600,'easeOutCirc');
	}
}
function loop($title) {
            var rand = Math.round(Math.random() * (10000 - 5000)) + 5000;
	setTimeout(function() {
		showNews($title);
		loop($title);
	}, rand);
};


$(document).ready(function(){
	$('.indexContent .secondContent .blockTitle').each(function(){
		loop($(this));
	});
});
