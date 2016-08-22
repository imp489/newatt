
$(document).ready(function(){
	
	$('a.tooltip').aToolTip({fixed: true,xOffset:125});
	
	$('#menu_items').sidebarMenu();
	
	
	$('#menu_items_control').click(function(){
		$('#menu_items').sidebarMenu("toggle");
		return false;
	});
	
});
