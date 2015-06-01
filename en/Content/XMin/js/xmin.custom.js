$(function() {
    LResize();
    $(window).resize(function(){LResize(); Processgraph();});
    $(window).scroll(function(){scrollmenu();});
    //Button Click  Ajax Loading
    $('.loading').live('click',function() {
        var str=$(this).attr('title');
        var overlay=$(this).attr('rel');
        loading(str,overlay);
        setTimeout("unloading()",1500);
    });
    $('#preloader').live('click',function(){
        unloading();
    });
    $('.searchAutocomplete').click(function() {
        $('.searchText').toggle('slow', function() {
            // Animation complete.
        });
    });
    // Submit Form
    $('a.submit_form').live('click',function(){
        var form_id=$(this).parents('form').attr('id');
        $("#"+form_id).submit();
    })
    // Logout Click
    $('.logout').live('click',function() {
        var str="Logout";
        var overlay="1";
        loading(str,overlay);
        setTimeout("unloading()",1500);
        setTimeout( "window.location.href='" + $(this).data('url') + "'", 2000 );
    });
    // Tipsy Tootip
    $('.tip a ').tipsy({gravity: 's',live: true});
    $(' span.tip ').tipsy({gravity: 's',live: true});
    $('.ntip a ').tipsy({gravity: 'n',live: true});
    $('.wtip a ').tipsy({gravity: 'w',live: true});
    $('.etip a,.Base').tipsy({gravity: 'e',live: true});
    $('.netip a ').tipsy({gravity: 'ne',live: true});
    $('.nwtip a  ').tipsy({gravity: 'nw',live: true});
    $('.swtip a,.iconmenu li a ').tipsy({gravity: 'sw',live: true});
    $('.setip a ').tipsy({gravity: 'se',live: true});
    $('.wtip input').tipsy({ trigger: 'focus', gravity: 'w',live: true });
    $('.etip input').tipsy({ trigger: 'focus', gravity: 'e',live: true });
    $('.iconBox, div.logout').tipsy({gravity: 'ne',live: true });

    function Processgraph(){
        var bar = $('.bar'), bw = bar.width(), percent = bar.find('.percent'), circle = bar.find('.circle'), ps =  percent.find('span'),
            cs = circle.find('span'), name = 'rotate';
        var t = $('#pct'), val = t.val();
        if(val){
            val = t.val().replace("%", "");

            if (val >=0 && val <= 100){
                var w = 100-val, pw = (bw*w)/100,
                    pa = {  	width: w+'%' },
                    cw = (bw-pw), ca = {	"left": cw }
                ps.animate(pa);
                cs.text(val+'%');
                circle.animate(ca, function(){
                    circle.removeClass(name)
                }).addClass(name);
            } else {
                alert('range: 0 - 100');
                t.val('');
            }
        }
    }

    // Effect 
    $('.SEclick, .SEmousedown, .SEclicktime,.SEremote,.SEremote2,.SEremote3,.SEremote4').jrumble();
    $('.SE').jrumble({
        x: 2,
        y: 2,
        rotation: 1
    });

    $('.alertMessage.error ').jrumble({
        x: 10,
        y: 10,
        rotation: 4
    });

    $('.alertMessage.success').jrumble({
        x: 4,
        y: 0,
        rotation: 0
    });

    $('.alertMessage.warning').jrumble({
        x: 0,
        y: 0,
        rotation: 5
    });

    $('.SE').hover(function(){
        $(this).trigger('startRumble');
    }, function(){
        $(this).trigger('stopRumble');
    });

    $('.SEclick').toggle(function(){
        $(this).trigger('startRumble');
    }, function(){
        $(this).trigger('stopRumble');
    });

    $('.SEmousedown').bind({
        'mousedown': function(){
            $(this).trigger('startRumble');
        },
        'mouseup': function(){
            $(this).trigger('stopRumble');
        }
    });

    $('.SEclicktime').click(function(){
        var demoTimeout;
        $this = $(this);
        clearTimeout(demoTimeout);
        $this.trigger('startRumble');
        demoTimeout = setTimeout(function(){$this.trigger('stopRumble');}, 1500)
    });
    $('.SEremote').hover(function(){
        $('.SEremote2').trigger('startRumble');
    }, function(){
        $('.SEremote2').trigger('stopRumble');
    });

    $('.SEremote2').hover(function(){
        $('.SEremote').trigger('startRumble');
    }, function(){
        $('.SEremote').trigger('stopRumble');
    })

    $('.SEremote3').hover(function(){
        $('.alertMessage').trigger('startRumble');
    }, function(){
        $('.alertMessage').trigger('stopRumble');
    })

    $('.SEremote4').hover(function(){
        $('.alertMessage.error').trigger('startRumble');
    }, function(){
        $('.alertMessage.error').trigger('stopRumble');
    })

    $('input[placeholder], textarea[placeholder]').placeholder();

    // icon  gray Hover
    $('.iconBox.gray').hover(function(){
        var name=$(this).find('img').attr('alt');
        $(this).find('img').animate({ opacity: 0.5 }, 0, function(){
            $(this).attr('src','/Content/XMin/images/icon/color_18/'+name+'.png').animate({ opacity: 1 }, 700);
        });
    },function(){
        var name=$(this).find('img').attr('alt');
        $(this).find('img').attr('src','/Content/XMin/images/icon/gray_18/'+name+'.png');
    })

    // Animation icon  Logout
    $('div.logout').hover(function(){
        var name=$(this).find('img').attr('alt');
        $(this).find('img').animate({ opacity: 0.4 }, 200, function(){
            $(this).attr('src','/Content/XMin/images/'+name+'.png').animate({ opacity: 1 }, 500);
        });
    },function(){
        var name=$(this).find('img').attr('name');
        $(this).find('img').animate({ opacity: 0.5 }, 200, function(){
            $(this).attr('src','/Content/XMin/images/'+name+'.png').animate({ opacity: 1 }, 500);
        });
    })

    // Animation icon  setting
    $('div.setting').hover(function(){
        $(this).find('img').addClass('gearhover');
    },function(){
        $(this).find('img').removeClass('gearhover');
    })

    // shoutcutBox   Hover
    $('.shoutcutBox').hover(function(){
        $(this).animate({ left: '+=15'}, 200);
    },function(){
        $(this).animate({ left: '0'}, 200);
    })

    // shoutcutBox   Hover
    $("#shortcut li").hover(function() {
        var e = this;
        $(e).find("a").stop().animate({ marginTop: "-7px" }, 200, function() {
            $(e).find("a").animate({ marginTop: "-5px" }, 200);
        });
    },function(){
        var e = this;
        $(e).find("a").stop().animate({ marginTop: "2px" }, 200, function() {
            $(e).find("a").animate({ marginTop: "0px" }, 200);
        });
    });

    // hide notify  Message with click
    $('#alertMessage').live('click',function(){
        $(this).stop(true,true).animate({ opacity: 0,right: '-20'}, 500,function(){ $(this).hide(); });
    });

    // images hover
    $('.picHolder,.SEdemo').hover(
        function() {
            $(this).find('.picTitle').fadeTo(200, 1);
        },function() {
            $(this).find('.picTitle').fadeTo(200, 0);
        }
    )

    function showTooltip(x, y, contents) {
        $('<div id="tooltip" >' + contents + '</div>').css({
            position: 'absolute',
            display: 'none',
            top: y -13,
            left: x + 10
        }).appendTo("body").show();
    }

    $("#main_menu").sortable({
        opacity: 0.6,connectWith: '.limenu',items: '.limenu'
    });

    // Confirm Delete.
    $(".delete").live('click', function() {
        var row = $(this).parents('tr');
        if(row.length == 0)
            row = $(this).parents('li').first();
        var dataSet = $(this).parents('form');
        var id = $(this).data("id");
        var name = $(this).data("name");
        var data = {
            id: id,
            name: name
        };
        Delete(data, row, dataSet);
    });
    $(".deleteAll").live('click', function() {
        var rel = $(this).attr('rel');
        var row = $(this).parents('.tab_content').attr('id');
        var row = row+' .load_page ';
        if(!rel) {
            var rel=0;
            var row=$('#load_data').attr('id');
        }
        var dataSet=$('form:eq('+rel+')');
        var	data=$('form:eq('+rel+')').serialize();
        var name = 'Các mục đã đánh dấu';
        Delete(data, name, row, 2, dataSet);
    });
    //Edit.
    $(".edit").live('click', function() {
        var type= $(this).data('type');
        var row = $(this).parents('tr');
        var dataSet = $(this).parents('form');
        var id = $(this).data("id");
        var name = $(this).data("name");
        var data = {
            id: id,
            name: name
        };
        Edit(data, row, dataSet, type);
    });
});

