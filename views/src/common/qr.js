define(function(require){
    require('qrcode');
    var o={};

    o.render = function(domid, url, width) {
        if (!width) width=150;
        var qrcode = new QRCode(document.getElementById(domid), {
            width  : width,
            height : width
        });
	    qrcode.makeCode(url);
    };

    return o;
});
