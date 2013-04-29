<script src='http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js'></script>
<script>
	$(".search-hide").click(function() {
		$(this).parent().parent().hide(300);
	});
	$("#search-toggle").click(function() {
		$("#search-simple").toggle(300);
		$("#search-advanced").toggle(300);
	});
	$("#search-field-select").change(function() {
		$("#" + $(this).val()).show(300);
		$(this).val("default");
	});
</script>
