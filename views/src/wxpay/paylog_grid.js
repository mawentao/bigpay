define(function(require){
    var ajax=require("ajax");
    var store,o={};

    o.init = function(){
        var thiso = this;
        var statusOptions=[
            {value:1,text:"支付成功"},
            {value:2,text:"支付失败"},
            {value:0,text:"未支付"}
        ];
        var statusStr = [
            '<span style="color:gray">未支付</span>',
            '<span style="color:green">支付成功</span>',
            '<span style="color:red">支付失败</span>'
        ];
        store = new MWT.Store({
            "url": ajax.getAjaxUrl('wxpay&method=querylog')
        });
        grid = new MWT.Grid({
            render: "paylog-grid-div",
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
                {dataIndex:'pay_fee',head:'支付金额',width:100,align:'right',sort:true,render:function(v,item){
                    return v;
                }},
                {dataIndex:'paytime',head:'支付时间',width:150,align:'center',sort:true,render:function(v,item){
                    return v;
                }},
                {dataIndex:'status',head:'状态',width:100,align:'center',sort:true,render:function(v,item){
                    return statusStr[v];
                }}
            ]),
            tbar: [
                {type:"select",label:"支付状态",id:"status-sel",width:60,options:statusOptions,value:"1",handler:thiso.query},
                {type:"search",label:"搜索",id:"so-key-log",width:500,handler:thiso.query,placeholder:"输入订单ID或订单内容"}
            ]
        });
        grid.create();
        thiso.query();
    };

    o.query = function() {
        store.baseParams = {
            "status": get_select_value('status-sel'),
            "key": get_value("so-key-log")
        };
        grid.load();
    };

    return o;
});
