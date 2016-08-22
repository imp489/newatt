<?php
	
	$const_table = '<div class="cell">';
		$const_table .= '<table>';
			$const_table .= '<tr>';
				$const_table .= '<td class="every">&nbsp;</td>';
			$const_table .= '</tr>';
			$const_table .= '<tr>';
				$const_table .= '<td class="empty">&nbsp;</td>';
			$const_table .= '</tr>';
			$const_table .= '<tr>';
				$const_table .= '<td class="even">&nbsp;</td>';
			$const_table .= '</tr>';
			$const_table .= '<tr>';
				$const_table .= '<td class="odd">&nbsp;</td>';
			$const_table .= '</tr>';
			$const_table .= '<tr>';
				$const_table .= '<td class="dont">&nbsp;</td>';
			$const_table .= '</tr>';
		$const_table .= '</table/>';
	$const_table .= '</div>';
	
	$const_table_c = '<div class="cell">';
		$const_table_c .= '<table>';
			$const_table_c .= '<tr>';
				$const_table_c .= '<td class="every">&nbsp;</td>';
				$const_table_c .= '<td class="dont">&nbsp;</td>';
				$const_table_c .= '<td class="empty">&nbsp;</td>';
				$const_table_c .= '<td class="odd">&nbsp;</td>';
				$const_table_c .= '<td class="even">&nbsp;</td>';
			$const_table_c .= '</tr>';
		$const_table_c .= '</table>';
	$const_table_c .= '</div>';
	
	
	$const_table_cell = '';
		$const_table_cell .= '<table>';
			$const_table_cell .= '<tr>';
				$const_table_cell .= '<td class="every">&nbsp;</td>';
				$const_table_cell .= '<td class="dont">&nbsp;</td>';
				$const_table_cell .= '<td class="empty">&nbsp;</td>';
				$const_table_cell .= '<td class="odd">&nbsp;</td>';
				$const_table_cell .= '<td class="even">&nbsp;</td>';
			$const_table_cell .= '</tr>';
		$const_table_cell .= '</table>';
	$const_table_cell .= '';
	
	$cell_slider = '<div class="cell">';
		$cell_slider .= '<div class="label">';
			$cell_slider .= '&nbsp;';
		$cell_slider .= '</div>';
		$cell_slider .= '<div class="options">';
			$cell_slider .= '<div class="holder"><div class="space"> </div>'.$const_table_cell.'</div>';
		$cell_slider .= '</div>';
	$cell_slider .= '</div>';
	
	$const_table_a = '<div class="cell">';
		$const_table_a .= '<table>';
			$const_table_a .= '<tr>';
				$const_table_a .= '<td class="empty" colspan="2">&nbsp;</td>';
			$const_table_a .= '</tr>';
			$const_table_a .= '<tr>';
				$const_table_a .= '<td class="dont">&nbsp;</td>';
				$const_table_a .= '<td class="every">&nbsp;</td>';
			$const_table_a .= '</tr>';
			$const_table_a .= '<tr>';
				$const_table_a .= '<td class="odd">&nbsp;</td>';
				$const_table_a .= '<td class="even">&nbsp;</td>';
			$const_table_a .= '</tr>';
		$const_table_a .= '</table>';
	$const_table_a .= '</div>';
	
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
	
	
	function const_table_thead_rows()
	{
		global  $const_table,$day_times,$const_table_c,$const_table_a;
		
		$col_index = 1;
		
		echo '<tr>';
		
			//echo '<th data-col="'.($col_index++).'" rowspan="2">&nbsp;</th>';
			
			echo '<th class="a_const_table" data-col="'.($col_index++).'" rowspan="2" colspan="2">';
				echo $const_table_a;
			echo '</th>';
			
			foreach( $day_times as $day_time ){
				echo '<th class="col const_label" data-col="'.($col_index++).'">';
					echo $day_time;
				echo '</th>';
			}
			
		echo '</tr>';
		
		
		$col_index = 2;
		
		echo '<tr class="min_h">';
			
			foreach( $day_times as $day_time ){
				echo '<th class="c_const_table col min_h" data-col="'.($col_index++).'">';
					echo $const_table_c;
				echo '</th>';
			}
			
		echo '</tr>';
	}
	
	function const_table_tbody_rows()
	{
		global $days,$const_table, $day_times,$cell_slider;
		
		$day_index = 0;
		
		
		foreach( $days as $day ){
			
			$col_index = 0;
			
			echo '<tr>';
				
				echo '<td class="min day" data-col="'.($col_index++).'">';
					echo '<div class="cell">';
						echo $days[$day_index++];
					echo '</div>';
				echo '</td>';
				
				echo '<td class="min r_const_table row" data-col="'.($col_index++).'">';
					echo $const_table;
				echo '</td>';

				 foreach( $day_times as $day_time ){
					echo '<td class="const" data-col="'.($col_index++).'">'.$cell_slider.'</td>';
				}
				
			echo '</tr>';
			
		}
	}

?>

<br />

<div class="constraint_table" id="constraint_table">
	
	<table class="ctable">
	
		<thead class="header">
			<?php const_table_thead_rows(); ?>			
		</thead>
		
		<tbody>
			<?php const_table_tbody_rows(); ?>		
		</tbody>
	
	</table>

</div>

<script>
$(document).ready(function(){
	
	var _table = $('#constraint_table');
	_table.constraintTable();
	_table.constraintTable('setStatus','ooofppppppfffffffffnfffsffffffsffffefffffs');
	
	//console.log( _table.constraintTable('getStatus') );

});
</script>