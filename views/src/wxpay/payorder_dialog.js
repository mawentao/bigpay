define(function(require){
    var ajax=require("ajax");
    var orderid,dialog,o={};

    function init_dialog_body(domid) {
        var html = "<div style='padding:5px;'>"+
              "<table class='tabform'>"+
                "<tr><th width='60'>订单内容:</th><td><input id='fm_order_body' type='text' class='form-control'></td><td class='tip'>*</td></tr>"+
                "<tr><th>订单金额:</th><td><input type='text' id='fm_total_fee' class='form-control'></td><td class='tip'>* 单位：分</td></tr>"+
              "</table>";
            "</div>";
        jQuery("#"+domid).html(html);
    }

    o.init = function() {
        var domid = "payorder-dialog-div";
        init_dialog_body(domid);
		dialog = new MWT.Dialog({
            "title"  : '创建订单',
            "width"  : 400,
            "top"    : 50,
            "render" : domid,
            buttons  : [
                {"label":"确定",handler:o.submit},
                {"label":"关闭",type:'close',cls:'mwt-btn-danger'}
            ]
        });
        dialog.create();
        dialog.on("open", function(){
            o.reset();
            if (orderid) {
                var item = require("./payorder_grid").getrecord(orderid);
                set_value("fm_order_body",item.order_body);
				set_value("fm_total_fee",parseFloat(item.total_fee)*100);
                dialog.setTitle("编辑订单");
            } else {
                dialog.setTitle("创建订单");
            }
        });
    };

    o.reset = function() {
        set_value("fm_order_body","");
        set_value("fm_total_fee","0");
    };

    o.open = function(oid) {
        orderid = 0;
        if (oid) orderid = oid;
        dialog.open();
    };

    o.submit = function() {
        var params = {
            order_id: orderid,
            order_body: get_text_value('fm_order_body'),
            total_fee: get_text_value('fm_total_fee')
        };
        //print_r(params);
        ajax.post("wxpay&method=saveorder",params,function(res){
            if (res.retcode!=0) {
                alert(res.retmsg);
            } else {
                dialog.close();
                require('./payorder_grid').query();
            }
        });
    };

    return o;
});
