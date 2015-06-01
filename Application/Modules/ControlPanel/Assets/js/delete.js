$(function(){
	$("a[rel='delete']").click(function(e){
		e.preventDefault();
		item = $(this);
		$("#deleteDialog").html("Bạn có chắc chắn muốn xóa <br><b>" + $(this).attr('item') + "</b>");
		$("#deleteDialog").dialog({
			modal: true,
			buttons: {
				"Đồng ý": function(){
					$.ajax({
						url: item.attr("href"),
						type: "post",
						data: {id: item.attr("data")},
						success: function(){
							item.parents("tr").fadeOut("slow");
							$("#deleteDialog").dialog("close")
						}
					});
				},
				"Hủy bỏ": function(){
					$("#deleteDialog").dialog("close");
				}
			}
		});
	});
});