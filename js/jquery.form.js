(function($){

	//
	var default_settings = {
	
	};
	var settings = {};
	//
	
	var methods = {
	
		init: function(options){			
			
			settings = $.extend(default_settings,options);
			
			return this.each(function(){
			
				//
				methods['_initStepsForm'].apply(this);
				methods['_initFormControl'].apply(this);
				//
			
			});
			
		},
		
		_initStepsForm: function(){
			
			var _this = $(this);
			var _ul = _this.find('ul.input:first');
			var _control = $('<div class="control" />');
			
			if( _ul.length==0 ){
				return false;
			}
			
			// create form control buttons
			var _prev_btn = $('<input class="btn prev"  type="button" value=" مرحله قبل" />');
			var _next_btn = $('<input class="btn next"  type="button" value="مرحله بعد" />');			
			
			_control.append(_prev_btn);
			_control.append(_next_btn);
			
			_control.insertAfter(_ul);
			//
			
			// create steps
			var _div_ul = $('<ul />')
			var _div = $('<div class="steps" />').append(_div_ul);
			
			var i = 1;
			_ul.children('li').each(function(){
				
				var step = 'step'+i;
				var _li = $(this).css({display:'none'}).addClass(step);
				var _a = $('<a href="#" />').text( _li.attr('title') ).attr('data-step',step);	

				_a.click(function(){
					
					//
					if( _a.hasClass('current') ){
						return false;
					}
					//
					
					//
					_div_ul.find('.current').removeClass('current');						
					_a.addClass('current');
					//
					
					//
					var cls = 'li.'+$(this).attr('data-step')+':first';
					var target = _ul.find(cls);
					
					
					var current_form = _ul.find('.current');
					//

					//
					if( current_form.length==0 ){
						target.addClass('current').fadeIn('fast');
					}
					else
					{
						current_form.removeClass('current').fadeOut('fast',function(){	
							target.addClass('current').fadeIn('fast');
						});
					}
					//
					
					// set prev and nex buttons state
					_prev_btn.removeClass('disabled').prop('disabled',false);
					_next_btn.removeClass('disabled').prop('disabled',false);
					
					var _a_parent = _a.parent();
					var _submit = _control.find('input[type="submit"]');
					
					if( _a_parent.index()==0 )
					{
						_prev_btn.addClass('disabled').prop('disabled',true);
						_submit.fadeOut();
						
					}
					else if( _a_parent.next().children('a:first').length==0 )
					{
						_next_btn.addClass('disabled').prop('disabled',true);
						_submit.fadeIn();
					}else{
						_submit.fadeOut();
					}
					
					return false;
				});
				
				_div_ul.append( $('<li />').append(_a) );
				
				i++;
			});
			_div.insertBefore(_ul);
			//
			
			

			// active form control buttons
			_prev_btn.click(function(){
				var _a = _div_ul.find('a.current');
				_a.parent().prev().children('a:first').click();
			});
			_next_btn.click(function(){
				var _a = _div_ul.find('a.current');
				_a.parent().next().children('a:first').click();
			});
			//
			
			// active first step
			_div_ul.find('li:first a').click();			
			//
			
			return true;
		},
		
		
		_initFormControl: function(){
		
			var _this = $(this);
			var _form = _this.children('form:first');
			var _control = _form.children('div.control');
			
			var _submit_btn = $('<input class="btn btn-danger"  type="submit" value="ارسال فرم" />').css('display','none');
			_control.append(_submit_btn);			

			
			_form.submit(function(){
				if( settings.onSubmit!=null )
				{
					settings.onSubmit.func.apply( settings.onSubmit.ref, [ _form.serialize() ]);
				}
				
				return false;
			});
			
		}
		
	};


	$.fn.form = function(methodOrOptions){
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