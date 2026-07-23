<table id="example2" class="table table-bordered table-hover box" role="grid" aria-describedby="example2_info">
	<thead>
	  <tr role="row">
	  <th class="sorting_asc"  aria-controls="example2" rowspan="1" colspan="1" >Title</th>
	  <th class="sorting"  aria-controls="example2" rowspan="1" colspan="1" >Path</th>
	  <th class="sorting"  aria-controls="example2" rowspan="1" colspan="1" >Page View</th>
	  
	  </tr>
	</thead>
	<tbody>
	
	</tbody>
</table>

<script>

	$(document).ready(function(){
		
		$.ajax({url: "<?php echo base_url().'admin/ajax_ga/show_ga_top_pages';?>", success: function(data){
		if(data != null){
			 var results = JSON.parse(data);
				var opt;			 
			 var sr=1;
			 console.log(results);
				
			
			//$("#browserSession").find('tbody').append(opt);
		}
		
	  }});	
	});	

</script>