define("mt/env", function(require){
	var o = {};

    /////////////////////////////////
	//  Env管理的数据
	/////////////////////////////////
	var data = {};
	o.get = function(k) { return data[k]; }
	o.set = function(k, v) { data[k] = v; }
    o.debug = function() { print_r(data);}

    o.getPath = function(){return this.get("path");};
    o.getController = function(){return this.get("controller");};
    o.getAction = function(){return this.get("action");};
    o.getQuery = function(){return this.get("query");};

    /////////////////////////////////
	// Env提供公共函数
    /////////////////////////////////
	o.time = function(){ var ms=new Date().getTime(); return parseInt(ms/1000); }
	o.mstime = function(){ return new Date().getTime(); }

    /////////////////////////////////
	// 按下Enter键的事件
    /////////////////////////////////
    o.enterListeners = {}; 
    o.onenter = function(domid, func) {
        if (!this.enterListeners[domid]) {
            this.enterListeners[domid]=[];
        }   
        for (var i=0; i<this.enterListeners[domid].length; ++i) {
            if (func==this.enterListeners[domid][i])
                return;
        }   
        this.enterListeners[domid].push(func);
    }   
    o.unenter = function(domid) { delete this.enterListeners[domid]; }
    o.fireenter = function(domid) {
        if (!this.enterListeners[domid]) { return; }
        var arr=this.enterListeners[domid];
        for (var i=0; i<arr.length; ++i) {
            var fun=arr[i];
            fun.call(fun, domid);
        }   
    }   
    document.onkeydown = function(e) {    
        var theEvent = e || window.event;  
        var code = theEvent.keyCode || theEvent.which || theEvent.charCode;  
        if (code == 13) {
            var activeElementId = document.activeElement.id;
            o.fireenter(activeElementId);
        }
    };

	return o;
});
define("env",["mt/env"],function(e){return e});
