define(function(require){
    var ajax = require("ajax");
    var o={};

    o.execute = function() {
        jQuery("#paybtn").click(callpay);
    };

    //调用微信JS api 支付
    function callpay()
    {
        //alert("callpay"); return;
        if (typeof WeixinJSBridge == "undefined"){
            if( document.addEventListener ){
                document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
            }else if (document.attachEvent){
                document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
                document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
            }
        }else{
            jsApiCall();
        }
    }

    function jsApiCall()
    {
        WeixinJSBridge.invoke('getBrandWCPayRequest',jsApiParameters,function(res){
            WeixinJSBridge.log(res.err_msg);
            if (res.err_msg=='get_brand_wcpay_request:ok') {
                payback();
            }
        });
    }

    // 支付完成后回调
    function payback() {
        if (rui!='') {
            window.location = rui;
        } else {
            show_panel(1);
        }
    };

    function show_panel(idx) {
        jQuery("[name=panel-div]").hide();
        jQuery("[name=panel-div]:eq("+idx+")").show();
    }

    return o;
});
