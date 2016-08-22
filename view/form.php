<br />
<div class="form">

	<div class="header">
		عنوان فرم
	</div>

	<form class="thin">
		
		<ul class="input">
			<li class="one active" title="گام اول">
				<input type="text" name="test" />
				step1
				<br />
				step1
				<br />
				step1
				<br />
				step1
				<br />
			</li>
			<li title="گام دوم">
				step2
				<br />
				step2
				<br />
				step2
				<br />
				step2
				<br />
			</li>
			<li title="گام سوم">
				step3
				<br />
				step3
				<br />
				step3
				<br />
			</li>
			<li title="گام چهارم و ارسال">
				step4 and submit				
			</li>
		</ul>
		
	</form>	
	
</div>

<script>
$(document).ready(function(){

	$('.form').form();

});
</script>