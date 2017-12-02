define(function(require){
    var qr=require("common/qr");
    var qrurl,dialog,o={};

    function init_dialog_body(domid) {
        var html = "<div style='padding:20px;' id='qrcode-div'></div>";
        jQuery("#"+domid).html(html);
    }

    o.init = function() {
        var domid = "qr-dialog-div";
        init_dialog_body(domid);
		dialog = new MWT.Dialog({
            "title"  : '请用微信扫描二维码',
            "width"  : 290,
            //"height" : 200,
            "top"    : 50,
            "render" : domid
        });
        dialog.create();
        dialog.on("open", function(){
            //alert(qrurl);
            jQuery("#qrcode-div").html("");
            qr.render("qrcode-div", qrurl, 250);
        });
    };

    o.open = function(orderid) {
        qrurl = v.payurl+orderid;
        dialog.open();
    };

    return o;
});