$(function(){
// Check browser fixbug
    var mybrowser=navigator.userAgent;
    if(mybrowser.indexOf('MSIE')>0){$(function() {
        $('.formEl_b fieldset').css('padding-top', '0');
        $('div.section label small').css('font-size', '10px');
        $('div.section  div .select_box').css({'margin-left':'-5px'});
        $('.iPhoneCheckContainer label').css({'padding-top':'6px'});
        $('.uibutton').css({'padding-top':'6px'});
        $('.uibutton.icon:before').css({'top':'1px'});
        $('.dataTables_wrapper .dataTables_length ').css({'margin-bottom':'10px'});
    });
    }
    if(mybrowser.indexOf('Firefox')>0){ $(function() {
        $('.formEl_b fieldset  legend').css('margin-bottom', '0px');
        $('table .custom-checkbox label').css('left', '3px');
    });
    }
    if(mybrowser.indexOf('Presto')>0){
        $('select').css('padding-top', '8px');
    }
    if(mybrowser.indexOf('Chrome')>0){$(function() {
        $('div.tab_content  ul.uibutton-group').css('margin-top', '-40px');
        $('div.section  div .select_box').css({'margin-top':'0px','margin-left':'-2px'});
        $('select').css('padding', '6px');
        $('table .custom-checkbox label').css('left', '3px');
    });
    }
    if(mybrowser.indexOf('Safari')>0){}
});

