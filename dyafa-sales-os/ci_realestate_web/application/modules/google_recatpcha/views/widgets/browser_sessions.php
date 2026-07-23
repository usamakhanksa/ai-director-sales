<div class="box box-solid session-block">
		<div class="box-header with-border">
		  <i class="fa fa-calendar"></i>
		  <h3 class="box-title">
			<?php echo mlx_get_lang('Sessions'); ?>
		  </h3>
		</div>
		<div class="box-body">
			<table class="table table-striped table-bordered" id="browserSession">
				<thead >
				<tr>
					<th >#</th>
					<th >Browser</th>
					<th >Session</th>
				</tr>
				
				<tbody>
				</tbody>
			</table>
			<div class="overlay">
			  <i class="fa fa-refresh fa-spin"></i>
			</div>
		</div>
</div>
<script>

	$(document).ready(function(){
		
		$.ajax({url: "<?php echo base_url().'admin/ajax_ga/show_ga_browser_per_session';?>", success: function(data){
		if(data != null){
			 var results = JSON.parse(data);
				var opt;			 
			 var sr=1;
				Object.keys(results).forEach(function(key) {
			  
				visitorsData [key] =  results[key] ;
				 opt +=`<tr><td>${sr}</td><td>${key}</td><td>${results[key]} (Sessions)</td></tr>`;
				 sr++;
				});
			
			$("#browserSession").find('tbody').append(opt);
			$('.session-block .overlay').hide();
		}
		
	  }});
		
	});	

</script>