function change(e)
{
	item = $(e);
	$.ajax({
		url: item.attr("href"),
		type: "post",
		data: {},
		success: function(data){       
			if(item.attr("type")=="view1"){
				$("#listdetail").html(data);
				$("#listsmall").html('');
				$("#listgallery").html('');
			}

			if(item.attr("type")=="view2"){
				$("#listsmall").html(data);
				$("#listdetail").html('');
				$("#listgallery").html('');
			}

			if(item.attr("type")=="view3"){
				$("#listgallery").html(data);
				$("#listsmall").html('');
				$("#listdetail").html('');
			}
			$('.btnview').removeClass('active');
			$(item).addClass('active');
			$('.open-tipsy').tipsy();
		}            
	});
}