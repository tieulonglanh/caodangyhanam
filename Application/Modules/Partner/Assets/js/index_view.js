$(function(){
    var oTable = $('#data_table').dataTable({
        "sDom": 'fCl<"clear">rtip',
        "bProcessing": true,
        "bServerSide": true,
        "sServerMethod": "POST",
        "sAjaxSource"    : $('#data_table').data('ajax-url'),
        "fnServerData": function(sUrl, aoData, fnCallback){
            for(var i=0; i < aoData.length; i++)
            {
                if(aoData[i]['name'] == 'sSearch_1')
                {
                    if($('#catFilter').val() != '0')
                        aoData[i]['value'] = "=" + $('#catFilter').val();
                    else
                        aoData[i]['value'] = "";
                }
            }
            $.ajax({
                "url": sUrl,
                "data": aoData,
                "success": fnCallback,
                "dataType": "json",
                "type": "post",
                "cache": false
            });
        },
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
            { "mDataProp" : "cat_id",
                "bSortable": false,
                "mRender": function (data, type, full){
                    return data.name;
                }
            },
            { "mDataProp" : "file",
              "bSortable": false,
              "mRender": function (data, type, full){
                    return '<img src="' + data + '" alt="" style="max-width: 150px; max-height: 100px" />';
              }
            },
            { "mDataProp" : "name",
              "sClass": "aleft"
            },
            { "mDataProp" : "id",
              "bSortable": false,
              "bUseRendered": false,
              "fnRender": function (oObj){
                  return '<span class="tip">' +
                         '</span>'+
                         '&nbsp;&nbsp;&nbsp;&nbsp;'+
                         '<span class="tip">'+
                         '<a class="delete" title="Xóa" data-id="' + oObj.aData.id + '" data-name="' + oObj.aData.name +'">'+
                         '<img src="/Content/XMin/images/icon/icon_delete.png" />'+
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

    $('#catFilter').live('change', function(){
        oTable.fnDraw();
    });
    $('#catFilter').selectBox();
});

function changeStatus(e)
{
    e = $(e);
    $.ajax({
        url: $('#tblForm').data('change-status-url'),
        data: {
            id : e.data('id'),
            stt: e.attr('checked') ? 1 : 0
        },
        dataType: 'json',
        type: 'post',
        success: function(res){
            if(!res.success)
                showError(res.message);
        }
    });
}