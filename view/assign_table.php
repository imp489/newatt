<?php

	$day_times = array(
		'09:10 - 07:30',
		'11:10 - 09:30',
		'13:40 - 12:00',
		'15:40 - 14:00',
		'17:40 - 16:00',
		'19:40 - 18:00'
	);
	$days = array(
		'شنبه',
		'یکشنبه',
		'دوشنبه',
		'سه ‌شنبه',
		'چهارشنبه',
		'پنجشنبه',
		'جمعه'		
	);

?>

<div class="assign_table" id="assign_table">
	
	<div class="clipboard">
	
	</div>
	
	<div class="data">

		<div class="loading">
			<div class="logo">
				
				<div class="progress">
					
				</div>
			</div>		
		</div>
		<div class="resources">
			<div class="courses">
				
				<div class="header">
					دروس
				</div>
				
				<div class="list">
				
					
					<div class="course every">
						<div class="parts">
							<div class="part every" data-type="every"></div>
							<div class="part every" data-type="every"></div>
						</div>
						<div class="c_holder">
							<span class="code">12061</span>
							<span class="name">ÙØ§Ø±Ø¢ÙØ±ÙÙÙ(ÙØ±ÙØ¯Ù ÙÙØ±84 ØšÙ ØšØ¹Ø¯)</span>
						</div>
					</div>
					
					
				
				</div>
				
				<div class="clr"> </div>
			</div>
			
			<div class="teachers">
				
				<div class="header">
					اساتید
				</div>
				
				<div class="list">
					
					
					<div class="teacher">
						<div class="t_holder">
							<div class="icon">-</div>
							<span class="name">Ø³ÛØ¯ ÙØ­Ø³Ù ÙÛØ±ÙÙÛØ¯Û</span>
						</div>
					</div>
					
					
					
				</div>
				
				<div class="clr"> </div>
			</div>
			
			<div class="rooms">
				
				<div class="header">
					اتاق‌ها
				</div>
				
				<div class="list">
					
					
					<div class="room">
						<div class="r_holder">
							<span class="name">class102-A</span>
						</div>
					</div>
					
					
				</div>
				
				<div class="clr"> </div>
			</div>
			
		</div>
		
		<div class="table">
		
			<table>
			
				<thead>
					<tr>
						<th class="empty">&nbsp;</th>
						
						<?php foreach( $day_times as  $day_time ) : ?>
						<th>
							<?php echo $day_time;  ?>
						</th>
						<?php endforeach; ?>
					</tr>
				</thead>
				
				<tbody>
				
					<?php foreach( $days as $day ) : ?>
					<tr>
						
						<td class="day min_w">
							
								<?php echo $day; ?>
							
						</td>
						
						<?php foreach( $day_times as  $day_time ) : ?>
						<td class="place">
							<div class="cell">
								
							</div>
						</td>
						<?php endforeach; ?>
						
					</th>
					<?php endforeach; ?>
				
				</tbody>
			
			</table>
		
		</div>
	
	</div>

</div>

<script>
$(document).ready(function(){

	$('#assign_table').assignTable();
	
});
</script>