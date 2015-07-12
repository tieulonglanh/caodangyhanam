$(function(){
    $("form").validationEngine();
    $(".editor").cleditor({
        height: 150
    });
    // on click callback
    $('.file-manager').click(function(){
        var callback = $(this).attr('id');
        var type = $(this).data('type');
        var input = $(this).attr('rel');
        var title = $(this).attr('title');
        fileDialogCallback(callback, type, input, title);
    });
    
    $('.file-manager-add').click(function(){
        var callback = $(this).attr('id');
        var type = $(this).data('type');
        var input = $(this).attr('rel');
        var title = $(this).attr('title');
        fileDialogCallbackAdd(callback, type, input, title);
    });
    // Checkbox iphoneStyle
    $(".on_off_checkbox").iphoneStyle();  // Label On / Off
});
function updateSeoUrl(s, d)
{
    s = $(s);
    d = $(d);
    d.val(seoTitleGenerate(s.val()));
}
function begin(res)
{
    loading('Cập nhật', true);
}
function success(res)
{
    console.log(res);
    setTimeout(function(){
        unloading();
        if(res.success){
            showSuccess(res.message);
            if(res.url)
                setTimeout( "window.location.href='" + res.url + "'", 3000 );
        }
        else{
            showError(res.message);
        }
    }, 1000);
}