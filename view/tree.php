<div class="tree building">

	<ul>
		<li>
			<a href="#">ریشه</a>
			<ul>
				<li>
					<a href="#">نود ۱</a>
				</li>
				<li>
					<a href="#">نود ۲</a>
					<ul>
						<li>
							<a href="#">نود ۲.۱</a>
						</li>
						<li>
							<a href="#">نود ۲.۲</a>
						</li>
						<li>
							<a href="#">نود ۲.۳</a>
							<ul>
								<li>
									<a href="#">نود ۲.۱</a>
								</li>
								<li>
									<a href="#">نود ۲.۲</a>
								</li>
								<li>
									<a href="#">نود ۲.۳</a>
								</li>
								<li>
									<a href="#">نود ۲.۴</a>
								</li>
							</ul>
						</li>
						<li>
							<a href="#">نود ۲.۴</a>
						</li>
					</ul>
				</li>
				<li>
					<a href="#">نود ۳</a>
				</li>
				<li>
					<a href="#">نود ۴</a>
					<ul>
						<li>
							<a href="#">نود ۲.۱</a>
						</li>
						<li>
							<a href="#">نود ۲.۲</a>
						</li>
						<li>
							<a href="#">نود ۲.۳</a>
							<ul>
								<li>
									<a href="#">نود ۲.۱</a>
								</li>
								<li>
									<a href="#">نود ۲.۲</a>
								</li>
								<li>
									<a href="#">نود ۲.۳</a>
								</li>
								<li>
									<a href="#">نود ۲.۴</a>
								</li>
							</ul>
						</li>
						<li>
							<a href="#">نود ۲.۴</a>
						</li>
					</ul>
				</li>
			</ul>
		</li>
	</ul>
	
	<div class="control">
		<a href="#" class="collapse">collapse</a>
		<a href="#" class="expand">expand</a>
	</div>
	
</div>

<script>

$(document).ready(function(){
	
	$('.tree').tree();
	
	$('.tree .collapse:first').click(function(){
		$('.tree').tree('collapse');
		return false;
	});
	
	$('.tree .expand:first').click(function(){
		$('.tree').tree('expand');
		return false;
	});

	
});

</script>