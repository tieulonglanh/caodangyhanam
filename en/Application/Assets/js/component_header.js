         		        $(function(){
         		        	$('#user-function .notif-icon li a').click(function(){
         		        	    e = $(this);
         		        	    e.addClass('active');
          		        	    $('.' + e.attr('rel')).find('loading').show();
             		        	$.ajax({
             		        	    url: e.data('url'),
             		        	    type: 'post',
             		        	    dataType: 'json',
             		        	    success: function(data){
             		        	    	if(data.success){
             		        	    		$('.' + e.attr('rel')).find('loading').hide();
                     		        	    //console.log(data);
                     		        	    html = "";
                  		        	        $.each(data.data, function(k, v){
                 		        	            html += "<li class='btn' onclick=\"window.location.href='" + v.link + "'\">";
                  		        	            html += "<a class='avatar' style=\"background-image: url(\'" + v.sender.avatar + "\')\"></a>";
                  		        	            html += "<div class='info'>";
                    		        	        html += "<div class='username'>" + v.sender.username + "</div>";
                    		        	        html += "<div class='m-title'>" + v.subject + "<span> - " + v.time_str + "</span></div>";
                    		        	        html += "</div><div class='cl'></div>";
                 		        	            html += "</li>";
                          		            });
                    		        	    $('.' + e.attr('rel')).find('ul').html(html);
                    		        	    //Use nanoscroller
                    		        	    scroller($('.' + e.attr('rel')));
             		        	    	}
             		        	    	//Use nanoscroller
                		        	    scroller($('.' + e.attr('rel')));
                		        	    //Show
                		        	    $(".notification, .uavatar .dropdown-list").not("." + e.attr('rel')).fadeOut("fast");
                		        	    $('#user-function .notif-icon li a[rel!="' + e.attr('rel') + '"], .uavatar .avatar-dropdown').removeClass('active');
                		        	    $('.' + e.attr('rel')).fadeIn("fast");
                     		        }
                     		    });
         		        	    return false;
                 		    });
         		        	$(document).click(function(e) {  
        		        		 var $clicked = $(e.target);
        		        		 if (!$clicked.parents().hasClass('.notification')) {
        		        			$('#user-function .notif-icon li a').removeClass('active');
        		        		    $('.notification, ').fadeOut("fast");
        		        		 }
        		            });
         		        	checkCountNotif();
             		    });
         		        
             		    function checkCountNotif(){
              		        $.each($('#user-function .notif-icon li a'), function(k, v){
                		        if($(v).find('span').length > 0)
                		        {
                    		        sp = $(v).find('span');
                		            c = parseInt(sp.html());
                		            if(c > 0)
                		            {
                		            	sp.show();
                    		        }
                		            else
                		            	sp.hide();
                    		    }
                      		});
                 		}
             		    
             		    function scroller(e){
             		    	if(e.find('ul li').length <= 5){
             		    		var tth = 0;
             		    		$.each(e.find('ul li'), function(k, v){
             		    			tth += $(v).height() + 11;
             		    		});
             		    		e.find('.nano').height(tth);
             		    	}
             		    	else{
             		    		e.find('.nano').height(260);
             		    	}
             		    	e.find('.nano').nanoScroller();
             		    }