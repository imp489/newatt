
(function($){
	
	var config = {
		handler: null,
		menu_handler: null,
		beforeShowCallback: null,
		selectCallback: null,
		menu_target: null,
		open_after_select_item: false
	}
	var settings = {};
	
	var methods = {
	
		init: function(options){
			
			settings = $.extend(config,options);
			
			settings.handler = this;
			settings.menu_handler = $('#'+settings.id);
			settings.beforeShowCallback = options.callbacks.beforeShowCallback;
			settings.selectCallback = options.callbacks.selectCallback;
			
			RCManager.addMenu(settings.handler);
			
			settings.menu_handler.on('click','a',function(evt){
				if( settings.selectCallback!=null ){
					settings.selectCallback.func.apply(settings.selectCallback.ref,[$(this),settings.menu_target]);
				}
				return false;
			});
			
			if( settings.open_after_select_item ){
				settings.menu_handler.on('mouseup','a',function(evt){
					evt.preventDefault();
					return false;
				});
			}
			
			return this.each(function(){				
				
				//
				$(this).mouseup(function(event){
				
					if( event.which==3 ){						
						methods['showMenu'].apply(this,[event]);
						event.preventDefault();
						return false;
					}
					
				});
				//				
				
			});
		},
		
		showMenu: function(event){
			settings.menu_target = event.target;
			if( settings.beforeShowCallback!=null && !settings.beforeShowCallback.func.apply(settings.beforeShowCallback.ref,[settings.menu_handler]) ){
				return false;
			}
			settings.menu_handler.css({ top: event.pageY, left: event.pageX}).fadeIn('fast');
		},
		
		hideMenu: function(){
			settings.menu_handler.stop().fadeOut('fast');
		}
		
	};

	$.fn.RightClick = function(methodOrOptions){
		if ( methods[methodOrOptions] )
		{
			return methods[ methodOrOptions ].apply( this, Array.prototype.slice.call( arguments, 1 ));
		}
		else if ( typeof methodOrOptions === 'object' || ! methodOrOptions )
		{
			return methods.init.apply( this, arguments );
		}
		else
		{
			$.error( 'Method ' +  method + ' does not exist on jQuery.tooltip' );
		}
	}


}(jQuery));