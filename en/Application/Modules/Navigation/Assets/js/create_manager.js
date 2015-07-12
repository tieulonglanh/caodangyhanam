$(function(){
    // Select boxes
    $("select").not("select.chzn-select").selectBox();

    $("#node").change(function(){
        var da  = false;
        var url = $(this).find('option:selected').data('url');
        if($('#currentValue').length == 1){
            if($('#currentValue').val().indexOf('/') == -1
                && $('#currentValue').val().indexOf('.php') == -1
                && $('#currentValue').val().indexOf('.html') == -1
                && $('#currentValue').val().indexOf('.htm') == -1)
                url += '/' + encodeURIComponent($('#currentValue').val());
            da = {
                data: $('#currentValue').val()
            };
        }

        $.ajax({
            url:  url,
            type: "post",
            data: da,
            success: function(res){
                $("#menu_node_container").html(res);
                $('#menu_node_container select').selectBox();
                $('#menu_node_container select').live('change', function(){
                    var url = $(this).find('option:selected').data('url');
                    $('#url').val(url);
                });
                $('#menu_node_container select').change();
            }
        });
    });
    $("#node").change();
});