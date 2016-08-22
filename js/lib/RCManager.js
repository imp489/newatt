
function RCManager(){
	this._menus = [];
};

RCManager.getInstance = function(){
	if( typeof RCManager._initialized == "undefined" ){
		RCManager._instance = new RCManager();
		RCManager._initialized = true;
	}
	return RCManager._instance;
}

RCManager.addMenu = function(menu){
	RCManager.getInstance()._menus.push(menu);
}

RCManager.hideAll = function(){

	var list = RCManager.getInstance()._menus;
	var len = list.length;
	
	for( var i=0; i<len; i++){
		list[i].RightClick('hideMenu');
	}
	
}

$(document).ready(function(){
	
	$(document).bind("contextmenu", function(e) {
		return false;
	});
	
	$(document).mouseup(function(evt) {
		RCManager.hideAll();
		evt.preventDefault();
	});
	
	
	$(document).on('selectstart dragstart','*',function(evt){
		evt.preventDefault();
		return false;
	});
	

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/*$(document).selectstart(function(evt) {
		return false;
	});*/
	/*$(document).mousedown(function(evt) {
		if( evt.target.is('select') ){
			
		}
		console.log( evt.target.is('select') );
		evt.preventDefault();
	});
	
	$(document).mousemove(function(evt) {
		evt.preventDefault();
	});*/
	
	/*$(document).bind('mousedown selectstart', function(e) {
	    return $(e.target).is('input, textarea, select, option');
	});*/


});