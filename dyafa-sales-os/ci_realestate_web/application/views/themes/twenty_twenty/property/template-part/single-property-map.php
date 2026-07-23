<div class="embed_iframe_container">
	<div id="gMap" style="width:100%;height:550px;"></div>

	<script>
		function initMap() {
			var map;
			var bounds = new google.maps.LatLngBounds();

			<?php if($has_lat_long_available){ ?>
				var mapOptions = {
					mapTypeId: 'roadmap',
				};
			<?php
			}
			else
			{
			?>
				var myLatlng = new google.maps.LatLng(<?php echo get_option('google_map_center_latitude')?>, <?php echo get_option('google_map_center_longitude')?>);
				var mapOptions = {
					center: myLatlng,
					zoom: 5
				};
			<?php
			}
			?>
							
			// Display a map on the web page
			map = new google.maps.Map(document.getElementById("gMap"), mapOptions);
			map.setTilt(50);
				
			// Multiple markers location, latitude, and longitude
			<?php if($has_lat_long_available){ ?>
			var markers = [
				<?php foreach($search_properties->result() as $prop_row){ 
					if(!empty($prop_row->lat) && !empty($prop_row->long))
					{
				?>
					['<?php echo ucfirst($prop_row->title); ?>', '<?php echo $prop_row->lat; ?>', '<?php echo $prop_row->long; ?>'],
				<?php }} ?>
			];

			// Info window content
			var infoWindowContent = [
					<?php foreach($search_properties->result() as $prop_row){ 
						if(!empty($prop_row->lat) && !empty($prop_row->long))
						{
					$addr_string = array();

					if(!empty($prop_row->sub_area))
						$addr_string[] = trim($prop_row->sub_area);
					if(!empty($prop_row->address))
						$addr_string[] = str_replace(array("\n", "\r\n"), ",",trim($prop_row->address));
					if(!empty($prop_row->city))
						$addr_string[] = $prop_row->city;
					if(!empty($prop_row->state))
						$addr_string[] = $prop_row->state;
					if(!empty($prop_row->country))
						$addr_string[] = $prop_row->country;
					
					$img_string = '';
					if (!empty($prop_row->property_images)) {
						$p_images = $myHelpers->global_lib->get_property_gallery($prop_row->p_id, 'medium');
						if (!empty($p_images)) {
							foreach ($p_images as $k => $v) {
								if(file_exists( $v['medium']))
								{
									$post_image_url = base_url() . $v['medium'];
									$img_string = '<img src="' . $post_image_url . '" class="img-responsive img-thumbnail" style="margin-bottom:10px;">';
									break;
								}
							}
						}
					} 
					?>
					['<div class="info_content">' +
					'<div class="media-container"><?php echo $img_string; ?></div>'+
					'<div class="iw-title"><h5><?php echo ucfirst($prop_row->title); ?></h5></div>' +
					'<p><i class="fa fa-map-marker"></i> <?php if(!empty($addr_string)){ echo implode(', ',$addr_string); } ?>'+
					'<p class="text-justify"><?php  echo ucfirst($prop_row->short_description); ?>'+
					'<div class="iw-subTitle"><h6>Property Details</h6></div>' +
					'<p style="margin-bottom:5px;"><strong>Price</strong> : <?php echo ucfirst($prop_row->price); ?></p>'+
					'<p style="margin-bottom:5px;"><strong>Size</strong> : <?php echo str_replace('~',' ',$prop_row->size); ?></p>'+
					'<p><strong>Property For</strong> : <?php echo ucfirst($prop_row->property_for); ?></p>'+
					'<p class="text-center"><a class="btn btn-primary btn-sm" href="<?php echo base_url(array("property",$prop_row->slug."~".$prop_row->p_id)); ?>" target="_blank">View Full Details</a></p>' + 
					'</div>'],
					<?php }} ?>
				];
			<?php }else{ ?>
			var markers = [];
			var infoWindowContent = [];
			<?php } ?>
								
			
			// Add multiple markers to map
			var infoWindow = new google.maps.InfoWindow({maxWidth: 350,}), marker, i;
			
			// Place each marker on the map  
			if(markers.length > 0)
			{
				for( i = 0; i < markers.length; i++ ) {
					var position = new google.maps.LatLng(markers[i][1], markers[i][2]);
					bounds.extend(position);
					marker = new google.maps.Marker({
						position: position,
						map: map,
						title: markers[i][0]
					});
					
					// Add info window to marker    
					google.maps.event.addListener(marker, 'click', (function(marker, i) {
						return function() {
							infoWindow.setContent(infoWindowContent[i][0]);
							infoWindow.open(map, marker);
						}
					})(marker, i));

					// Center the map to fit all markers on the screen
					map.fitBounds(bounds);
				}
			}

			// Set zoom level
			var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function(event) {
				this.setZoom(8);
				google.maps.event.removeListener(boundsListener);
			});
			
		}
	</script>

	<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $google_map_js_api_key; ?>&callback=initMap"></script>
	<style>
		.gm-style img {
			max-width: 100%;
		}
	</style>
</div>