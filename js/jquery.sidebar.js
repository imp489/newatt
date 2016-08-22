(function($){

	var settings = {
		min_width: <?php echo SIDEBAR_MIN_WIDTH; ?>,
		max_width: <?php echo SIDEBAR_MAX_WIDTH; ?>,
		anim_time: 400,
		anim_ease: "easeInOutExpo",
		status: "<?php echo SIDEBAR_DEFAULT_STATE; ?>",
		save_state: true,
		splitter_id: "sidebar_splitter",
		page_content_id: "page_content",
		select_callback: function(){}
	}
	
	var methods = {
		
		init: function(options){
			var settings = $.extend(settings,options); // merge settings and options
			var $this = this;
			
			return this.each(function(){
				
				var menu_element = $(this);
				
				// group elements
				menu_element.find('li.group').click(function(){
					
					<?php if(!SIDEBAR_MULTI_LEVEL) : ?>
						$(this).parent().find('.item_actived').removeClass('item_actived');
						$(this).addClass('item_actived');
						settings.select_callback($(this).find('> a').attr('href'));
					<?php else: ?>
					
						var li_element = $(this);
						var is_anim_play = li_element.data('slide_anim_play')==true ? true : false;
						var ul_element = li_element.children('ul');

						if( is_anim_play || ul_element.length==0 ){
							return;
						}
						
						var is_open = false; 
						if( li_element.hasClass('open') ){
							is_open = true;
						}
						else if( li_element.hasClass('close') ){
							is_open = false;
						}
						else
						{
							is_open = ul_element.height()==0 ? false : true;
						}
						
						li_element.data('slide_anim_play',true);
						if( is_open ){			
							ul_element.stop().slideUp(400,"easeInOutQuint",function(){
								li_element.data('slide_anim_play',false);
							});
							li_element.removeClass('open').addClass('close');
						}else{
							ul_element.stop().slideDown(400,"easeInOutQuint",function(){
								li_element.data('slide_anim_play',false);
							});
							li_element.removeClass('close').addClass('open');
						}
					
					<?php endif; ?>
					
					return false;
					
				});
				//
				
				// group item elements
				menu_element.find('li.item').click(function(){
					
					var li_element = $(this);
					
					li_element.parent().parent().parent().find('.selected').removeClass('selected');
					li_element.parent().parent().parent().find('.active').removeClass('active');
					li_element.addClass('selected');
					li_element.parent().parent().addClass('active');

					settings.select_callback($(this).find('> a').attr('href'));
					
					return false;
				});
				
				
				var last_state = Cookie.get("sidebar_menu_state");
				
				if( last_state=="min" ){
					methods["_min"].apply($this);
				}else if( last_state=="max" ){
					methods["_max"].apply($this);
				}else{
					methods["_<?php echo SIDEBAR_DEFAULT_STATE; ?>"].apply($this);
				}

				
			});
		},
 
		min: function(){
			return this.each(function(){
				
				if( settings.status!='min' ) {
					
					var page_content = $('#'+settings.page_content_id);
					var sidebar_splitter = $('#'+settings.splitter_id);
					
					page_content.stop().animate({'margin-right':settings.min_width},settings.anim_time,settings.anim_ease);
					sidebar_splitter.stop().animate({'right':settings.min_width-9},settings.anim_time,settings.anim_ease);
					
					settings.status = 'min';
					$(this).addClass('min');
					
					Cookie.set("sidebar_menu_state","min",365);
				}
				
			});
		},
 
		_min: function(){
			return this.each(function(){
				
				if( settings.status!='min' ) {
					
					var page_content = $('#'+settings.page_content_id);
					var sidebar_splitter = $('#'+settings.splitter_id);
					
					page_content.stop().css({'margin-right':settings.min_width});
					sidebar_splitter.stop().css({'right':settings.min_width-9});
					
					settings.status = 'min';
					$(this).addClass('min');
					
					Cookie.set("sidebar_menu_state","min",365);
				}
				
			});
		},
 
		max: function(){
			return this.each(function(){
				
				if( settings.status!='max' ) {
					
					var page_content = $('#'+settings.page_content_id);
					var sidebar_splitter = $('#'+settings.splitter_id);
					
					page_content.stop().animate({'margin-right':settings.max_width},settings.anim_time,settings.anim_ease);
					sidebar_splitter.stop().animate({'right':settings.max_width-9},settings.anim_time,settings.anim_ease);
					
					settings.status = 'max';
					$(this).removeClass('min');
					
					Cookie.set("sidebar_menu_state","max",365);
				}
				
			});
		},
 
		_max: function(){
			return this.each(function(){
				
				if( settings.status!='max' ) {
					
					var page_content = $('#'+settings.page_content_id);
					var sidebar_splitter = $('#'+settings.splitter_id);
					
					page_content.stop().css({'margin-right':settings.max_width});
					sidebar_splitter.stop().css({'right':settings.max_width-9});
					
					settings.status = 'max';
					$(this).removeClass('min');
					
					Cookie.set("sidebar_menu_state","max",365);
				}
				
			});
		},
 
		toggle: function(){
			
			var $this = this;
			
			return this.each(function(){
				
				if( settings.status!='min' ) {
					methods["min"].apply($this);
				}else{
					methods["max"].apply($this);					
				}
				
			});
		} /*,*/
		
	}
	
	$.fn.sidebarMenu = function(methodOrOptions){
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
