	var fdata;
	
	$(function(){
		$('#dialog').dialog({
			autoOpen: false,
			modal: true,
			title: 'Cắt ảnh để chọn được avatar đẹp nhất',
			width: 550,
			height: 600,
			buttons: {
				"Cắt ảnh": function(){
					$.ajax({
						url: '/User/FirstLogin/photoresize',
						data: {x: parseInt($("#x").val()), y: parseInt($("#y").val()), w: parseInt($("#w").val()), h: parseInt($("#h").val()), crop: 1},
						type: 'post',
						dataType: 'json',
						success: function(res){
							$('#previewImage').css('background-image', "url('" + res.en_path_url + '?' + Math.random() + "')");
						}
					});
					$(this).dialog("close");
				},
				"Giữ ảnh gốc": function(){
					$.ajax({
						url: '/User/FirstLogin/photoresize',
						type: 'post',
						dataType: 'json',
						success: function(res){
							$('#previewImage').css('background-image', "url('" + res.path + '?' + Math.random() + "')");
						}
					});
					$(this).dialog("close");
				}
			}
		});
	});
	
    function createUploader(){            
        var uploader = new qq.FileUploader({
            element: document.getElementById('file-uploader'),
            action: $('#file-uploader').attr('rel'),
            params: {},
            allowedExtensions: ['jpg', 'jpeg', 'png'],
            sizeLimit: 7340032,
            onComplete: function(id, fileName, data){
            	fdata = data;
				//console.log(data);
				if(data.success)
				{
					im = $('<img id="cropImage" />').attr('src', data.path + '?' + Math.random());
					$("#dialog").html(im);
					$('#cropImage').Jcrop({
						onSelect:    postCoords,
			            bgColor:     'black',
			            bgOpacity:   .4,
			            setSelect:   [ 0, 0, 310, 310 ],
			            allowResize: false
					});
					$("#dialog").dialog("open");
					$('#submit-box').slideDown();
				}
				else
				{
					$("#errormessage").html(data.error.error[0]);
				}
            },
            onCancel: function(id, fileName){},
            messages: {
                // error messages, see qq.FileUploaderBasic for content
            },
            showMessage: function(message){ alert(message); },
            debug: true
        });           
    }
    $(function(){
    	createUploader();
    });
    
	function postCoords(c){
		$("#x").val(c.x);
		$("#y").val(c.y);
		$("#w").val(c.w);
		$("#h").val(c.h);
	}