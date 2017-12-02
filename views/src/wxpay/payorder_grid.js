define(function(require){
    var ajax=require("ajax");
    var qrdialog = require('./qr_dialog');
    var payorder_dialog = require('./payorder_dialog');
    var store,o={};

    o.init = function(){
        var thiso = this;
        store = new MWT.Store({
            "url": ajax.getAjaxUrl('wxpay&method=queryorder')
        });
        grid = new MWT.Grid({
            render: "payorder-grid-div",
            store: store,
            pagebar: false,
            bordered: true,
            pagebar: true,
            pageSize: 20,
            cm: new MWT.Grid.ColumnModel([
                {dataIndex:'order_id',head:'支付订单ID',width:90,sort:true,render:function(v,item){
                    return "#"+v;
                }},
                {dataIndex:'order_body',head:'订单内容',sort:true,render:function(v,item){
                    return v;
                }},
                {dataIndex:'total_fee',head:'订单金额',width:100,align:'right',sort:true,render:function(v,item){
                    return v;
                }},
                {dataIndex:'addtime',head:'创建时间',width:150,align:'center',sort:true,render:function(v,item){
                    return v;
                }},
                {dataIndex:'addby',head:'创建者',width:70,align:'center',sort:true,render:function(v,item){
                    return v;
                }},
                {dataIndex:'order_id',head:'操作',width:150,align:'center',render:function(v,item){
                    var qrbtn = "<a name='qrbtn' data-id='"+v+"' href='javascript:;'>二维码</a>";
                    var editbtn = "<a name='editbtn' data-id='"+v+"' href='javascript:;'>编辑</a>";
                    var delbtn = "<a name='delbtn' data-id='"+v+"' href='javascript:;'>删除</a>";
                    var logbtn = "<a href='javascript:;'>支付记录</a>";
                    var btns = [qrbtn, editbtn, delbtn]; 
                    return btns.join("&nbsp;&nbsp;");
                }}
            ]),
            tbar: [
                {type:"search",label:"搜索",id:"so-key",width:500,handler:thiso.query,placeholder:"搜索订单内容"},
                '->',
                {"label":"创建订单",class:'mwt-btn mwt-btn-primary',handler:payorder_dialog.open}
            ]
        });
        grid.create();
        store.on('load',function(){
            jQuery("[name=qrbtn]").click(function(){
                var orderid = jQuery(this).data('id');
                qrdialog.open(orderid);
            });
            jQuery("[name=editbtn]").click(function(){
                var orderid = jQuery(this).data('id');
                payorder_dialog.open(orderid);
            });
            jQuery("[name=delbtn]").click(function(){
                var orderid = jQuery(this).data('id');
                o.del(orderid);
            });
        });
        qrdialog.init();
        payorder_dialog.init();
        thiso.query();
    };

    o.getrecord = function(id) {
        return grid.getRecord("order_id", id);
    };

    o.query = function() {
        store.baseParams = {
            "key": get_value("so-key")
        };
        grid.load();
    };

    o.del = function(orderid) {
        if (!window.confirm("注意：删除订单会同时删除该订单的所有支付记录！\n\n确定要删除吗？")) return;
        ajax.post("wxpay&method=delorder",{ids:orderid},function(res){
            if (res.retcode!=0) alert(res.retmsg);
            else o.query();
        });
    };

    return o;
});
