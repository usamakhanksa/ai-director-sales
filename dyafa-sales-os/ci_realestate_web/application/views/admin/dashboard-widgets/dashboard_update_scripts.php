<script type="text/javascript">
	jQuery("document").ready(function($) {

		$(".dashboard-widget").each(function() {
			var thiss = $(this);
			var callback = thiss.attr('data-callback');
			console.log(callback);
			$.ajax({
				url: base_url + "admin_ajax",
				type: "POST",
				success: function(res) {
					console.log(thiss.find('.widget-count').text());
					thiss.find('.widget-count').text(res);

				},
				data: {
					callback: callback
				},
				cache: false,
				/**/
			});

		});


	});
</script>