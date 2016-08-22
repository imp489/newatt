<?php
	
	define('JS',dirname(__FILE__).'/');
	define('WEBROOT',realpath(JS.'../').'/');
	define('JSLIB',JS.'lib/');
	
	//
	include_once(WEBROOT.'config.php');
	//
	
	
	// files sequence to load
	$js_files = array(
		JSLIB.'Cookie',
		'jquery',
		'jquery.easing',
		'jquery.atooltip',
		'jquery.sidebar',
		'jquery.tree',
		'jquery.form',
		'jquery.constraint_table',
		'jquery.progressbar',
		'jquery.assign_table_course',
		'jquery.assign_table',
		'theme',
		JSLIB.'RCManager',
		JSLIB.'RCMenu',
		'mouse_events',
		'jquery.sortElements',
		'browser',
		'jquery.course_table'
	);
	//
	

	header('Content-type: text/javascript');
	
	foreach( $js_files as $js_file ){
		
				
		# execute and echo file content
		ob_start();
		include_once($js_file.'.js');
		$content = ob_get_clean();
		echo $content."\n";
		#
		
	}

?>

// 

