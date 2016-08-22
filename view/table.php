
<div id="course_table" class="table">	
	
	
	<div class="toolbars">
		
		<div class="holder">
		
			<div class="toolbar base">
				<span class="label">عملیات اولیه:</span>
				<a href="#" class="add">&nbsp;</a>
				<a href="#" class="edit">&nbsp;</a>
				<a href="#" class="delete">&nbsp;</a>			
				<div class="clr"> </div>
			</div>
			
			<div class="toolbar conditions">
				<span class="label">قیدها:</span>
				<a href="#" class="prior">&nbsp;</a>
				<a href="#" class="term">&nbsp;</a>
				<a href="#" class="go">&nbsp;</a>
				<a href="#" class="stack">&nbsp;</a>
				<a href="#" class="time">&nbsp;</a>
				<a href="#" class="adm">&nbsp;</a>
				<a href="#" class="steps">&nbsp;</a>
				<div class="clr"> </div>
			</div>
			

			<div class="clr"> </div>
		
		</div>
		
	</div>
	
	<div class="header">
		جدول دروس
	</div>
	
	<div class="filter thin">
		<form>
			
			<label>کد و عنوان:</label>
			<input type="text" name="search" />
			
			<span class="space"> </span>
			
			<input type="checkbox" />
			<label>دروس ترم جاری</label>

			<span class="space"> </span>
			
			<input type="checkbox" />
			<label>دارای استاد</label>
			
			<span class="space"> </span>
			
			<input type="checkbox" />
			<label>دارای زمان</label>
			
			<span class="space"> </span>			
			
			<input type="checkbox" />
			<label>دارای اتاق</label>
			
			<span class="space"> </span>
			
			<input type="checkbox" />
			<label>دارای زیر گروه</label>	

			<span class="space"> </span>
			
			<label>نوع درس</label>
			<select>
				<option>همه</option>
				<option>عمومی</option>
				<option>پایه</option>
				<option>اختصاصی</option>
				<option>اصلی</option>
				<option>اختیاری</option>
			</select>
			
			<div class="float_l">
				<label>تعداد سطرها:‌ </label>
				<select class="rows_number">
					<option value="10">10</option>
					<option value="25">25</option>
					<option value="50">50</option>
					<option value="100">100</option>
				</select>
			</div>
			
		</form>
		
		<div class="clr"> </div>
	</div>
	
	<div class="clr"> </div>
	
	<div class="scroll_holder thin">
		<div class="auto_h_scroll">
			<form>
				<table>
				
					<thead>
						<tr>
							<th data-col="0" rowspan="2"  class="min one"><div class="cell check fix_height"><input type="checkbox" /></div></th>
							<th data-col="1" rowspan="2"  class="min sortable"><div class="cell code">کد</div></th>
							<th data-col="2" rowspan="2"  class="sortable asc" data-disableable="no"><div class="cell title">عنوان</div></th>
							<th data-col="3" rowspan="2"  class="min sortable"><div class="cell type">نوع</div></th>
							<th data-col="4" colspan="2"  class="min"><div class="cell units">تعداد واحد</div></th>
							<th data-col="5" colspan="2"  class="min"><div class="cell teachers">تعداد اساتید</div></th>
							<th data-col="6" rowspan="2"  class="min"><div class="cell sims">هم‌نیاز</div></th>
							<th data-col="7" rowspan="2"  class="min"><div class="cell pref">پیش‌نیاز</div></th>
							<th data-col="8" colspan="2"  class="min"><div class="cell room">اتاق</div></th>
							<th data-col="9" rowspan="2"  class="min"><div class="cell hardness">میزان سختی</div></th>
							<th data-col="10" rowspan="2" class="min"><div class="cell codes">مشخصه‌ها</div></th>
							<th data-col="11" rowspan="2" class="min"><div class="cell group">گروه</div></th>
						</tr>
						<tr class="sub">
							<th data-col="4" class="min">تئوری</th>
							<th data-col="4" class="min">عملی</th>
							<th data-col="5" class="min">تئوری</th>
							<th data-col="5" class="min">عملی</th>
							<th data-col="8" class="min">تئوری</th>
							<th data-col="8" class="min">عملی</th>
						</tr>
					</thead>

					<tbody>
						<?php include('_data_table_content.php'); ?>
					</tbody>
					
					
				</table>
			</form>
		</div>
	</div>
	<div class="clr"> </div>
	<div class="footer">
		<div class="status">status</div>
		<div class="paginate">
			<a href="#frist">ابتدا</a>
			<a href="#prev">قبل</a>
			<span class="pages">
			
			</span>
			<a href="#next">بعد</a>
			<a href="#last">انتها</a>
		</div>
		<div class="clr"> </div>
	</div>

</div>

<script type="text/javascript">
	$(document).ready(function(){		
		
		$('#course_table').courseTable();
		
	});
	
</script>



<div id="table_menu" class="rc_menu courses">
	<ul>		
	</ul>
</div>
