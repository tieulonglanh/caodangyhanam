$(function(){
	$("#txtMatchWord").autocomplete($('#allword').val(), {
		dataType: 'json',
		minChars: 0,
		max: 20,
		autoFill: false,
		matchContains: true,
		scrollHeight: 220,
		parse: function(data) {
			return $.map(data, function(row) {
				return {
					data: row,
					value: row.id,
					result: row.tag
				}
			});
		},
		formatItem:   function(row, i, max) {
			return row.tag;
		},
		formatResult: function(row) {
			return row.id;
		}
	});

	$('.worddelete').hover(function(){
		$(this).parents('li').addClass('hover');
	});

	$('.worddelete').mouseout(function(){
		$(this).parents('li').removeClass('hover');
	});
	
	checkApprovedShowHide();
});
function worddeleteclick(e){
	if(confirm('Bạn có chắc muốn xoá từ khoá này không ?'))
	{
		durl = $(e).parents('li').attr('rel');
		tid = $(e).parents('li').attr('id');
		e = $(e);
	    $.ajax({
		    url: durl,
		    dataType: 'json',
		    type: 'post',
		    data: { tag_id: tid },
		    success: function(res){
		        if(res.success){
			        $(e).parents('li').fadeOut();
			        checkApprovedShowHide();
			    }
		    }
		});
	}
	else
		return false;
}

function addMatchWord(e)
{
	$.ajax({
	    url: $(e).attr('rel'),
	    dataTye: 'json',
	    type: 'post',
	    data: {
	        tag: $('#txtMatchWord').val()
	    },
	    success: function(res){
	        if(res.success)
	        {
		        html =  '<li id="' + res.tag.id + '" rel="' + res.deleteurl + '"><img src="/Content/4love/images/icon_delete_matchword.png" alt="Xoá" title="Xóa" class="btn"  onclick="return worddeleteclick(this);" />&nbsp; ';
		        html += '<a href="' + res.tag.url + '">' + res.tag.title + '</a></li>';
		        if(res.tag.approved == '1')
		            $('#ulSelfWords').append(html);
		        else
		        	$('#ulPendingWords').append(html);

		        checkApprovedShowHide();
		    }
	        else
		        alert(res.message);
	    }
	});
}

function checkApprovedShowHide()
{
	if($('#divPendingLable ul li').length > 0){
		$('#divPendingLable').show();
	}
	else
		$('#divPendingLable').hide();
}