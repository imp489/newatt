(function($){
	
	var default_options = {
		scroll_hit_height: 40,
		auto_scroll_step: 8 //10
	};
	var settings = {};

	var methods = {
	
		init: function(options){
			
			settings = $.extend(default_options,options);
		
			return this.each(function()
			{
				var _this = $(this);
				
				settings._resources_handler = _this.find('.resources:first');
				settings._loading_handler = _this.find('.loading:first');
				settings._courses_list_handler = _this.find('.courses:first .list:first');
				settings._teachers_list_handler = _this.find('.teachers:first .list:first');
				settings._rooms_list_handler = _this.find('.rooms:first .list:first');
				settings._slots_holder_list_handler = _this.find('td.place');
				settings._offset = _this.offset();
				
				methods['_init'].apply(this);
				methods['_loadDataComplete'].apply(this);
				methods['_initMouseEvents'].apply(this);
			
			});
		},
		
		_init: function(){
		
			var _this = $(this);
			
			// init progress bar			
			settings._progressbar_handler = settings._loading_handler.find('.progress').progressbar({
				finished_callback: { ref:this, func: methods['_loaded']}
			});			
			settings._progressbar_handler.progressbar('set',0,1);
			//		

			// load data
			//CourseDB.isLoaded();
			//TeacherDB.isLoaded();
			//BuildingDB.isLoaded();
			//
			
		},
		
		_loadData: function(){
			// progressbar
		},
		
		_loadDataComplete: function(){
			settings._progressbar_handler.progressbar('set',1,1000);
		},
		
		_loaded: function(){
			settings._loading_handler.fadeOut();
			methods['_updateCourses'].apply(this);
			methods['_updateTeachers'].apply(this);
			methods['_updateRooms'].apply(this);
		},
		
		
		_updateTeachers: function(){
			
			// update teachers list
			
			//
		},
		
		_updateCourses: function(){
			
			// update course list
			settings._courses_handler = settings._courses_list_handler.find('.course');
			settings._courses_handler.assingTableCourse();
			//
		
		},
		
		_updateRooms: function(){
		
			// update rooms list
			
			//
			
		},
		
		_updateTableSlots: function(){
		
		},
		
		
		_autoVerticalScroll: function(mouse_y){
			/*var  = evt.clientY;*/
					
			var _widnow = $(window);
			
			var scroll_top = _widnow.scrollTop();
			
			if( mouse_y<settings.scroll_hit_height )
			{
				_widnow.scrollTop(  _widnow.scrollTop()-settings.auto_scroll_step );				
			}
			else if( mouse_y>_widnow.height()-settings.scroll_hit_height )
			{	
				_widnow.scrollTop(  scroll_top+settings.auto_scroll_step );
				if( $(document).height()>settings._real_document_height ){
					_widnow.scrollTop(scroll_top);
				}			
			}
			
			//var changedY = scroll_top-_widnow.scrollTop();
			//settings.pageY -= changedY;
			
		},
		
		_stratAutoScroll: function(){
			
			settings._scrollInterval = setInterval(function(){
					
				if( settings._is_drag_item )
				{
					methods['_autoVerticalScroll'](settings.clientY);
				}
			
			},30);
			
		},
		_stopAutoScroll: function(){
			
			clearInterval(settings._scrollInterval);
			
		},
		
		_initMouseEvents: function(){
			
			var _this = $(this);
			
			settings.clientY = 0;
			settings.pageX = 0;
			settings.pageY = 0;
			settings._this = _this;
			
			// strat drag from resources to table
			settings._resources_handler.on('mousedown','.course,.teacher,.room',function(evt){
			
				var _target = $(this);
				
				settings.clientY = evt.clientY;
				settings.pageX = evt.pageX;
				settings.pageY = evt.pageY;
				
				settings._is_drag_item = true;
				
				settings._real_document_height = $(document).height();
				methods['_stratAutoScroll']();
				
				settings._drag_target = _target;
				settings._drag_target.addClass('dragging');
				
				var offset = _target.offset();
				settings._drag_target_offset = [evt.pageX - offset.left, evt.pageY - offset.top];
				
				// start drag event
				if( settings._drag_target.hasClass('course') ){
					methods['_courseDragStart'].apply(_this,[evt]);
				}
				else if( settings._drag_target.hasClass('teacher') ){
					methods['_teacherDragStart'].apply(_this,[evt]);
				}
				else if( settings._drag_target.hasClass('room') ){
					methods['_roomDragStart'].apply(_this,[evt]);
				}
				//
			
				evt.preventDefault();
				return false;
			
			});
			//
			
			// start drag data between table cells
			settings._slots_holder_list_handler.on('mousedown','.course_slot',function(evt){
				var _target = $(this);
				
				settings.clientY = evt.clientY;
				settings.pageX = evt.pageX;
				settings.pageY = evt.pageY;
				
				settings._is_drag_item = true;
				
				settings._real_document_height = $(document).height();
				methods['_stratAutoScroll']();
				
				settings._drag_target = _target;
				settings._drag_target.addClass('dragging');
				
				var offset = _target.offset();
				settings._drag_target_offset = [evt.pageX - offset.left, evt.pageY - offset.top];
				
				// start drag event
				if( settings._drag_target.hasClass('course_slot') ){
					methods['_courseSlotDragStart'].apply(_this,[evt]);
				}
				//
				
				evt.preventDefault();
				return false;
			});
			//			
			
			$(document).mousemove(function(evt){
				
				settings.clientY = evt.clientY;
				settings.pageX = evt.pageX;
				settings.pageY = evt.pageY;
				
				if( settings._is_drag_item ){
					
					if( settings._drag_target.hasClass('course') ){
						methods['_courseOnDrag'].apply(_this,[settings.pageX,settings.pageY]);
					}
					else if( settings._drag_target.hasClass('teacher') ){
						methods['_teacherOnDrag'].apply(_this,[settings.pageX,settings.pageY]);
					}
					else if( settings._drag_target.hasClass('room') ){
						methods['_roomOnDrag'].apply(_this,[settings.pageX,settings.pageY]);
					}
					else if( settings._drag_target.hasClass('course_slot') ){
						methods['_courseSlotOnDrag'].apply(_this,[settings.pageX,settings.pageY]);
					}
					
				}
				
				evt.preventDefault();
				return false;
			});
			
			$(document).mouseup(function(evt){
			
				if( !settings._is_drag_item ){
					return false;					
				}				
					
				methods['_stopAutoScroll']();
				
				settings._drag_target.removeClass('dragging');
				
				//
				if( settings._drag_target.hasClass('course') ){
					var _slot_holder = methods['_getTdCellUnderCursor']( evt.pageX, evt.pageY );
					if( _slot_holder.length!=0 ){
						methods['_courseDragFinish'].apply(settings._this,[_slot_holder]);
					}					
				}
				else if( settings._drag_target.hasClass('teacher') ){
					var _slot_holder = methods['_getElementUnderPosition']( '.table .place .course_slot', evt.pageX, evt.pageY );
					if( _slot_holder.length!=0 ){
						methods['_teacherDragFinish'].apply(settings._this,[_slot_holder]);
					}
				}
				else if( settings._drag_target.hasClass('room') ){
					var _slot_holder = methods['_getElementUnderPosition']( '.table .place .course_slot', evt.pageX, evt.pageY );
					if( _slot_holder.length!=0 ){
						methods['_roomDragFinish'].apply(settings._this,[_slot_holder]);
					}
				}
				else if( settings._drag_target.hasClass('course_slot') ){
					var _slot_holder = methods['_getTdCellUnderCursor']( evt.pageX, evt.pageY );
					if( _slot_holder.length!=0 ){
						methods['_courseSlotDragFinish'].apply(settings._this,[_slot_holder]);
					}else{
						methods['_courseSlotDragFailed']();
					}
				}				
				// 
				
				_this.find('.clipboard').empty();
				
				settings._is_drag_item = false;
				
			});		
			
			$(window).scroll(function(evt){
				
				if( settings._is_drag_item ){
					
					var scroll_top = $(this).scrollTop();
					
					if( settings._drag_target.hasClass('course') ){
						methods['_courseOnDrag'].apply(_this,[settings.pageX,scroll_top+settings.clientY]);
					}
					else if( settings._drag_target.hasClass('teacher') ){
						methods['_teacherOnDrag'].apply(_this,[settings.pageX,scroll_top+settings.clientY]);
					}
					else if( settings._drag_target.hasClass('room') ){
						methods['_roomOnDrag'].apply(_this,[settings.pageX,scroll_top+settings.clientY]);
					}
					
				}

			});
		
			
			// active select course type by part
			settings._courses_list_handler.on('mousedown','.part',function(evt){
				var _part = $(this);
				
				var target_type = _part.attr('data-type');
				var _course = _part.closest('.course');
				
				_course.removeClass('odd even every');
				_course.addClass(target_type);		
				
				
				
				evt.preventDefault();
				return false;
			});
			//
		
		},
		
		_getTdCellUnderCursor: function(x, y) {
		
			return settings._slots_holder_list_handler.filter(function(){
				
				var _cell = $(this);
				var offset = _cell.offset();
				
				var l = offset.left;
				var t = offset.top;
				var h = _cell.height();
				var w = _cell.width();

				var maxx = l + w;
				var maxy = t + h;

				return ( y <= maxy && y >= t) && (x <= maxx && x >= l) ? true : false;
			}).find('div.cell');
			
		},
		
		_getElementUnderPosition: function( selector, x, y){
			return settings._this.find(selector).filter(function(){
				
				var _cell = $(this);
				var offset = _cell.offset();
				
				var l = offset.left;
				var t = offset.top;
				var h = _cell.height();
				var w = _cell.width();

				var maxx = l + w;
				var maxy = t + h;

				return ( y <= maxy && y >= t) && (x <= maxx && x >= l) ? true : false;
			});
			// 
		},
		
		
		// Course Drag Events - Start
		_courseDragStart: function(evt){
			
			var _this = $(this);
			
			var _course_cpy = settings._drag_target.clone();;
		
			_this.find('.clipboard').html(_course_cpy);
			
			_course_cpy.addClass('dragging');
			
			settings._drag_target_shadow = _course_cpy;			
			
			methods['_courseOnDrag'].apply(_this,[evt.pageX,evt.pageY]);
		},
		
		_courseOnDrag: function(pageX,pageY){
			//var pos_x = evt.pageX - settings._dragging_course_target_offset[0] - settings._offset.left;
			//var pos_y = evt.pageY - settings._dragging_course_target_offset[1] - settings._offset.top;		
			if( settings._drag_target_shadow==null ){
				return;
			}
			var pos_x = pageX - settings._drag_target_offset[0] - settings._offset.left;
			var pos_y = pageY - settings._drag_target_offset[1] - settings._offset.top;		
			settings._drag_target_shadow.css({'top': pos_y, 'left':pos_x });
			
		},
		
		_courseDragFinish: function(_course_holder){
		
			if( methods['_isValidCourseDrop'].apply(settings._this,[ _course_holder]) ){
				methods['_courseDrop'].apply(settings._this,[ _course_holder]);
			}
		},
		
		_courseDrop: function(_course_holder){
			
			
			
		},
		
		_isValidCourseDrop: function( _course_holder){
		
			return true;
		},
		// Course Drag Events - End
		
		
		// Teacher Drag Events - Start
		_teacherDragStart: function(evt){
			
			var _this = $(this);
			
			var _teacher_cpy = settings._drag_target.clone();;
		
			_this.find('.clipboard').html(_teacher_cpy);
			
			_teacher_cpy.addClass('dragging');
			
			settings._drag_target_shadow = _teacher_cpy;			
			
			methods['_teacherOnDrag'].apply(_this,[evt.pageX,evt.pageY]);
		
		},
		
		_teacherOnDrag: function(pageX,pageY){
			if( settings._drag_target_shadow==null ){
				return;
			}
			var pos_x = pageX - settings._drag_target_offset[0] - settings._offset.left;
			var pos_y = pageY - settings._drag_target_offset[1] - settings._offset.top;		
			settings._drag_target_shadow.css({'top': pos_y, 'left':pos_x });
		},
		
		_teacherDragFinish: function(_course){
		
			if( methods['_isValidTeacherDrop'].apply(settings._this,[ _course]) ){
				methods['_teacherDrop'].apply(settings._this,[ _course]);
			}
		},
		
		_teacherDrop: function(_course){
		

		},
		
		_isValidTeacherDrop: function( _course){
			return true;
		},
		// Teacher Drag Events - End
		
		// Room Drag Events - Start
		_roomDragStart: function(evt){
			
			var _this = $(this);
			
			var _room_cpy = settings._drag_target.clone();;
		
			_this.find('.clipboard').html(_room_cpy);
			
			_room_cpy.addClass('dragging');
			
			settings._drag_target_shadow = _room_cpy;			
			
			methods['_roomOnDrag'].apply(_this,[evt.pageX,evt.pageY]);
		
		},
		
		_roomOnDrag: function(pageX,pageY){
			if( settings._drag_target_shadow==null ){
				return;
			}
			var pos_x = pageX - settings._drag_target_offset[0] - settings._offset.left;
			var pos_y = pageY - settings._drag_target_offset[1] - settings._offset.top;		
			settings._drag_target_shadow.css({'top': pos_y, 'left':pos_x });
		},
		
		_roomDragFinish: function(_course){
			if( methods['_isValidRoomDrop'].apply(settings._this,[ _course]) ){
				methods['_roomDrop'].apply(settings._this,[ _course]);
			}
		},
		
		_roomDrop: function(_course){

		},
		
		_isValidRoomDrop: function( _course){			
			return true;
		},
		// Room Drag Events - End
		
		// Drag Course Slot in table
		_courseSlotDragStart: function(evt){
			
			var _this = $(this);
			
			var width = settings._drag_target.width();
			var _course_slot_cpy = settings._drag_target.clone().css({width:width});
			
			
			_this.find('.clipboard').html(_course_slot_cpy);
			
			_course_slot_cpy.addClass('dragging');
			
			settings._drag_target_shadow = _course_slot_cpy;			
			
			methods['_courseSlotOnDrag'].apply(_this,[evt.pageX,evt.pageY]);
			
			settings._drag_target.css({visibility:'hidden'});
		},
		
		_courseSlotOnDrag: function(pageX,pageY){
			if( settings._drag_target_shadow==null ){
				return;
			}
			var pos_x = pageX - settings._drag_target_offset[0] - settings._offset.left;
			var pos_y = pageY - settings._drag_target_offset[1] - settings._offset.top;		
			settings._drag_target_shadow.css({'top': pos_y, 'left':pos_x });
		},
		
		_courseSlotDragFinish: function(_course_holder){
			if( methods['_isValidcourseSlotDrop'].apply(settings._this,[ _course_holder]) ){
				methods['_courseSlotDrop'].apply(settings._this,[ _course_holder]);
			}else{
				methods['_courseSlotDragFailed']();
			}
		},
		
		_courseSlotDrop: function(_course_holder){		
			var _course_slot_cpy = settings._drag_target.clone().css({visibility:'visible'});			
			_course_holder.append(_course_slot_cpy);			
			settings._drag_target.remove();			
		},
		
		_isValidcourseSlotDrop: function(_course_holder){
			return true;
		},
		
		_courseSlotDragFailed: function(){
			settings._drag_target.css({visibility:'visible'});
		}
		//
		
	};


	$.fn.assignTable = function(methodOrOptions){
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