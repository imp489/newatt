<?php
	
	$menu_items = array(
		
		
		array(
			'icon' => '',
			'label' => 'دروس',
			'link' => '#'
		),
		array(
			'icon' => '',
			'label' => 'اساتید',
			'link' => '#'
		),
		array(
			'icon' => '',
			'label' => 'اتاق‌ها',
			'link' => '#'
		),
		array(
			'icon' => '',
			'label' => 'گروه‌ها',
			'link' => '#'
		),
		array(
			'icon' => '',
			'label' => 'زمان‌ها',
			'link' => '#'
		),
		array(
			'icon' => '',
			'label' => 'برنامه‌ریزی دستی',
			'link' => '#'
		),
		array(
			'icon' => '',
			'label' => 'برنامه‌ریزی خودکار',
			'link' => '#'
		),
		array(
			'icon' => '',
			'label' => 'امتحانات',
			'link' => '#'
		),
		array(
			'icon' => '',
			'label' => 'گزارش‌ها',
			'link' => '#'
		),
		array(
			'icon' => '',
			'label' => 'تنظیمات عمومی',
			'link' => '#'
		),
		
		/*
		// group 1
		array(
			'icon' => '',
			'label' => 'گزینه اول',
			'link' => '#',
			'sub_items' => array(
				array(
					'icon' => '',
					'label' => 'زیر گزینه یک',
					'link' => '#',					
				),
				array(
					'icon' => '',
					'label' => 'زیر گزینه دو',
					'link' => '#',
					'selected' => true,
				),
				array(
					'icon' => '',
					'label' => 'زیر گزینه سه',
					'link' => '#',
				),
			)
		),
		//
		
		// group 2
		array(
			'icon' => '',
			'label' => 'گزینه دوم',
			'link' => '#',
			'sub_items' => array(
				array(
					'icon' => '',
					'label' => 'زیر گزینه یک',
					'link' => '#',
				),
				array(
					'icon' => '',
					'label' => 'زیر گزینه دو',
					'link' => '#',
				),
				array(
					'icon' => '',
					'label' => 'زیر گزینه سه',
					'link' => '#',
				),
				array(
					'icon' => '',
					'label' => 'زیر گزینه چهار',
					'link' => '#',
				),
			)
		),
		//
		
		// group 3
		array(
			'icon' => '',
			'label' => 'گزینه سوم',
			'link' => '#',
			'sub_items' => array(
				array(
					'icon' => '',
					'label' => 'زیر گزینه یک',
					'link' => '#',
				),
				array(
					'icon' => '',
					'label' => 'زیر گزینه دو',
					'link' => '#',
				),
				array(
					'icon' => '',
					'label' => 'زیر گزینه سه',
					'link' => '#',
				),
			)
		),
		// */
		
		
	);
	
?>

<ul id="menu_items" class="float_r">
	
	<?php $i=1; ?>
	<?php foreach( $menu_items as $menu_item ) : ?>
		<li class="group<?php echo $i==1 ? ' one' : ''; ?>">
			<a href="<?php echo $menu_item['link']; ?>" class="tooltip" title="<?php echo $menu_item['label']; ?>">
				<span class="icon"><?php echo $menu_item['icon']; ?></span>
				<span class="label">
					<?php echo $menu_item['label']; ?>
				</span>
			</a>
			
			<?php if( !empty($menu_item['sub_items']) ) : ?>
			<ul>
				
				<?php foreach( $menu_item['sub_items'] as $sub_item ) : ?>
				<li class="item<?php echo !empty($sub_item['selected']) ? ' selected' : ''; ?>">
					<a href="<?php echo $sub_item['link']; ?>" class="tooltip" title="<?php echo $sub_item['label']; ?>">
						<span class="icon"><?php echo $sub_item['icon']; ?></span>
						<span class="label">
							<?php echo $sub_item['label']; ?>
						</span>
					</a>
				</li>
				<?php endforeach; ?>
				
			</ul>
			<?php endif; ?>
		</li>
		<?php $i++; ?>
	<?php endforeach; ?>
	
	<a href="#" id="menu_items_control">on/off</a>
</ul>
<div class="clr"> </div>

<div class="clr"> </div>