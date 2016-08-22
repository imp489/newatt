(function($){

	var default_options = {
	
	};
	var settings = {};

	var methods = {
	
		init: function(options){
			
			settings = $.extend(default_options,options);
		
			return this.each(function(){
				
				methods['_initMouseEvents'].apply(this);
				
			});
		},
		
		_initMouseEvents: function(){
			
			var _this = $(this);
			
			
			
		}
		
		
		
	
	};



	$.fn.assingTableCourse = function(methodOrOptions){
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