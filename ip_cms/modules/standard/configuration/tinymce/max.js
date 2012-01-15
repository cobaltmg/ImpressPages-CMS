ipTinyMceConfigMax = {
    // Location of TinyMCE script
    script_url : ip.baseUrl + ip.libraryDir + 'js/tiny_mce/tiny_mce.js',
    
    theme : "advanced",
    entity_encoding : "raw", 
    plugins : "iplink,paste,safari,spellchecker,pagebreak,style,layer,table,advhr,advimage,emotions,iespell,inlinepopups,media,contextmenu,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
    theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
    theme_advanced_buttons2 : "cut,copy,pastetext,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,code,|,forecolor,backcolor",
    theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,fullscreen",
    theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,blockquote,pagebreak,|,insertfile,insertimage",
    theme_advanced_toolbar_location : "top",
    theme_advanced_toolbar_align : "left",
    theme_advanced_statusbar_location : "bottom",
    theme_advanced_resizing : true,
    theme_advanced_resize_horizontal : false,
    valid_elements : "@[class|style],strong,em,br,sup,sub,p,span,b,u,i,a[name|href|target|title],ul,ol,li",
    height : 300,
    width : '100%',
    content_css : ip.baseUrl + ip.themeDir + ip.theme + "/ip_content.css",
    theme_advanced_styles : "Text=;Caption=caption;Signature=signature;Note=note",
    forced_root_block : "p",

    document_base_url : ip.baseUrl,
    remove_script_host : false,
    relative_urls : false,
    convert_urls : true,

    paste_auto_cleanup_on_paste : true,
    paste_retain_style_properties : false,
    paste_strip_class_attributes : false,
    paste_remove_spans : false,
    paste_remove_styles : true,
    paste_convert_middot_lists : true,
    paste_text_use_dialog : true,    
    
    paste_preprocess : function(pl, o) {
    console.log(pl);
    console.log(o);
        ipTinyMceConfigPastePreprocess(pl, o, new Array('caption', 'signature', 'note'));
    }
    

};