(function($){

	var config = {
	
		_this: null,
		table_handler: null,
		thead_th_handler: null,
		thead_sortable_th_handler: null,
		tbody_handler: null,
		rows_number_handler: null,
		paginate_handler: null,
		paginate_span_handler: null,
		header_rc_menu_handler: null,
		div_scroller_handler: null,
		div_scroller_scroll_value:-1,
		document_scroller_scroll_value:-1,
		table_id: null,
		page: 1,
		total_pages: -1,
		total_rows: -1,
		header_rc_menu_id: 'table_menu',
		cookie_name: 'ct',
		cols_visible_status: [1,1,1,1,1,1,1,1,1,1,1,1],
		cols_sort_status: [],
		page_mouse_down_pos: {x:-1000,y:-1000},
		mouse_is_down: false
		
	}
	
	var settings = {};
	
	var methods = {
	
		init: function(options){
			
			settings = $.extend(config,options);
			
			settings._this = $(this);
			
			settings.table_handler = this;
			settings.table_id = this.selector;
			settings.tbody_handler = settings._this.find('tbody:first');
			settings.thead_th_handler = settings._this.find('thead:first th');
			settings.thead_sortable_th_handler = settings._this.find('thead:first th.sortable');
			settings.rows_number_handler = settings._this.find('.rows_number:first');
			settings.paginate_handler = settings._this.find('.footer:first .paginate:first');
			settings.paginate_span_handler = settings.paginate_handler.find('span.pages:first');
			settings.div_scroller_handler = settings._this.find('.auto_h_scroll:first');
			
			methods["_loadTableStatus"].apply(settings.table_handler);
			methods["_initHeaderRCMenu"].apply(settings.table_handler);
			methods["_initTableSorter"].apply(settings.table_handler);

			settings.rows_number_handler.change(function(){
				methods["_saveTableStatus"].apply(settings.table_handler);
				methods["update"].apply(settings.table_handler);
			});
			
			methods["_initMouseEvents"].apply(settings.table_handler);
			methods["_initCheckboxes"].apply(settings.table_handler);
			methods["_fixToolbars"].apply(settings.table_handler);
			

			return this.each(function(){
				
				// update table
				methods["update"].apply(settings.table_handler);
				//
			
			});
			
		},
		
		_loadTableStatus: function(){
		
			var data = Cookie.get(settings.header_rc_menu_id);
			if( data==undefined || data==null ){
				return;
			}			
			data = data.split('#');
			
			settings.rows_number_handler.prop("selectedIndex",data[0]);
			settings.cols_visible_status = data[1].split('|');
			
		},		
		
		_saveTableStatus: function(){
			
			var data = [];
			data.push(settings.rows_number_handler.prop("selectedIndex"));
			data.push( settings.cols_visible_status.join('|') );
			
			Cookie.set(settings.header_rc_menu_id,data.join('#'),365);
			
			//cookie_name
		},
		
		_initHeaderRCMenu: function(){
		
			settings.header_rc_menu_handler = $('#'+settings.header_rc_menu_id).find('ul:first');
			
			var items = [];
			
			$(this).find('thead:first tr:first th').each(function(){
				
				var th = $(this);			
				var label = $.trim( th.text() );
				var th_col_index = th.attr('data-col');
				
				if( label!="" && th.attr('data-disableable')!="no" ){
					
					var a_item = $('<a href="#" />');
					var li_item = $('<li />').append(a_item);
					a_item.html( '<span class="icon"><span></span></span>'+$(this).text() );					
					a_item.data('col_index',th_col_index).attr('data-col',th_col_index);
						
					settings.header_rc_menu_handler.append(li_item);
				}
				
				//console.log( settings.cols_visible_status );
				if( settings.cols_visible_status[th.attr('data-col')]==0 ){
					methods['hideColumn'].apply(settings.table_handler,[th.attr('data-col')]);
				}
				
				
			});
			//
			
			settings.thead_th_handler.RightClick({
				id:settings.header_rc_menu_id,
				open_after_select_item: true,
				callbacks:{
					beforeShowCallback: { ref: settings.table_handler, func:methods["_headerRCMenuBeforeShowCallback"]},
					selectCallback: { ref: settings.table_handler, func:methods["_headerRCMenuSelectCallback"]}				
				}
			});
			//
		},
		
		_initTableSorter: function(){
			
			settings.thead_sortable_th_handler.click(function(){
				methods["_sort"].apply(settings.table_handler,[$(this)]);
			});
			
			var found_sorted = false;
			
			settings.thead_sortable_th_handler.each(function(){
				if( $(this).hasClass('sortable') ){
					var sort_stat; // no sort
					//sort_stat = $(this).hasClass('asc') ? 1 : -1; // 1:asc, -1:desc
					
					if( $(this).hasClass('asc') ){
						sort_stat = 1;
						found_sorted = true;
					}else if( $(this).hasClass('desc') ){
						sort_stat = -1;
						found_sorted = true;
					}else{
						sort_stat = 0;
					}
					
					
					settings.cols_sort_status[ $(this).attr('data-col') ] = sort_stat;
					
				}	

								
			});
			
			//settings.cols_sort_status
			if( !found_sorted ){
				settings.cols_sort_status[1] = 1;
			}
			
			
			//console.log( settings.cols_sort_status );
		},
		
		_initMouseEvents: function(){
			
			settings.fix_ie_scroll_bug = (BrowserDetect.browser == "Explorer" && (BrowserDetect.version>=8)) ? -1 : 1;
			
			settings.tbody_handler.on('mousedown', 'tr', function(evt) {
				settings.mouse_is_down = true;
				settings.page_mouse_down_pos.x = evt.clientX;
				settings.page_mouse_down_pos.y = evt.clientY;
				
				//				
				settings.tbody_handler.addClass('disable_hover');
				$(this).addClass('hover');
				//

			});
			
			$(document).mouseup(function(e) {
				settings.mouse_is_down = false;
				settings.div_scroller_scroll_value = -1;
				settings.document_scroller_scroll_value = -1;
				settings.tbody_handler.removeClass('scrolling');
				
				//
				settings.tbody_handler.find('tr.hover').removeClass('hover');
				settings.tbody_handler.removeClass('disable_hover');
				//
				
			});
			
			settings.tbody_handler.on('mouseup', 'tr', function(evt) {			
				
				// validate click
				if( Math.abs(settings.page_mouse_down_pos.x-evt.clientX)>5 || Math.abs(settings.page_mouse_down_pos.y-evt.clientY)>5 ){
					return;
				}
				//
			
				var _tr = $(this);
				_tr.toggleClass('selected');
				
				var _checkbox = _tr.find('td.checkbox:first input');
				if( _tr.hasClass('selected') ){
					_checkbox.prop('checked', true);
				}else{
					_checkbox.prop('checked', false);
				}					
			
			});
			
			settings.tbody_handler.on('mousemove', 'tr', function(evt) {
			
				if( settings.mouse_is_down ){
				
					// scroll horiz
					if( settings.div_scroller_scroll_value==-1 ){
						settings.div_scroller_scroll_value = settings.div_scroller_handler.scrollLeft();
						settings.tbody_handler.addClass('scrolling');
					}
					var dx = (settings.page_mouse_down_pos.x-evt.clientX)*settings.fix_ie_scroll_bug;						
					settings.div_scroller_handler.scrollLeft( settings.div_scroller_scroll_value+dx );
					//
					
					// scroll vertical
					if( settings.document_scroller_scroll_value==-1 ){
						settings.document_scroller_scroll_value = $(document).scrollTop();
					}
					var dy = (settings.page_mouse_down_pos.y-evt.clientY);	
					$(document).scrollTop(settings.document_scroller_scroll_value+dy);
					//
					
				}				
				//evt.preventDefault();
				
			});
			
		},
		
		_initCheckboxes: function(){
			// checkbox
		},
		
		_fixToolbars: function(){
			var toolbars = settings._this.find('.toolbars .holder:first');
			var stickyHeaderTop = toolbars.offset().top - toolbars.height();
			var _hide = false;

			$(window).scroll(function(){
				if( $(window).scrollTop() > stickyHeaderTop ) {
					toolbars.addClass('fixed');
					
				} else {
					toolbars.removeClass('fixed');
				}
			});			
		},
		
		update: function(){
			
			// update table data
			
			//
			
		},
		
		gotoPage: function(selected_page){
			settings.page = selected_page;
			methods["update"].apply(settings.table_handler,[settings]);
		},
		
		hideColumn: function(index,validate){
			settings.table_handler.find('th[data-col="'+index+'"],td[data-col="'+index+'"]').addClass('disabled');
			settings.cols_visible_status[index]=0;
			methods["_saveTableStatus"].apply(settings.table_handler);
			settings.header_rc_menu_handler.find('a[data-col="'+index+'"]').addClass('unchecked');
		},
		
		showColumn: function(index,validate){
			settings.table_handler.find('th[data-col="'+index+'"],td[data-col="'+index+'"]').removeClass('disabled');
			settings.cols_visible_status[index]=1;
			methods["_saveTableStatus"].apply(settings.table_handler);
			settings.header_rc_menu_handler.find('a[data-col="'+index+'"]').removeClass('unchecked');
		},
		
		toggleColumn: function(index,validate){
			
			if( methods['_disableableColumn'].apply(settings.table_handler,[index]) ){
				if( settings.cols_visible_status[index]==1 ){
					methods['hideColumn'].apply(settings.table_handler,[index])
				}else{
					methods['showColumn'].apply(settings.table_handler,[index])
				}
			}
			
			//settings.table_handler.find('th[data-col="'+index+'"]:first').hasClass('disabled');
		},
		
		_sort: function(th_col,toggle){
			
			if( toggle==undefined ){
				toggle = true;
			}
			var col_index = th_col.attr('data-col');
			
			var sort_asc;
			if( toggle ){
				sort_asc = th_col.hasClass('asc') ? true : false;
			}else{
				sort_asc = th_col.hasClass('asc') ? false : true;
			}
			 
			
			
			
			settings.tbody_handler.find('td[data-col="'+col_index+'"]')
			.sortElements(function(a, b){
				return $.text([a]) > $.text([b]) ? (sort_asc ? -1 : 1) : (sort_asc ? 1 : -1);
				
			}, function(){
				return this.parentNode; 
			});
			
			settings.thead_sortable_th_handler.removeClass('asc desc');
			th_col.addClass( sort_asc ? 'desc' : 'asc' );
			
			methods["_colorizeRows"].apply(settings.table_handler);			
			
			settings.cols_sort_status[ col_index ] = sort_asc ? 1 : -1;			
		},		
		
		_headerRCMenuBeforeShowCallback: function(menu_handler){
			return true;
		},
		
		_headerRCMenuSelectCallback: function(selected_item,menu_target){
			methods['toggleColumn'].apply( settings.table_handler, [selected_item.data('col_index')]);
		},
		
		_disableableColumn: function(index){
			var target_th = settings.table_handler.find('th[data-col="'+index+'"]:first');
			return target_th.attr('data-disableable')!="no";
		},
		
		_visibleColumn: function(index){
			if( settings.cols_visible_status[index]==undefined || settings.cols_visible_status[index]==0 ){
				return false;
			}
			return true;
		},
		
		_colorizeRows: function(){
			settings.tbody_handler.find('tr.odd').removeClass('odd');
			settings.tbody_handler.find('tr:nth-child(2n+1)').addClass('odd');
		}
		
	}



	$.fn.courseTable = function(methodOrOptions){
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
