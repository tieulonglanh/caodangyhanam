$(document).ready(function(){
	$('ol.sortable').nestedSortable({
		disableNesting: 'no-nest',
		forcePlaceholderSize: true,
		handle: 'div',
		helper:	'clone',
		items: 'li',
		maxLevels: 1,
		opacity: .6,
		placeholder: 'placeholder',
		revert: 250,
		tabSize: 25,
		tolerance: 'pointer',
		toleranceElement: '> div'
	});
	$("#orderList").submit(function(e){
		e.preventDefault();
		serialized = $('ol.sortable').nestedSortable('serialize');
		//Đóng toàn bộ hộp alert và hiển thị loading
		$(".alert").slideUp();
		$(".alert-loading").slideDown();
		$.ajax({
			type: "POST",
			data: serialized,
			success: function(res){
				//Đóng loading
				$(".alert-loading").slideUp();
				//Hiển thị hộp alert
				if(res.success)
				{
					$(".alert-success #successMessage").text(res.message);
					$(".alert-success").slideDown();
				}
				else
				{
					$(".alert-error #errorMessage").text(res.message);
					$(".alert-error").slideDown();
				}
			}
		});
	});
	$("div.options a[rel='edit']").click(function(){
		window.location.href = $(this).attr("href");
	});
	$("div.options a[rel='delete']").click(function(e){
		e.preventDefault();
		//Đổi màu nền sang màu đỏ
		bgcss = $(this).parents("div.title").css("background");
		$(this).parents("div.title").css({
			background: "scroll #F9CDCD url('/Content/XAdmin/img/draggable.png') no-repeat 0 -66px"
		});
		item = $(this);
		$("#dialogDelete").html("Bạn có chắc chắn muốn xóa danh mục: <br /><b>" + $(this).attr('item') + "</b> ?");
		$("#dialogDelete").dialog({
			title: "Xóa danh mục tin tức",
			modal: true,
			buttons: {
				"Đồng ý": function(){
					$.ajax({
						url: item.attr('href'),
						data: {id: item.attr('data')},
						type: 'POST',
						success: function(res){
							if(res.success)
							{
								$("#dialogDelete").dialog("close");
								item.parents("div.title").fadeOut("slow");
							}
						}
					});
				},
				"Hủy bỏ": function(){
					$(this).dialog("close");
					item.parents("div.title").css("background", bgcss);
				}
			}
		});
	});
});