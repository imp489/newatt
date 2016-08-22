<?php
	
	include('lib/functions.php');	
	
	define('WEBROOT',dirname(__FILE__).'/');
	define('LIB',WEBROOT.'lib/');
	
	include_once(LIB.'public.php');
	include_once('config.php');
	
?>

<!DOCTYPE html>
<html lang="fa">

	<head>
		<title>new theme</title>
		
		<meta charset="utf-8" />		
		<link rel="stylesheet" href="css/style.css" type="text/css">
		<script type="text/javascript" src="js/script.js"></script>
		
		<!--[if IE 8]>
		<link rel="stylesheet" href="css/ie8.css" type="text/css">
		<![endif]-->
		<!--[if IE 7]>
		<link rel="stylesheet" href="css/ie7.css" type="text/css">
		<![endif]-->
			
	</head>
	
	<body>
		
		<div id="container">
		
			<div id="header">			
				<div class="logo float_r">logo</div>
				<div class="messages float_l">messages</div>
				<div class="clr"> </div>
			</div>
		
			
			<div id="page">

				<div id="sidebar" class="float_r">
					<div class="top_padding"> </div>
					<div class="space top"> </div>
					<?php get_element('right_menu'); ?>
				
				</div>
				
				<div id="sidebar_splitter"> </div>
				
				<div id="page_content">
					
					<div class="top_padding"> </div>
					<div class="clr"> </div>
					
					<div id="content">
						<?php
							//include "view/assign_table.php";
							//include "view/tree.php";
							//include "view/table.php";
							//include "view/form.php";
							include "view/constraint_table.php";
						?>
					</div>
					
					<div id="footer">
						<div class="line"> </div>
						&copy; 2013
					</div>
				
				</div>
				
				<div class="clr"> </div>
			</div>
			
		</div>
		
	</body>
	
</html>