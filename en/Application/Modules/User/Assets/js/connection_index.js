function sortUser()
{
	if(window.location.href.indexOf('sort=') >= 0)
	{
		window.location.href = window.location.href.replace(/sort=(\d)/, 'sort=' + $('#sort').val());
	}else{
		if(window.location.href.indexOf('?') >= 0)
			window.location.href = window.location.href + '&sort=' + $('#sort').val(); 
		else
			window.location.href = window.location.href + '?page=1&sort=' + $('#sort').val();
	}


}    
function viewUser()
{
	if(window.location.href.indexOf('view1=') >=0)
	{
		window.location.href = window.location.href.replace(/view1=(\w)/, 'view1=' + $('#view1').val());
	}else{
		if(window.location.href.indexOf('?') >=0)
			window.location.href = window.location.href + '&view1=' + $('#view1').val();
		else
			window.location.href = window.location.href + '?page=1&view1=' + $('#view1').val();
	}

} 