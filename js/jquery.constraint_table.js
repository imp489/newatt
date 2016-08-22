(function($){

	var default_settings = {
	
	};
	var settings = {};


	var methods = {
		
		init: function(options){
			
			settings = $.extend(default_settings,options);
			
			return this.each(function(){			
			
				methods['_initHover'].apply(this);
				methods['_initConstControl'].apply(this);
				methods['_initCellSlider'].apply(this);
			
			});
		},
		
		
		_initHover: function(){
			
			var _this = $(this);			
			
			var _td_const = _this.find('td.const,th');
			
			// table top rows mouse over event
			_this.find('th, th td, th table').hover(function(){
				
				var _th = $(this).closest('th');
				
				//
				var col_index = _th.attr('data-col');
				_td_const.filter(function(){ return $(this).attr('data-col')!=col_index; }).removeClass('hover');
				_td_const.filter(function(){
					return $(this).attr('data-col')==col_index && !$(this).hasClass('hover');
				}).addClass('hover');
				//				
				
			});
			
			var _thead = _this.find('thead.header');
			_this.find('th').mouseout(function(evt){				
				if( evt.pageY>_thead.offset().top && evt.pageY<_thead.offset().top+_thead.height() ){
					return false;
				}
				_td_const.filter(function(){ return $(this).hasClass('hover') }).removeClass('hover');				
			});
			//
			
			var _td_day = _this.find('td.day');
			
			// table right mouse over
			_this.find('td.day, td.r_const_table').mouseenter(function(){
				
				var _th_td_const = $(this).parent().find('td.const,td.day');
				//

				//
				_td_const.filter(function(){ return $(this).hasClass('hover') }).removeClass('hover');
				_td_day.filter(function(){ return $(this).hasClass('hover') }).removeClass('hover');
				_th_td_const.filter(function(){ return !$(this).hasClass('hover') }).addClass('hover');
				//
				
				
			});
			
			
			_this.find('td.day, td.r_const_table').mouseleave(function(evt){

				/*var _tbody_min_y = _this.find('tbody:first').offset().top;
				var _tbody_max_y = _this.find('tbody:first').offset().top + _this.find('tbody:first').height();
				var _td_min_x = _this.find('td.r_const_table:first').offset().left;
				var _td_max_x = _this.find('td.day:first').offset().left+_this.find('td.day:first').width();
				
				if( evt.pageX>_td_min_x && evt.pageX<_td_max_x && evt.pageY>_tbody_min_y && evt.pageY<_tbody_max_y ){
					return false;
				}*/
				
				_td_const.filter(function(){ return $(this).hasClass('hover') }).removeClass('hover');
				_td_day.filter(function(){ return $(this).hasClass('hover') }).removeClass('hover');
				
			});
			//
			
			// table all mouse over
			var _td_const1 = _this.find('td.const');
			var _th_const_table_a = _this.find('th.a_const_table');
			
			_th_const_table_a.mouseover(function(){
				_td_const1.filter(function(){ return !$(this).hasClass('hover') }).addClass('hover');
			});
			
			//var _th_const_table_a_width = _th_const_table_a.width();
			//var _th_const_table_a_height = _th_const_table_a.height();
			
			_th_const_table_a.mouseleave(function(evt){
				
				/*var min_x = _th_const_table_a.offset().left;
				var max_x =min_x + _th_const_table_a_width ;
				
				var min_y = _th_const_table_a.offset().top;
				var max_y =min_y + _th_const_table_a_height ;
				
				if( evt.pageX>min_x && evt.pageX<max_x && evt.pageY>min_y && evt.pageY<max_y ){
					return false;
				}
				
				alert( (evt.pageX>min_x && evt.pageX<max_x && evt.pageY>min_y && evt.pageY<max_y) ? 'true' : 'false' );*/				
				
				_td_const1.filter(function(){ return $(this).hasClass('hover') }).removeClass('hover');
			});
			//
			
			
		},
		
		
		_initConstControl: function(){
			
			var _this = $(this);
			
			// active row const
			_this.find('td.r_const_table.row td').click(function(){
			
				var _td = $(this);
				var _tr = _td.parent().parent().parent().parent().parent().parent();
				
				var _td_const = _tr.find('td.const:not(.disable)');
				_td_const.removeClass('empty every even odd dont');
				_td_const.addClass( _td.attr('class') );
				_td_const.find('div.label').html( methods['_translate'](_td.attr('class')) );
				
			});
			//	

			// active col const
			_this.find('th.c_const_table.col td').click(function(){
			
				var _td = $(this);
				
				var _th = _td.parent().parent().parent().parent().parent();
				var col_index = _th.attr('data-col');
				
				var _td_const = _this.find('td.const[data-col="'+col_index+'"]:not(.disable)');
				
				_td_const.removeClass('empty every even odd dont');
				_td_const.addClass( _td.attr('class') );
				_td_const.find('div.label').html( methods['_translate'](_td.attr('class')) );
				
				
			});
			//
			
			// active cell const
			_this.find('td.const td').click(function(){
			
				var _td = $(this);
				
				var _td_const = _td.parent().parent().parent().parent().parent().parent().parent();				
				
				_td_const.removeClass('empty every even odd dont');
				_td_const.addClass( _td.attr('class') );
				_td_const.find('div.label').html( methods['_translate'](_td.attr('class')) );
				
			});
			//
			
			// active table control
			
			_this.find('th.a_const_table td').click(function(){
				var _td_const = _this.find('td.const:not(.disable)');
				var _td = $(this);
				_td_const.removeClass('empty every even odd dont');
				_td_const.addClass( _td.attr('class') );
				_td_const.find('div.label').html( methods['_translate'](_td.attr('class')) );
				
			});
			//
			
		},
 
		_initCellSlider: function(){
			
			var _this = $(this);
			
			var _td_cell = _this.find('td.const div.cell');
			
			_td_cell.mouseenter(function(){
				
				
				/*function rnd_number( min, max){
					
				}*/
				var _this = $(this);

				if( _this.parent().hasClass('disable') ){
					return false;
				}
				
				//var classes = [ 'up', 'down', 'right', 'left'];				
				//var rnd_class = classes[ methods['_rnd_numer'](0,3) ];
				
				
				/*if(jQuery(this).hasClass("right")){
					jQuery(this).children('.slideFirst').stop().animate({"margin-left":jQuery(this).children(".slideSecond").width()+0},400);
					jQuery(this).children(".slideSecond").stop().animate({"left":0},400);
				}
				else if(jQuery(this).hasClass("left")){
					jQuery(this).children('.slideFirst').stop().animate({"margin-left":-jQuery(this).children(".slideSecond").width()-0},400);
					//jQuery(this).children(".slideSecond").stop().animate({"left":"-="+(jQuery(this).children(".slideSecond").width()+0)},400);
					jQuery(this).children(".slideSecond").stop().animate({"left":0},400);
				}
				
				else if(jQuery(this).hasClass("down")){
					jQuery(this).children('.slideFirst').stop().animate({"margin-top":jQuery(this).children(".slideSecond").height()+0},400);
					jQuery(this).children(".slideSecond").stop().animate({"top":0},400);
				}else*/
				//if( _this.hasClass("up") ){
					//jQuery(this).children('.slideFirst').stop().animate({"margin-top":-jQuery(this).children(".slideSecond").height()-0},400);
					//jQuery(this).children(".slideSecond").stop().animate({"top":"-="+(jQuery(this).children(".slideSecond").height()+0)},400);
					
				//}
				
				_this.children(".options").stop().animate({"top":'-'+_this.children(".options").height()},300);
				//_this.children(".label").stop().animate({"top":'-'+_this.children(".label").height()},300);
			
			}).mouseleave(function(){
				var _this = $(this);
				//_this.children(".label").stop().animate({"top":0},300);
				_this.children(".options").stop().animate({"top":_this.height()},300);
			
			});
		},
		
		_rnd_numer: function( min, max){
			return Math.floor(Math.random() * (max - min + 1)) + min;
		},
 
		_translate: function( label){
			
			label = $.trim(label);
			
			switch(label){
				case 'every':
					return 'هر هفته';
					break;
				case 'even':
					return 'زوج'
					break;
				case 'odd':
					return 'فرد';
					break;
				case 'dont':
					return 'ترجیحا، نه';
					break;
				case 'disable':
					return 'غیرفعال';
					break;
			}

			return '&nbsp;';
		},
		
		_translateChar2Class: function(chr)
		{
			switch(chr)
			{
				case 's':
					return 'every';
					break;
				case 'p':
					return 'dont';
					break;
				case 'n':
					return 'disable';
					break;
				case 'e':
					return 'even';
					break;
				case 'o':
					return 'odd';
					break;
			}
			
			return 'empty';
		},
		
		_translateClass2Char: function(_td)
		{
			if( _td.hasClass('disable') ){
				return 'n';
			}
			else if( _td.hasClass('every') ){
				return 's';
			}
			else if( _td.hasClass('odd') ){
				return 'o';
			}
			else if( _td.hasClass('even') ){
				return 'e';
			}
			else if( _td.hasClass('dont') ){
				return 'p';
			}
			
			return 'f';
		},
		
		setStatus: function(status){
		
			var _this = $(this);
			var len = status.length;
			var index =0;
			
			var _td_const = _this.find('td.const');
			
			_td_const.each(function(){
				
				if( index>41 ){
					return;
				}
				
				var _td = $(this);
				_td.removeClass('empty every even odd dont disable');
				var new_class = methods['_translateChar2Class'](status[index++]);				
				_td.addClass(new_class);
				_td.find('div.label').html( methods['_translate'](new_class) );
		
			});
			
		},
		
		getStatus: function(){
			
			var _this = $(this);
			var status = [];
			
			var index =0;
			
			var _td_const = _this.find('td.const');
			
			_td_const.each(function(){
				
				if( index>41 ){
					return;
				}
				
				var _td = $(this);
				
				status.push( methods['_translateClass2Char'](_td) );
				/*_td.removeClass('empty every even odd dont disable');
				var new_class = methods['_translateChar2Class'](status[index++]);				
				_td.addClass(new_class);
				_td.find('div.label').html(  );*/
		
			});
			
			
			return status.join('');
		}
	
	};


	$.fn.constraintTable = function(methodOrOptions){
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