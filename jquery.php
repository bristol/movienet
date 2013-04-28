<script src='http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js'></script>
<script>
	$(".search-hide").click(function() {
		$(this).parent().parent().hide(500);
	});
	$("#search-toggle").click(function() {
		$("#search-simple").toggle(500);
		$("#search-advanced").toggle(500);
	});
</script>
