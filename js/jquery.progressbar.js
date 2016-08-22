(function($){
	
	var default_options = {
		finished_callback: null
	};
	var settings = {};

	var methods = {
	
		init: function(options){
			
			settings = $.extend(default_options,options);
		
			return this.each(function(){
				
				var _this = $(this);;
				
				_this.addClass('progressbar');
				_this.html('');
				_this.append( $('<div class="holder"><div class="bar"> </div></div>') );
				//_this.insert(  );
				
			});
		},
		
		set: function(progress,time){
			
			progress = progress<0 ? 0 : progress;
			progress = progress>1 ? 1 : progress;			
			progress *= 100;
			
			if( time==undefined ){
				time = 400;
			}
			
			var _this = $(this);
			
			_this.find('.bar:first').stop().animate({'width':progress+'%'},time,function(){
				if( progress==100 && settings.finished_callback!=undefined ){
					settings.finished_callback.func.apply(settings.finished_callback.ref);
				}
			})
			
		}
	
	};


	$.fn.progressbar = function(methodOrOptions){
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

