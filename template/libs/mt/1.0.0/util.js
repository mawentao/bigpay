define("mt/util", function(require){
    var o = {};

    /**
     * ��path����ȡcontroller��action��
     * path�ṹ��/controller/action
     **/
    o.getControllerAction = function(path) {
        var controller = "index";
        var action = "index";
        var arr = path.split("/");
        if (arr.length>1 && arr[1]!="") controller = arr[1];
        if (arr.length>2 && arr[2]!="") action = arr[2];
        return [controller, action];
    };

    return o;
});
