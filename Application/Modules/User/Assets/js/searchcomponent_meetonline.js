function backpage(){
	p = parseInt($('#page').val());
	if(p == 1)
		return false;
	$.ajax({
	    url: '/User/SearchComponent/meetOnline?page=' + (p - 1),
	    type: 'GET',
	    success: function(dt){
	        $('.online-list').html(dt);
	    }
    });
}
function nextpage(){
	p = parseInt($('#page').val());
	mp = parseInt($('#maxpage').val());
	if(p + 1 > mp)
		return false;
	$.ajax({
	    url: '/User/SearchComponent/meetOnline?page=' + (p + 1),
	    type: 'GET',
	    success: function(dt){
	        $('.online-list').html(dt);
	    }
    });
}