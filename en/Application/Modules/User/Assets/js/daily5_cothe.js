function toggleAll(ele)
{
	if($(ele).attr('checked') == 'checked')
	    $("input.fid").attr("checked", "checked");
	else
		$("input.fid").removeAttr("checked");
}
$(function(){
    $('#frmDelete').submit(function(e){
        return confirm("Bạn có chắc chắn muốn xoá những người này khỏi danh sách yêu thích");
    });
    $('form').jqTransform({imgPath:'assets/jqtransformplugin/img/'});
});