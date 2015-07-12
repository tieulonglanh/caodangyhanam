/*------------------------------------------------------------------------------------------------------*/
/*	(TinyMCE Full)
/*------------------------------------------------------------------------------------------------------*/
$(function(){
	//TinyMCE
    tinyMCE.init({
    	// General options
        mode: "exact",
        elements: "content-editor",
        theme: "advanced",
        skin_variant: "silver",
        plugins: "imagemanager,filemanager,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave",

        // Theme options
        theme_advanced_buttons1: "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons2: "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
        theme_advanced_buttons3: "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
        theme_advanced_buttons4: "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft",
        theme_advanced_toolbar_location: "top",
        theme_advanced_toolbar_align: "left",
        theme_advanced_statusbar_location: "bottom",
        theme_advanced_resizing: true,

        relative_urls : false,
    
        language: "en",

        width: '100%',
        height:'300px',

        file_browser_callback : "openFileManger"
     });
});
function openFileManger(field_name, url, type, win) {
    alert(type);
    /*
    $('<div id="finder_'+callback+'"/>').elfinder({
        url : '/Assets/elfinder/connectors/php/connector-'+type+'.php',
        editorCallback : function(url){
            document.forms[0].elements[field_name].value = url;
        },
        closeOnEditorCallback : true,
        dialog :{
            title : "File Manager",
            modal : true,
            width : 700
        }
    });
    return false;*/
}