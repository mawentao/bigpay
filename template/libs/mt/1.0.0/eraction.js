define("mt/eraction",function(require){
    var env = require("./env");
    var ErAction=require("er/Action");
    var o = new ErAction();
    o.parseErUrl = function(erurl) {
        var path = erurl.getPath();
        var ca = require("./util").getControllerAction(path);
        env.set("path", path);
        env.set("query", erurl.getQuery());
        env.set("controller", ca[0]);
        env.set("action", ca[1]);
    };
    o.on("enter",function(){
        var url = this.context.url;
        this.parseErUrl(url);
        var cm = "actions/"+env.getController();
        require([cm], function(c){
            var action = env.getAction()+"Action";
            c[action]();
        });
    });
    return o;
});
