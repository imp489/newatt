(function($){

	//
	var config = {
		
		select_root_callback: { obj_ref:null, func: null},
		select_parent_callback: { obj_ref:null, func: null},
		select_child_callback: { obj_ref:null, func: null},		
		anim_time: 400,		
		enable_root_item:false
		
	}
	var settings = {}
	//
	
	var gvars = {
		_this: null,
		tree_handler: null,		
		tree_ul_handler: null,		
		tree_root_ul_handler: null,
		tree_childs_ul_handler: null,
		tree_li_parent: null
	}

	
	var methods = {
		
		init: function(options){
			
			settings = $.extend(config,options);
			gvars.tree_handler = this;
			gvars._this = $(this);
			
			gvars.tree_ul_handler = gvars._this.find('ul');
			
			gvars.tree_root_ul_handler = gvars._this.find('> ul:first');
			gvars.tree_childs_ul_handler = gvars.tree_root_ul_handler.find('ul');
			
			

			methods['_detectTree'].apply(gvars.tree_handler);
			methods['_initMouseEvents'].apply(gvars.tree_handler);
			
			gvars.tree_li_parent = gvars.tree_childs_ul_handler.find('li.parent');
			
			return this.each(function(){
			
			});
			
		},
		
		_initMouseEvents: function(){
			
			function tree_behaviour(){
			
				var _a = $(this);
				var _li = _a.parent();
				var _ul = _li.find(' > ul');
				
				var callback_args = [_a];
				
				if( _ul.length==0 ){
					// you click on child
					if( settings.select_child_callback.obj_ref!=null ){
						settings.select_child_callback.func.apply( settings.select_child_callback.obj_ref, callback_args);
					}
				}
				else
				{
				
					// you click on parent or root
					if( _a.hasClass('root') )
					{
						if( settings.select_root_callback.obj_ref!=null && !settings.select_root_callback.func.apply(settings.select_root_callback.obj_ref,callback_args) ){
							return false
						}
					}
					else
					{
						if( settings.select_parent_callback.obj_ref!=null && !settings.select_parent_callback.func.apply(settings.select_parent_callback.obj_ref,callback_args) ){
							return false;
						}
					}
					//
				
					var is_anim = _ul.data('anim')==undefined || _ul.data('anim')==false ? false : true;
				
					if( !is_anim ){
						
						if( !_li.hasClass('close') )
						{
							_ul.data('anim',true);
							_li.removeClass('open').addClass('close');
							_ul.stop().slideUp(
								settings.anim_time,
								'easeInOutExpo',
								function(){_ul.data('anim',false);}
							);
							_a.addClass('close');
						}
						else
						{
							_ul.data('anim',true);
							_li.removeClass('close').addClass('open');
							_ul.stop().slideDown(
								settings.anim_time,
								'easeInOutExpo',
								function(){_ul.data('anim',false);}
							);
							_a.removeClass('close');
						}						
					}
				
					
				}
				
				return false;
				
			}
			
			
			if( settings.enable_root_item ){
				gvars.tree_ul_handler.on( 'click', 'a', tree_behaviour);
			}
			else
			{
				gvars.tree_ul_handler.on( 'click', 'a.root:first', function(){return false;});
				gvars.tree_ul_handler.on( 'click', '.childs a', tree_behaviour);
			}
			
			
		},
		
		_detectTree: function(){
			
			var _root_li = gvars._this.find('> ul:first li:first');
			
			_root_li.children('a:first').addClass('root');
			_root_li.children('ul:first').addClass('childs');
			
			_root_li.find('ul').each(function(){
				
				var _ul = $(this);
				var _parent = _ul.parent();
				_parent.children('a').addClass('parent');
				_parent.addClass('parent');
				_ul.children('li:last').addClass('last');
				
			});
			
			
			_root_li.find('a').prepend('<span class="icon"><span /></span>'); // add icons
			methods['_indexTree'].apply(gvars.tree_handler,[gvars.tree_root_ul_handler,'tree'+gvars.tree_handler.selector]);
			
			
		},
		
		
		collapse: function(){
			
			gvars.tree_li_parent.each(function(){
				
				var _li = $(this);
				var _a  = _li.children('a:first');
				var _ul  = _li.children('ul:first');
				
				
				var is_anim = _ul.data('anim')==undefined || _ul.data('anim')==false ? false : true;
				
				if( !is_anim ){
					
					if( !_li.hasClass('close') )
					{
						_ul.data('anim',true);
						_li.removeClass('open').addClass('close');
						_ul.stop().slideUp(
							settings.anim_time,
							'easeInOutExpo',
							function(){_ul.data('anim',false);}
						);
						_a.addClass('close');
					}

				}
				
			});
			
		},
		
		expand: function(){
			
			
			gvars.tree_li_parent.each(function(){
				
				var _li = $(this);
				var _a  = _li.children('a:first');
				var _ul  = _li.children('ul:first');
				
				
				var is_anim = _ul.data('anim')==undefined || _ul.data('anim')==false ? false : true;
				
				if( !is_anim ){
					
					if( _li.hasClass('close') )
					{
						_ul.data('anim',true);
						_li.removeClass('close').addClass('open');
						_ul.stop().slideDown(
							settings.anim_time,
							'easeInOutExpo',
							function(){_ul.data('anim',false);}
						);
						_a.removeClass('close');
					}

					
				}
				
			});
		
		},
 
		_indexTree: function(_ul,parent_index_id){
			
			var i = 0;
			_ul.children('li').each(function(){
				
				var _li = $(this);
				var id = parent_index_id+'.'+i;
				
				_li.attr('id',id);
				methods['_indexTree'].apply(gvars.tree_handler,[_li.children('ul'),id]);
				
				i++;
			});
			
		}
		
	}


	$.fn.tree = function(methodOrOptions){
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