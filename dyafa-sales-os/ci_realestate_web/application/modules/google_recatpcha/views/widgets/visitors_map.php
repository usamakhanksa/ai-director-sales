<div class="col-md-4">
	<div class="box box-solid bg-light-blue-gradient visitors-map-block">
		<div class="box-header">
		  
		  <i class="fa fa-map-marker"></i>
		  <h3 class="box-title">
			<?php echo mlx_get_lang('Visitors'); ?>
		  </h3>
		</div>
		<div class="box-body">
		  <div id="world-map" style="height: 250px; width: 100%;"></div>
		</div>
		<div class="overlay">
		  <i class="fa fa-refresh fa-spin"></i>
		</div>
	</div>
</div>

<script>
  var visitorsData={};
  $(document).ready(function(){
	 
	  $.ajax({url: "<?php echo base_url().'admin/ajax_ga/show_ga_map';?>", success: function(data){
		if(data != null){
			 var results = JSON.parse(data);	
			Object.keys(results).forEach(function(key) {
				visitorsData [key] =  results[key] ;
			});
			
			initVectorMap();
		}
		
	  }});
	  
  });
  
  function initVectorMap(){
  //World map by jvectormap
  $('#world-map').vectorMap({
	  
    map: 'world_mill_en',
    backgroundColor: "transparent",
    regionStyle: {
      initial: {
        fill: '#ccc',
        "fill-opacity": 2,
        stroke: 'none',
        "stroke-width": 0,
        "stroke-opacity": 1
      }
    },
    series: {
      regions: [{
          values: visitorsData,
          //scale: ["#90c1dc", "#ebf4f9"],
		  scale: ["#ddb8eb", "#701293"],
          normalizeFunction: 'polynomial'
        }]
    },
    onRegionLabelShow: function (e, el, code) {
      if (typeof visitorsData[code] != "undefined")
        el.html(el.html() + ': ' + visitorsData[code] + ' new visitors');
    }
  });
	
	$('.visitors-map-block .overlay').hide();
	
 }
 
 
  
  
</script>			