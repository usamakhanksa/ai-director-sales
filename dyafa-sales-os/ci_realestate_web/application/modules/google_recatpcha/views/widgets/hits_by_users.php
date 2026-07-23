<div class="box hits-by-user-block box-solid">
	<div class="box-header">
	<i class="fa fa-inbox"></i>
	  <h3 class="box-title"><?php echo mlx_get_lang('Hits by Users'); ?>
	  </h3>
	</div>
	<div class="tab-content no-padding">
		<div class="chart tab-pane active" id="hitsByUsers" style="position: relative; height: 268px;"></div>
	</div>
	<div class="overlay">
	  <i class="fa fa-refresh fa-spin"></i>
	</div>
</div>	



<script>

	var hitsData = [];
	$(document).ready(function(){
		
		$.ajax({url: "<?php echo base_url().'admin/ajax_ga/show_ga_users_per_day';?>", 
			success: function(data){
				
		if(data != null){
			 var results = JSON.parse(data);
				var opt;			 
			 var sr=1;
			for(var i = 0; i < results.length; i++) {
				var obj = results[i];
				

				hitsData.push ( { y: obj.date, users: obj.users , new_users: obj.newusers}); 
				
			}	
			

			loadHitsByUsersChart();
		}
		
	  }});	
	});	
	
	function loadHitsByUsersChart(){
		
		
		  var area = new Morris.Area({
			element: 'hitsByUsers',
			resize: true,
			data: hitsData,
			xkey: 'y',
			xLabels: 'day',
			ykeys: ['users', 'new_users'],
			labels: ['Users', 'New Users'],
			lineColors: ['#a0d0e0', '#3c8dbc'],
			hideHover: 'auto'
		  });
		
		$('.hits-by-user-block .overlay').hide();
		
	}

</script>