        $(function(){
        	loadImage();
            //Bottom paging
            $(window).scroll(function(){
                if ($(window).scrollTop() == $('#main-content').height() - $(window).height()){
                	loadNewPage();
                }
            }); 
        });

        function loadImage()
        {
        	$('.avatar').lazyload();
        }
        
        function loadNewPage() 
        {
            if($('#endpage').val() == '1')
                return false;
            
            $('.paging-loading').show();
            $.ajax({
                url: $('#pagingUrl').val(),
                data: {
                    seeking_gender: $('#seeking_gender').val(),
                    page: parseInt($('#page').val()) + 1,
                    age_max: $('#age_max').val(),
                    age_min: $('#age_min').val(),
                    province: $('#province').val(),
                    avatar: $('#avatar').val()
                },
                type: 'POST',
                success: function(htmlData){
                	$('.paging-loading').hide();
                    if($.trim(htmlData) == "")
                    {
                        $('#endpage').val(1);
                        return false;
                    }
                    else
                    {
                        $('.list-search-user').append(htmlData);
                        loadImage();
                        //tang page them 1
                        $('#page').val(parseInt($('#page').val()) + 1); 
                    }
                }
            });
        }

        function changeSeekingGender(g)
        {
            if(g == parseInt($('#seeking_gender').val())){
            	if(window.location.href.indexOf('seeking_gender=') >= 0)
            		window.location.href = window.location.href.replace(/[\?\&]seeking_gender=([-]*\d)/, '');   
            }
            else{
            	$('#seeking_gender').val(g);
                seekingGender();
            }
        }
        
        function seekingGender()
    	{
    	    if(window.location.href.indexOf('seeking_gender=') >= 0)
    	    {
    	        window.location.href = window.location.href.replace(/seeking_gender=([-]*\d)/, 'seeking_gender=' + $('#seeking_gender').val());
            }
            else
            {
                if(window.location.href.indexOf('?') >= 0)
                    window.location.href = window.location.href + '&seeking_gender=' + $('#seeking_gender').val();
                else
                	window.location.href = window.location.href + '?seeking_gender=' + $('#seeking_gender').val();
            }
    	}
    	function minAge()
    	{
    		if(window.location.href.indexOf('age_min=') >= 0)
    	    {
    	        window.location.href = window.location.href.replace(/age_min=(\d+)/, 'age_min=' + $('#age_min').val());
            }
            else
            {
                if(window.location.href.indexOf('?') >= 0)
                    window.location.href = window.location.href + '&age_min=' + $('#age_min').val();
                else
                	window.location.href = window.location.href + '?age_min=' + $('#age_min').val();
            }
        }
    	function maxAge()
    	{
    		if(window.location.href.indexOf('age_max=') >= 0)
    	    {
    	        window.location.href = window.location.href.replace(/age_max=(\d+)/, 'age_max=' + $('#age_max').val());
            }
            else
            {
                if(window.location.href.indexOf('?') >= 0)
                    window.location.href = window.location.href + '&age_max=' + $('#age_max').val();
                else
                	window.location.href = window.location.href + '?age_max=' + $('#age_max').val();
            }
        }
        function province()
        {
        	if(window.location.href.indexOf('province=') >= 0)
    	    {
    	        window.location.href = window.location.href.replace(/province=(\d+)/, 'province=' + $('#province').val());
            }
            else
            {
                if(window.location.href.indexOf('?') >= 0)
                    window.location.href = window.location.href + '&province=' + $('#province').val();
                else
                	window.location.href = window.location.href + '?province=' + $('#province').val();
            }
        }
        function onlineFilter()
        {
            if($('#online').attr('checked'))
            {
                if(window.location.href.indexOf('online=') >= 0)
                {
                	window.location.href = window.location.href.replace(/online=(\d)/, 'online=1');
                }
                else
                	window.location.href = window.location.href + '&online=1';
            }
            else
            {
            	if(window.location.href.indexOf('online=') >= 0)
                {
            		window.location.href = window.location.href.replace(/online=(\d)/, 'online=0');
                }
                else
                    window.location.href = window.location.href.replace('&online=1', '');
            }
            return false;
        }