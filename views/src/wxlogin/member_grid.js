define(function(require){
    var ajax=require("ajax");
    var store,o={};

    o.init = function(){
        var thiso = this;
        store = new MWT.Store({
            "url": ajax.getAjaxUrl('wxmember&method=query')
        });
        grid = new MWT.Grid({
            render: "usergrid-div",
            store: store,
            pagebar: false,
            bordered: true,
            pagebar: true,
            pageSize: 20,
            cm: new MWT.Grid.ColumnModel([
                {dataIndex:'headimgurl',head:'微信头像',width:60,align:'center',render:function(v,item){
                    return "<img src='"+v+"' style='width:40px;height:40px;'>";
                }},
                {dataIndex:'nickname',head:'微信昵称',width:100,sort:true,render:function(v,item){
                    return v;
                }},
                {dataIndex:'uid',head:'用户ID',width:60,sort:true,render:function(uid,item){
                    var url = v.siteurl+"/home.php?mod=space&uid="+uid;
                    return "<a href='"+url+"' target='_blank'>"+uid+"</a>";
                }},
                {dataIndex:'username',head:'用户名',render:function(username,item){
                    var url = v.siteurl+"/home.php?mod=space&uid="+item.uid;
                    return "<a href='"+url+"' target='_blank'>"+username+"</a>";
                }},
                {dataIndex:'addtime', head:'加入时间',width:150,sort:true,render:function(v){
                    return v;
                }},
                {dataIndex:'uptime', head:'更新时间',width:150,sort:true,render:function(v){
                    return v;
                }}
            ]),
            tbar: [
                {type:"search",label:"搜索",id:"so-key",width:500,handler:thiso.query,placeholder:"输入uid或用户名"}
            ]
        });
        grid.create();
        thiso.query();
    };

    o.query = function() {
        store.baseParams = {
            "key": get_value("so-key")
        };
        grid.load();
    };

    return o;
});
