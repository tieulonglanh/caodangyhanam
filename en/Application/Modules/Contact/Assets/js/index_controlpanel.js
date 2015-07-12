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
            {  "mDataProp" : "id",
                "bSortable": false,
                "mRender": function (data, type, full){
                    return '<div class="checksquared">' +
                        '<input type="checkbox" id="checksquared' + data + '" name="checklist[]" />'+
                        '<label for="checksquared' + data + '"></label>' +
                        '</div>';
                }
            },
            { "mDataProp" : "name",
                "sClass": "aleft"
            },
            { "mDataProp" : "email",
                "sClass": "aleft"
            },
            { "mDataProp" : "title",
                "sClass": "aleft"
            },
            { "mDataProp" : "created_date",
                "iDataSort": 4,
                "mRender": function (data, type, full){
                    return date('d-m-Y', strtotime(data)) + ' lúc ' + date('H:i', strtotime(data));
                }
            },
            { "mDataProp" : "created_date",
                "bVisible" : false,
                "mRender": function(data, type, full){
                    return date("YmdHis", strtotime(data));
                }
            },
            {
                "mDataProp" : "phone",
                "mRender": function(data, type, full){
                    return data;
                }
            },
            { "mDataProp" : "id",
                "bSortable": false,
                "bUseRendered": false,
                "fnRender": function (oObj){
                    return '<span class="tip">'+
                           '<a class="delete" title="Xóa" data-id="' + oObj.aData.id + '" data-name="' + oObj.aData.title +'">'+
                           '<img src="/Content/XMin/images/icon/icon_delete.png" />'+
                           '</a>'+
                           '</span>'+
                           '&nbsp;&nbsp;&nbsp;&nbsp;'+
                           '<span class="tip">'+
                           '<a class="lecture" title="Xem nội dung" data-id="' + oObj.aData.id + '" data-name="' + oObj.aData.title +'">'+
                           '<img src="/Content/XMin/images/icon/color_18/document.png" />'+
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

    $('.lecture').live('click', function(){
        var context = $(this);
        loading('Đang tải');
        $.ajax({
            url: $('form').data('show-url'),
            data: {
                id: context.data('id'),
                title: context.data('name')
            },
            type: 'post',
            success: function(res){
                unloading();
                $('#dialog').html(res);
                $('#dialog').dialog({
                    title: context.data('name'),
                    width: 800,
                    height: 600,
                    modal: true,
                    buttons: {
                        "Đóng": function(){
                            $('#dialog').dialog('close');
                            oTable.fnDraw();
                        }
                    }
                });
            }
        });
    });
});