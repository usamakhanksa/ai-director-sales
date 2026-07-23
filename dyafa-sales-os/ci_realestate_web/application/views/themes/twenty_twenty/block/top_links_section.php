<?php 
global $settings;
$CI = &get_instance();
?>
<div class="site-section site-section-sm" id="top-links-section">
  <div class="container">
	
	<div class="row justify-content-center mb-5">
	  <div class="col-md-10 text-center">
		<div class="site-section-title">
		 	<?php 
			if(isset($settings['heading']) && $settings['heading'] != ''){?>
			<h2> <?php echo mlx_get_lang($settings['heading']); ?></h2>
			<?php } ?>
			<?php if(isset($settings['sub_heading']) && $settings['sub_heading'] != ''){?>
			<p class="subheading"><?php echo mlx_get_lang($settings['sub_heading']); ?></p>
			<?php } ?>

		</div>
	  </div>
	</div>

	
    <?php 

		/*if(function_exists(get_property_type_lists()))*/

	$property_type_list = get_property_type_lists();

	if(isset($property_type_list) && $property_type_list->num_rows() > 0){ ?>
	<div class="row  mb-5">
		<div class="col-md-12">
			<div class="owl-carousel owl-theme" id="top-link-carousel">
  				
	<?php	foreach($property_type_list->result() as $prop_row){ 
		
				 $prop_type_slug = $prop_row->slug;
		$cities_list = location_get_cities_list();
		$prop_type_title = mlx_get_lang(ucfirst($prop_row->title));
		?>
					
					<div class="item">
						<ul class="top-links">
							<?php 	
								foreach($cities_list as $city){	
								/*$type = $CI->menu_lib->get_url('type='.strtolower($prop_row->title));*/
								
								
								
								$type_url = site_url(array('search',':lang',
															'property-for-sale' , 
															'property-type-'.$prop_type_slug ,
															'city-' . $city['city']
															));
								
								$type_url = $CI->menu_lib->remove_lang_from_url($type_url);
								
							?>
							<li>	
								<a href="<?php echo  $type_url;?>" target="_blank"><?php echo $prop_type_title.' in '.$city['title']; ?></a>			
							</li>
							<?php		}	?>
						</ul>
					
														</div>
					
														<div class="item">
						<ul class="top-links">
						<?php 	/*foreach($city_list as $ck=>$cv)	{	*/
							foreach($cities_list as $city){	
							
								$type_url = site_url(array('search',':lang',
															'property-for-rent' , 
															'property-type-'.$prop_type_slug ,
															'city-' . $city['city']
															));
								
								$type_url = $CI->menu_lib->remove_lang_from_url($type_url);
						?>
								<li>		
									<a href="<?php echo  $type_url;?>" target="_blank"><?php echo $prop_type_title.' for Rent in '.$city['title']; ?></a>		</li>
						<?php		}	?>
						</ul>
														</div>
				
	<?php } ?>
														
														</div>
	</div>
		</div>
	<?php } ?>
	
  </div>
  <style>
	#top-links-section ul.top-links {
		/*
		padding-left: 0;
		list-style-type: none;
		*/
		list-style-type: disclosure-closed;
		margin-bottom:0px;
	}
	#top-links-section ul.top-links li a {
		color: #000;
		font-weight: normal;
   		font-size: 14px;
	}
	#top-links-section .owl-next, #top-links-section .owl-prev {
		position: absolute;
		top: 50%;
		left: -21px;
		width: 42px;
		height: 42px;
		padding-top: 10px;
		border-radius: 50%;
		background-color: #fff;
		box-shadow: 0 5px 10px 2px rgb(0 0 0 / 10%);
		z-index: 9;
		text-align: center;
		color: #1c1c1c;
		font-size: 16px;
		cursor: pointer;
		-webkit-transform: translateY(-50%);
		-ms-transform: translateY(-50%);
		transform: translateY(-50%);
	}
	#top-links-section .owl-next{
		left: auto;
    	right: -21px;
	}
  </style>
  <script>
	$(document).ready(function(){
		$('#top-link-carousel').owlCarousel({
			items:4,
			loop:false,
			margin:10,
			nav:true,
			navText: ["<i class='fa fa-chevron-left'></i>","<i class='fa fa-chevron-right'></i>"],
			autoWidth:true,
			dots:false,
			responsive:{
				0:{
					items:1
				},
				600:{
					items:3
				},
				1000:{
					items:5
				}
			}
		})
	});
  </script>
</div>
	
	
	