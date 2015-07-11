$(document).ready(function(){
    $('ol.sortable').nestedSortable({
        disableNesting: 'no-nest',
        forcePlaceholderSize: true,
        handle: 'a',
        helper:	'clone',
        items: 'li',
        maxLevels: 3,
        opacity: .6,
        placeholder: 'placeholder',
        revert: 250,
        tabSize: 25,
        tolerance: 'pointer',
        toleranceElement: '> a'
    });

    $("#orderList").submit(function(e){
        e.preventDefault();
        serialized = $('ol.sortable').nestedSortable('serialize');
        //Đóng toàn bộ hộp alert và hiển thị loading
        loading('Cập nhật', true);
        $.ajax({
            type: "POST",
            data: serialized,
            success: function(res){
                //Đóng loading
                unloading();
                //Hiển thị hộp alert
                if(res.success){
                    showSuccess(res.message);
                }
                else{
                    showError(res.message);
                }
            }
        });
    });
});