 function filterCategories()
 {
	 value = $('#categoryFilter').val();
	 if (value != '0') {
		 $('#pageList').data("kendoGrid").dataSource.filter({ field: "category_id", operator: "eq", value: parseInt(value) });
     } else {
    	 $('#pageList').data("kendoGrid").dataSource.filter({});
     }
 }
 function edit(e)
 {
	href = $(e).attr('href');
	id = $(e).attr('data');
	window.location.href = href + '/' + id;
 }
 function remove(ele)
 {
 	item = $(ele);
 	$("#deleteDialog").html("Bạn có chắc chắn muốn xóa <br><b>" + item.attr('title') + "</b>");
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
 }