<div class="box box-solid browser-hit-block">
		<div class="box-header with-border">
		  <i class="fa fa-safari"></i>
		  <h3 class="box-title">
			<?php echo mlx_get_lang('Top Browsers'); ?>
		  </h3>
		</div>
		<div class="box-body">
			<table id="browserHits" class="table table-striped table-bordered" role="grid" aria-describedby="example2_info">
				<thead>
				  <tr role="row">
				  <th class="sorting"  aria-controls="browserHits" rowspan="1" colspan="1" >#</th>
				  <th class="sorting_asc"  aria-controls="browserHits" rowspan="1" colspan="1" >Browser</th>
				  <th class="sorting"  aria-controls="browserHits" rowspan="1" colspan="1" >Device</th>
				  <th class="sorting"  aria-controls="browserHits" rowspan="1" colspan="1" >OS</th>
				  <th class="sorting"  aria-controls="browserHits" rowspan="1" colspan="1" >Hits</th>
				  
				  </tr>
				</thead>
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
		
		$.ajax({url: "<?php echo base_url().'admin/ajax_ga/show_ga_hits';?>", success: function(data){
		if(data != null){
			 var results = JSON.parse(data);
				var opt;			 
			 var sr=1;
			 //console.log(results);
			for(var i = 0; i < results.length; i++) {
				var obj = results[i];
				opt +='<tr><td>'+ sr + '<td>'+ obj.browser 
					+ '</td><td>'+ obj.deviceCategory 
					+ '</td><td>'+ obj.operatingSystem 
					+'</td><td>'+ obj.hits +'</td></tr>';
				 sr++;
				
			}	
			$("#browserHits").find('tbody').append(opt);
			$('.browser-hit-block .overlay').hide();
		}
		
	  }});	
	});	

</script>