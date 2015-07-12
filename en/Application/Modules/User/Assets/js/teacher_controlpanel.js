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
                        return '<span class="tip">' +
                            '<a class="edit" title="Chuyển vào lớp" data-type="1" data-id="' + oObj.aData.user_id + '" data-name="' + oObj.aData.username +'">'+
                            '<img src="/Content/XMin/images/icon/color_18/administrator.png" alt="administrator">'+
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
});