function fileDialogCallback(callback, type, input, title){
    $('<div id="finder_'+callback+'"/>').elfinder({
        url : '/Assets/elfinder/connectors/php/connector-'+type+'.php',
        editorCallback : function(url){
            $('#'+input).val(url);
        },
        closeOnEditorCallback : true,
        dialog :{
            title : title,
            modal : true,
            width : 700
        }
    });
}

function fileDialogCallbackAdd(callback, type, input, title){
    $('<div id="finder_'+callback+'"/>').elfinder({
        url : '/Assets/elfinder/connectors/php/connector-'+type+'.php',
        editorCallback : function(url){
            if($('#'+input).val()=="")
                $('#'+input).val(url);
            else
                $('#'+input).val($('#'+input).val()+";"+url);
        },
        closeOnEditorCallback : true,
        dialog :{
            title : title,
            modal : true,
            width : 700
        }
    });
}

function fileDialog(callback, type, title){
    $('<div id="finder_'+callback+'"/>').elfinder({
        url : '/Assets/elfinder/connectors/php/connector-'+type+'.php',
        dialog : {
            title : title,
            modal : true,
            width : 700
        }
    });
}

function Delete(data, row, dataSet){
    var url = dataSet.data('delete-url');
    $.confirm({
        'title': '_HỘP CẢNH BÁO XÓA',
        'message': "<strong>BẠN MUỐN XOÁ </strong><br /><span style='color: red;'>' "+ data.name +" ' </span>",
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
                            $('#preloader').html('Xoá...');
                            if(res.success){
                                setTimeout(function(){
                                    unloading();
                                    row.slideUp(function(){
                                        if(res.message)
                                            showSuccess(res.message, 5000);
                                        else
                                            showSuccess('Xóa thành công', 5000);
                                    });
                                },1000);
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
}

function Edit(data, row, dataSet, type){
    var url = dataSet.data('edit-url');
    if(parseInt(type) == 1)
        window.location.href = url + '/' + data.id
}

function ResetForm(){
    $('form').each(function(index) {
        var form_id=$('form:eq('+index+')').attr('id');
        if(form_id){
            $('#'+form_id).get(0).reset();
            $('#'+form_id).validationEngine('hideAll');
            var editor=$('#'+form_id).find('#editor').attr('id');
            if(editor){
                $('#editor').cleditor()[0].clear();
            }
        }
    });
}

function hexFromRGB(r, g, b) {
    var hex = [
        r.toString( 16 ),
        g.toString( 16 ),
        b.toString( 16 )
    ];
    $.each( hex, function( nr, val ) {
        if ( val.length === 1 ) {
            hex[ nr ] = "0" + val;
        }
    });
    return hex.join( "" ).toUpperCase();
}

function refreshSwatch() {
    var red = $( "#red" ).slider( "value" ),
        green = $( "#green" ).slider( "value" ),
        blue = $( "#blue" ).slider( "value" ),
        hex = hexFromRGB( red, green, blue );
    $( "#swatch" ).css( "background-color", "#" + hex );
}

function showError(str, delay){
    if(delay){
        $('#alertMessage').removeClass('success info warning').addClass('error').html(str).stop(true,true).show().animate({ opacity: 1,right: '10'}, 500,function(){
            $(this).delay(delay).animate({ opacity: 0,right: '-20'}, 500,function(){ $(this).hide(); });
        });
        return false;
    }
    $('#alertMessage').removeClass('success info warning').addClass('error').html(str).stop(true,true).show().animate({ opacity: 1,right: '10'}, 500);
}
function showSuccess(str, delay){
    if(delay){
        $('#alertMessage').removeClass('error info warning').addClass('success').html(str).stop(true,true).show().animate({ opacity: 1,right: '10'}, 500,function(){
            $(this).delay(delay).animate({ opacity: 0,right: '-20'}, 500,function(){ $(this).hide(); });
        });
        return false;
    }
    console.log(str);
    $('#alertMessage').removeClass('error info warning').addClass('success').html(str).stop(true,true).show().animate({ opacity: 1,right: '10'}, 500);
}
function showWarning(str, delay){
    if(delay){
        $('#alertMessage').removeClass('error success  info').addClass('warning').html(str).stop(true,true).show().animate({ opacity: 1,right: '10'}, 500,function(){
            $(this).delay(delay).animate({ opacity: 0,right: '-20'}, 500,function(){ $(this).hide(); });
        });
        return false;
    }
    $('#alertMessage').removeClass('error success  info').addClass('warning').html(str).stop(true,true).show().animate({ opacity: 1,right: '10'}, 500);
}
function showInfo(str, delay){
    if(delay){
        $('#alertMessage').removeClass('error success  warning').html(str).stop(true,true).show().animate({ opacity: 1,right: '10'}, 500,function(){
            $(this).delay(delay).animate({ opacity: 0,right: '-20'}, 500,function(){ $(this).hide(); });
        });
        return false;
    }
    $('#alertMessage').removeClass('error success  warning').addClass('info').html(str).stop(true,true).show().animate({ opacity: 1,right: '10'}, 500);
}

function loading(name, overlay) {
    $('body').append('<div id="overlay"></div><div id="preloader">'+name+'..</div>');
    if(overlay){
        $('#overlay').css('opacity',0.4).fadeIn(400,function(){
            $('#preloader').fadeIn(400);
        });
        return false;
    }
    $('#preloader').fadeIn();
}

function unloading() {
    $('#preloader').fadeOut(400,function(){ $('#overlay').fadeOut(); $.fancybox.close(); }).remove();
}

function imgRow(){
    var maxrow=$('.albumpics').width();
    if(maxrow){
        maxItem= Math.floor(maxrow/160);
        maxW=maxItem*160;
        mL=(maxrow-maxW)/2;
        $('.albumpics ul').css({
            'width'	:	maxW	,
            'marginLeft':mL
        })
    }}

function scrollmenu(){
    if($(window).scrollTop()>=1){
        $("#header ").css("z-index", "50");
    }else{
        $("#header ").css("z-index", "47");
    }
}

function LResize(){
    imgRow();
    scrollmenu();
    $("#shadowhead").show();
    if($(window).width()<=480) {
        $(' .albumImagePreview').show();
        $('.screen-msg').hide();
        $('.albumsList').hide();
    }
    if($(window).width()<=768){
        $('body').addClass('nobg');
        $('#content').css({ marginLeft: "70px" });
        $('#main_menu').removeClass('main_menu').addClass('iconmenu');
        $('#main_menu li').each(function() {
            var title=$(this).find('b').text();
            $(this).find('a').attr('title',title);
        });
        $('#main_menu li a').find('b').hide();
        $('#main_menu li ').find('ul').hide();
    }else{
        $('body').removeClass('nobg').addClass('dashborad');
        $('#content').css({ marginLeft: "240px" });
        $('#main_menu').removeClass('iconmenu ').addClass('main_menu');
        $('#main_menu li a').find('b').show();
    }
    if($(window).width()>1024) {
        //	$('#main_menu').removeClass('iconmenu ').addClass('main_menu');
        //	$('#main_menu li a').find('b').show();
    }
}

function seoTitleGenerate(str){
    str= str.toLowerCase();
    str= str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g,"a");
    str= str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g,"e");
    str= str.replace(/ì|í|ị|ỉ|ĩ/g,"i");
    str= str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g,"o");
    str= str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g,"u");
    str= str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g,"y");
    str= str.replace(/đ/g,"d");
    str= str.replace(/!|@|%|\^|\*|\(|\)|\+|\=|\<|\>|\?|\/|,|\.|\:|\;|\'| |\"|\&|\#|\[|\]|~|$|_/g,"-");
    /* tìm và thay thế các kí tự đặc biệt trong chuỗi sang kí tự - */
    str= str.replace(/-+-/g,"-"); //thay thế 2- thành 1-
    str= str.replace(/^\-+|\-+$/g,"");
    //cắt bỏ ký tự - ở đầu và cuối chuỗi
    return str;
}