    	function sortUser()
    	{
    	    if(window.location.href.indexOf('sort=') >= 0)
    	    {
    	        window.location.href = window.location.href.replace(/sort=(\d)/, 'sort=' + $('#sort').val());
            }
            else
            {
            	if(window.location.href.indexOf('?') >= 0)
            		window.location.href = window.location.href + '&sort=' + $('#sort').val();
                else
                	window.location.href = window.location.href + '?sort=' + $('#sort').val();
            }
                
    	}
        function onlinefilter()
        {
            if($('#online').attr('checked'))
            {
                if(window.location.href.indexOf('online=') >= 0)
                {
                	window.location.href = window.location.href.replace(/online=(\d)/, 'online=1');
                }
                else
                {
                	if(window.location.href.indexOf('?') >= 0)
                		window.location.href = window.location.href + '&online=1';
                    else
                	    window.location.href = window.location.href + '?online=1';
                }
            }
            else
            {
            	if(window.location.href.indexOf('online=') >= 0)
                {
            		window.location.href = window.location.href.replace(/online=(\d)/, 'online=0');
                }
                else
                {
                	if(window.location.href.indexOf('?') >= 0)
                		window.location.href = window.location.href + '&online=0';
                    else
                	    window.location.href = window.location.href + '?online=0';
                }
            }
            return false;
        }
        function addMatchWordToMyProfile(t, e)
        {
        	$.ajax({
        	    url: $(e).attr('href'),
        	    dataType: 'json',
        	    type: 'post',
        	    data: { tag: t },
        	    success: function(res){
        	        if(!res.success)
            	       alert(res.message);
                }
        	});
        	return false;
        }