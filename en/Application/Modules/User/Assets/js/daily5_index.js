    function interest(ele){
        item = $(ele);
        $.ajax({
		url: item.attr("href"),
				type: "post",
				data: {id: item.attr("okid"), current: item.attr('current')},
				success: function(data){
                                        $('#interested').html(data);
                                        $('#interested').append();
                                        $('#maybebutton').fadeOut('slow', function() {
                                            
                                        });
                                        $('#nobutton').fadeOut('slow', function() {
                                            
                                        });
                                        //$('#maybebutton').fadein('<a disabled="disabled"><img src="http://images.intl.match.com/sites/daily5/mil_D5_btn_maybe_1.jpg" ></a>');
                                        //$('#nobutton').fadein('<a disabled="disabled"><img src="http://images.intl.match.com/sites/daily5/mil_D5_btn_no_1.jpg" ></a>');
				}
			});
    }

    function listToogle(ele)
    {
        $('.people-list').slideToggle();
        if($(ele).parents('.title').hasClass('down')){
        	$(ele).parents('.title').removeClass('down');
            $(ele).parents('.title').addClass('up');
        }
        else{
        	$(ele).parents('.title').removeClass('up');
            $(ele).parents('.title').addClass('down');
        }
        return false;
    }