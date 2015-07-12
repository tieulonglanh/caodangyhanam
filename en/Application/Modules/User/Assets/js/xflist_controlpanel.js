$(function(){
    var oTable = $('#data_table').dataTable({
        "sDom": 'fCl<"clear">rtip',
        "bProcessing": true,
        "bServerSide": true,
        "sServerMethod": "POST",
        "sAjaxSource"    : $('#data_table').data('ajax-url'),
        "sPaginationType": "full_numbers",
        "aaSorting": [],
        "aoColumns": [
            {  "mDataProp" : "user_id",
                "bSortable": false,
                "mRender": function (data, type, full){
                    return '<div class="checksquared">' +
                           '<input type="checkbox" id="checksquared' + data + '" name="checklist[]" />'+
                           '<label for="checksquared' + data + '"></label>' +
                           '</div>';
                }
            },
            {  "mDataProp" : "user_id"
            },
            { "mDataProp" : "username",
              "sClass": "aleft"
            },
            { "mDataProp" : "email",
              "sClass": "aleft"
            },
            { "mDataProp" : "register_date",
                "iDataSort": 4,
                "mRender": function (data, type, full){
                    return date('d-m-Y', data) + ' lúc ' + date('H:i', data);
                }
            },
            { "mDataProp" : "register_date",
                "bVisible" : false
            },
            { "mDataProp" : "user_id",
                "bSortable": false,
                "bUseRendered": false,
                "fnRender": function (oObj){
                    if($.inArray(oObj.aData.user_id, teachers) == -1){
                        return '<span class="tip">' +
                            '<a class="setTeacher" title="Phân làm giảng viên" data-type="1" data-id="' + oObj.aData.user_id + '" data-name="' + oObj.aData.username +'">'+
                            '<img src="/Content/XMin/images/icon/color_18/administrator.png" alt="administrator">'+
                            '</a>'+
                            '</span>';
                    }
                    else
                        return '<span class="tip">' +
                               '<a class="edit" title="Sửa thông tin giảng viên" data-type="1" data-id="' + oObj.aData.user_id + '" data-name="' + oObj.aData.username +'">'+
                               '<img src="/Content/XMin/images/icon/icon_edit.png" />'+
                               '</a>'+
                               '</span>';
                }
            }
        ],
        "fnPreDrawCallback": function() {
            loading('Đang tải');
        },
        "fnDrawCallback": function() {
            unloading();
        },
        "oLanguage": {
            sZeroRecords: "Không tìm thấy dữ liệu - xin lỗi",
            sInfo: "Hiển thị _START_ đến _END_ của tổng _TOTAL_ bản ghi",
            sInfoEmpty: "Không có bản ghi nào",
            sInfoFiltered: "(đã lọc trong tổng số _MAX_ bản ghi)",
            sLengthMenu: "_MENU_",
            sSearch: "_INPUT_",
            oPaginate: {
                sFirst: "Đầu",
                sLast: "Cuối",
                sNext : "Sau",
                sPrevious: "Trước"
            },
            sProcessing: "Đang xử lý dữ liệu..."
        }
    });

    $('.dataTables_filter input[type=text]').attr('placeholder', 'Tìm kiếm');
    $('input[placeholder], textarea[placeholder]').placeholder();

    // Checkbox  All in Data Table
    $(".checkAll").live('click',function(){
        var table=$(this).parents('table').attr('id');
        var checkedStatus = this.checked;
        var id= this.id;
        $( "table#"+table+" tbody tr td:first-child input:checkbox").each(function() {
            this.checked = checkedStatus;
            var id= this.id;
            if (this.checked) {
                $(this).attr('checked', $('#' + id).is(':checked'));
            }else{
                $(this).attr('checked', $('#' + id).is(''));
            }
        });
    });
    // Select boxes
    $(".dataTables_wrapper .dataTables_length select").selectBox();
    // Select boxes in Data table
    $(".dataTables_wrapper .dataTables_length select").addClass("small");

    $('.setTeacher').live('click', function(){
        var context = $(this);
        var url = $(this).parents('#tblForm').data('setteacher-url');
        var data = {
            id: $(this).data('id'),
            username: $(this).data('name')
        };
        $.confirm({
            'title': '_HỘP CẢNH BÁO PHÂN QUYỀN',
            'message': "<strong>BẠN MUỐN PHÂN QUYỀN CHO GIÁO VIÊN </strong><br /><span style='color: red;'>' "+ data.username +" ' </span>",
            'buttons': {
                'Đồng ý': {
                    'class': 'special',
                    'action': function(){
                        loading('Kiểm tra', true);
                        $.ajax({
                            url: url,
                            data: data,
                            dataType: 'json',
                            type: 'post',
                            success: function(res){
                                $('#preloader').html('Cập nhật...');
                                if(res.success){
                                    context.hide();
                                    setTimeout(function(){
                                        unloading();
                                            if(res.message)
                                                showSuccess(res.message, 5000);
                                            else
                                                showSuccess('Phân quyền giáo viên thành công', 5000);
                                        if(res.url){
                                            setTimeout(function(){
                                                window.location.href = res.url;
                                            }, 3000);
                                        }
                                    });
                                }
                                else
                                    showError(res.message, 5000);
                            }
                        });
                        return false;
                    }
                },
                'Huỷ bỏ': {
                    'class'	: ''
                }
            }
        });
    });
});