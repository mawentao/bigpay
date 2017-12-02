define(function(require){
    var nav,o={};
    var pagedivs = ['payorder-div','paylog-div'];
    var inited = [0,0];
    var payorder_grid = require('./payorder_grid');
    var paylog_grid = require('./paylog_grid');
    o.execute = function(){
        nav = new MWT.Nav({
            render: "navdiv",
            cls: "mwt-nav-tabs",
            style: 'font-size:12px;padding:6px 10px;',
            items: [
                {title:"支付订单",handler:o.active_payorder},
                {title:"支付记录",handler:o.active_paylog}
            ]
        });
        nav.create();
        o.active_payorder();
    };
    o.show_div = function(idx) {
        for(var i=0;i<pagedivs.length;++i) {
            hide(pagedivs[i]);
        }
        display(pagedivs[idx]);
    };
    o.active_payorder=function() {
        nav.active(0);
        o.show_div(0);
        if (!inited[0]) {
            inited[0] = 1;
            payorder_grid.init();
			payorder_grid.query();
        }
    };
    o.active_paylog=function() {
        nav.active(1);
        o.show_div(1);
        if (!inited[1]) {
            inited[1] = 1;
            paylog_grid.init();
			paylog_grid.query();
        }
    };
    return o;
});
