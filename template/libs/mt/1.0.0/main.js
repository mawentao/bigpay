define("mt/main", function(require){
    require("./eraction");
    var env  = require("./env");
    var util = require("./util");
    var o = {};
    o.init = function(pathList) {
        pathList.push("/");
        var map = {"/":true};
        var actions = ["/"];
        for (var i=0; i<pathList.length; ++i) {
            var path = pathList[i];
            var ca = util.getControllerAction(path);
            var c = ca[0];
            var a = ca[1];
            path = "/"+c;
            if (typeof(map[path])=="undefined") {
                map[path] = true;
                actions.push(path);
            }
            path += "/"+a;
            if (typeof(map[path])=="undefined") {
                map[path] = true;
                actions.push(path);
            }
        }
        require("er").start();
        var r=require("er/controller");
        for (var i=0; i<actions.length; ++i) {
            var item = {path:actions[i],type:"mt/eraction"};
            r.registerAction(item);
        }
    };
    return o;
});
define("mt",["mt/main"],function(e){return e});
