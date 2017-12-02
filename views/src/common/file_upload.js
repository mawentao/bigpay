/* 文件上传控件 */
define(function(require){
    require("jquery/ajaxfileupload");
    var ajax=require('ajax');
    var callbackfun,filesel,o={};
	var domid = "file-upload-div";

    function create() {
		var code = '<form method="POST" enctype="multipart/form-data">'+
                     '<input type="file" id="upfile" name="upfile" accept="*/*" style="display:none;"/>'+
                   '</form>';
        jQuery("#"+domid).html(code);
        filesel = jQuery('#upfile');
        filesel.change(do_upload);
    }

    function do_upload() {
        var imgfile = filesel.val();
        if (imgfile=="") return;
        var upurl= ajax.getAjaxUrl("upload&method=upfile&fileElementId=upfile");
        //alert(upurl);
        //callbackfun(upurl);
        jQuery.ajaxFileUpload({
            url: upurl,
            secureuri: false,
            fileElementId: 'upfile',
            dataType: 'json',
            timeout: 30000,
            complete: function(data) {
                console.log(data);
                create();
            },  
            success: function(data,status) {
                data['filename'] = imgfile;
                callbackfun(data);  
            },  
            error: function (data, status, e) {
                alert("Error: "+e);
                //callbackfun(data);
            }
        });
    };

    o.init = function() {
        if(!document.getElementById(domid)) { 
            var onediv = document.createElement('div');
            onediv.id=domid;
            document.body.appendChild(onediv);
        }
        create();
    };

    o.upload = function(callfun) {
        if (!filesel) {
            o.init();
        }
        callbackfun = callfun;
        filesel.val("");
        filesel.click();
    };

    return o;
});

