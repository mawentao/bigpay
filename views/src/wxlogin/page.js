define(function(require){
    var nav,o={};
    var pagedivs = ['login-div','userlist-div'];
    var inited = [0,0];
    var member_grid = require('./member_grid');
    o.execute = function(){
        nav = new MWT.Nav({
            render: "navdiv",
            cls: "mwt-nav-tabs",
            style: 'font-size:12px;padding:6px 10px;',
            items: [
                {title:"登陆入口", handler:o.active_login},
                {title:"微信用户", handler:o.active_userlist}
            ]
        });
        nav.create();
        o.active_login();
    };
    o.show_div = function(idx) {
        for(var i=0;i<pagedivs.length;++i) {
            hide(pagedivs[i]);
        }
        display(pagedivs[idx]);
    };
    o.active_login=function() {
        nav.active(0);
        o.show_div(0);
        if (!inited[0]) {
            inited[0] = 1;
            var loginurl = v.loginurl;
            var code = "<a href='"+loginurl+"' target='_blank'>"+loginurl+"</a>";
            jQuery('#login-url-td').html(code);
            
            require("common/qr").render('login-url-qr', loginurl, 200);
        }
    };
    o.active_userlist=function() {
        nav.active(1);
        o.show_div(1);
        if (!inited[1]) {
            inited[1] = 1;
            member_grid.init();
            member_grid.query();
        }
    };

    return o;
});
