
<?php $this->load->view("admin/sidebar-left"); ?>

<?php 

$site_language = get_option('site_language');
echo link_tag("application/views/$theme/assets/plugins/jstree/themes/default/style.min.css");
echo script_tag("application/views/$theme/assets/plugins/jstree/jstree.min.js");

if(isset($locations) && !empty($locations))
	$locations = json_decode($locations, true);
	

?>

<style>
	ul.tax_items{
		padding:0px;
	}
	ul.tax_items li { display: inline-block; padding-right:20px; }
	span.active{
		color: green;
		font-weight: bold;
		font-size: 12px;
	}
	span.inactive{
		color: red;
		font-weight: bold;
		font-size: 12px;
	}
	.box .overlay{
		background-color:#fff;
	}

	.fixed-tab {
		position: fixed;
		top: 60px;
		width: calc(100% - 40px);
		z-index:1000;
		background-color:#fff;	
	}
	#locations-setting .row{
		display: table;
	}
	#locations-setting > .row > [class*="col-"] {
		float: none;
		display: table-cell;
		vertical-align: middle;
		border-right: 1px solid #f4f4f4;
	}
	#locations-setting > .row > [class*="col-"]:last-child {
		border-right:0px none;	
	}
	#locations-setting h4 {
		margin-top: 0px;
	}
	#locations-setting .row .row [class*="col-"]:first-child .btn {
		margin-bottom: 10px;
	}
	#locations-setting .btn{
		color:#fff;
	}
	.no-action{
		pointer-events: none;
	}
</style>
      
<script>
	var country_code = '';
	var country_name = '';
	$(document).ready(function() {
		
		
		$('#locationLangModal').scroll(function(){
			var st = $(this).scrollTop() - 30;
			if ($('#locationLangModal').scrollTop() >= 100) {
				$('#locationLangModal .nav.nav-tabs').addClass('fixed-tab').css('top', st + 'px');
			}
			else {
				$('#locationLangModal .nav.nav-tabs').removeClass('fixed-tab').css('top', st + 'px');
			}
		});
		
		$('#locationLangModal').on('hide.bs.modal', function () {
			$('#locationLangModal .modal-body').html('');
			$('#locationLangModal .overlay').show();
		})
		
		$('.element_country').click(function() {
			var thiss = $(this);
			country_code = thiss.attr('data-country_code');
			country_name = thiss.attr('data-country_name');
			$('#locationLanguageModal .overlay').show();
		});
		
		$('#locationLanguageModal').on('shown.bs.modal', function () {
			$('#locationLanguageModal').find('.country_code').val(country_code);
			$('#locationLanguageModal').find('.country_name').html(country_name);
		    
			var callback = 'get_current_language_list';
			$.ajax({						
				url: base_url + 'admin_ajax',					
				type: 'POST',						
				success: function (res) 
				{		
					$('#locationLanguageModal').find('.language_list').html(res);
					$('#locationLanguageModal .overlay').hide();
				},						
				data: {	country_code : country_code, callback : callback},						
				cache: false					
			});
		});
		
		$('#locationLanguageModal').on('hide.bs.modal', function () {
			$('#locationLanguageModal').find('.language_list').html('');
			$('#locationLanguageModal').find('.country_code').val('');
			$('#locationLanguageModal').find('.country_name').html('');
			$('#locationLanguageModal .overlay').show();
		})
		
		$('.location_language_form').submit(function() {
			$('#locationLanguageModal .overlay').show();
			var thiss = $(this);

			var callback = 'update_location_language';
			$.ajax({						
				url: base_url+'admin_ajax',						
				type: 'POST',						
				success: function (res) 
				{		
					$('#locationLanguageModal').modal('hide');
					window.location.reload();
				},						
				data: thiss.serialize()+'&callback='+callback,						
				cache: false					
			});
			return false;
		});
		
		$('#locationLangModal').on('shown.bs.modal', function () {
			var callback = 'get_location_language_list';
			$.ajax({						
				url: base_url+'admin_ajax',						
				type: 'POST',						
				success: function (res) 
				{		
					$('#locationLangModal').find('.modal-body').html(res.output);
					$('#locationLangModal .overlay').hide();
				},						
				data: {callback:callback},						
				cache: false					
			});
		});
		
		
		$('.location_lang_form').submit(function() {
			$('#locationLangModal .overlay').show();
			var thiss = $(this);
			var callback = 'update_location_lang';
			$.ajax({						
				url: base_url+'admin_ajax',						
				type: 'POST',						
				success: function (res) 
				{		
					$('.content-header .page-title').after(res.output);
					$('.alert').delay(5000).fadeOut('slow');
					$('#locationLangModal').modal('hide')
				},						
				data: thiss.serialize()+'&callback='+callback,							
				cache: false					
			});
			return false;
		});
		
		$('.reset-locations-hierarchy-btn').on('click',function() {
			
			if(confirm('Do you really want to reset location hierarchy?'))
			{
				$('.full_sreeen_overlay').show();
				var thiss = $(this);
				var callback = 'reset_location_meta';
				$.ajax({						
					url: base_url+'admin_ajax',						
					type: 'POST',						
					success: function (res) 
					{		
						$('.content-header .page-title').after(res.output);
						$('.alert').delay(5000).fadeOut('slow');
						$('#locations-tree,.reset-locations-hierarchy-btn,.location_lang_modal_btn').hide();
						$('#locations-tree').html('');
						$('.no-location-hierarchy-block').show();
						$('.full_sreeen_overlay').hide();
					},						
					data: {callback:callback},						
					cache: false					
				});
			
			}
			return false;
		});
		
	});
</script>
      
      <div class="content-wrapper">
        <section class="content-header">
          <h1 class="page-title"><i class="fa fa-map-marker"></i> <?php echo mlx_get_lang('Manage Locations Hierarchy'); ?> </h1>
        </section>

        <section class="content">
			<?php 
			 
			$attributes = array('name' => 'add_form_post','class' => 'form');		 			
			echo form_open_multipart('',$attributes); ?>
			<input type="hidden" name="user_id" class="user_id" value="<?php echo $this->session->userdata('user_id'); ?>">
			
			<div class="row">
			<div class="col-md-8">   
			   
			  <div class="box box-<?php echo get_skin_class(); ?>">
                <div class="box-header with-border">
                  <h3 class="box-title"><?php echo mlx_get_lang('Manage Locations'); ?></h3>
				  <div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				  </div>
                </div>
                <div class="box-body">
				  
				<?php
				
				$location_taxonomy = array();
				$location_taxonomy ['country'] = array(
												"tax_type" => "country", "tax_title" => "Country",
												"tax_parent" => "root",	"has_child" => 1,	
												"enabled" => true, "multi_lang_enabled" => false,
												);
												
				$location_taxonomy ['state'] = array(
												"tax_type" => "state", "tax_title" => "State",
												"tax_parent" => "country",	"has_child" => 1,	
												"enabled" => false, "multi_lang_enabled" => true,
												);
												
				$location_taxonomy ['city'] = array(
												"tax_type" => "city", "tax_title" => "City",
												"tax_parent" => "state",	"has_child" => 2,	
												"enabled" => false, "multi_lang_enabled" => true,	
												);
				
				$location_taxonomy ['zipcode'] = array(
												"tax_type" => "zipcode", "tax_title" => "Zipcode",
												"tax_parent" => "city",	"has_child" => 0,	
												"enabled" => false, "multi_lang_enabled" => true,	
												);
				
				$location_taxonomy ['sub-area'] = array(
												"tax_type" => "sub_area", "tax_title" => "Sub-Area",
												"tax_parent" => "city",	"has_child" => 0,	
												"enabled" => false, "multi_lang_enabled" => true,	
												);
				
				if(empty($loc_tax_settings)){
				?>   
				<input type="hidden" name="loc_tax_settings" value='<?php echo json_encode($location_taxonomy); ?>' />
				<?php echo mlx_get_lang('Your Locations settings is not set. Save now'); ?>
				<button name="submit" type="submit" class="btn btn-<?php echo get_skin_class(); ?> pull-right" id="add_location"><?php echo mlx_get_lang('Save Now'); ?></button>
				
				<?php } else {
					$loc_tax_settings = json_decode($loc_tax_settings,true);
					
				?>
				<div id="locations-setting" class="demo">
				<?php
					echo "<div class='row'>";
					$has_multiple_sub_menu = false;
					$par_name = '';
					$sub_menu_string = '';
					$n = 0;
					foreach($loc_tax_settings as $loc_key => $loc_taxes)
					{
						$n++;
						$has_child = '';
						if($loc_taxes['has_child'] == 0)
							$has_child = "";
						else if($loc_key != 'country')
						{
							$has_child = "";
						}
						
						
						
						$parent = ""; //(city)";
						$status = '';
						if($loc_key != 'country')
						{
							
							if(!$loc_tax_settings['city']['enabled'] && ($loc_key == 'zipcode' || $loc_key == 'sub-area'))
							{
								if($loc_taxes['enabled'] && $loc_tax_settings['city']['enabled'])
									$status = "<span class='active btn btn-success btn-block disabled'> ".mlx_get_lang('Active')." </span>";
								else
									$status = "<span class='inactive btn btn-warning btn-block disabled'> ".mlx_get_lang('In-Active')." </span>";
							}
							else
							{
								if($loc_taxes['enabled'])
									$status = "<a data-toggle='tooltip' data-placement='bottom' title='".mlx_get_lang('Click for In-Active')."' href='". 
									base_url('admin/locations/manage/?action&tax='.$loc_taxes["tax_type"].'&status=inactive')."'class='active btn btn-success btn-block'> ".mlx_get_lang('Active')." </a>  
									";
								else
									$status = "<a data-toggle='tooltip' data-placement='bottom' title='".mlx_get_lang('Click for Active')."' href='". 
									base_url('admin/locations/manage/?action&tax='.$loc_taxes["tax_type"].'&status=active')."'class='inactive  btn btn-warning btn-block'> ".mlx_get_lang('In-Active')." </a>";
								
							}
						}
						else
							$status = "<a class='active btn btn-success btn-block no-action' > ".mlx_get_lang('Active')." </a> "; 
						
						if($loc_key == 'zipcode' || $loc_key == 'sub-area')
						{
							if($loc_key == 'zipcode')
								echo '<div class="col-md-3"><div class="row">';
							echo "<div class='col-md-12'> <h4 class='text-center'><strong>".mlx_get_lang($loc_taxes['tax_title'])."</strong></h4>".$status. $has_child ."  </div>";
							if($loc_key == 'sub-area')
								echo '</div></div>';
						}
						else
						{
							echo "<div class='col-md-3'> <h4 class='text-center'><strong>".mlx_get_lang($loc_taxes['tax_title'])."</strong></h4>".$status. $has_child ."  </div>";
						}
					}
					echo "</div>";
				?>
				
				</div  >
				
				<?php } ?>		
				
				
				
				</div>
                
              </div>
			 
			<?php if(!empty($loc_tax_settings)){ ?>
			<div class="box box-<?php echo get_skin_class(); ?>">
                <div class="box-header with-border">
					<h3 class="box-title"><?php echo mlx_get_lang('List of Locations Hierarchy'); ?></h3>
					<div class="box-tools pull-right">
						<span class="btn btn-box-tool reset-locations-hierarchy-btn" 
						<?php if(!isset($locations) || (isset($locations) && empty($locations))) {
							echo ' style="display:none;" ';
						} 
						?>
						data-toggle='tooltip' title="<?php echo mlx_get_lang('Empty Locations Hierarchy'); ?>"><i class="fa fa-trash fa-2x"></i></span>
						<span class="btn btn-box-tool location_lang_modal_btn" 
						<?php if(!isset($locations) || (isset($locations) && empty($locations))) {
							echo ' style="display:none;" ';
						} 
						?>
						data-toggle='modal' data-target='#locationLangModal'><i class="fa fa-language fa-2x" data-toggle="tooltip" title="<?php echo mlx_get_lang('Location Languages'); ?>"></i></span>
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				  </div>
                </div>
                <div class="box-body">
				
				<style>
				.locations-tree .panel {
					margin-bottom: 10px;
				}
				.locations-tree .panel-collapse{
					padding: 10px 10px 0px 10px;
				}
				.list-group-item{
					border:1px solid #ddd !important;
				}
				.list-group{
					margin-bottom:10px !important;
				}
				.rem-loc i, .element_country i {
					color: #fff;
				}
				.panel-heading .box-tools a.btn{
					margin-left:5px;
				}
				.panel-heading .box-tools a.arrow-r{
					margin-left:5px;
				}
				.panel-title {
					display: inline-block;
					max-width: 80%;
					white-space: nowrap;
					text-overflow: ellipsis;
					overflow: hidden;
				}
				.panel-heading {
					padding: 7px 10px 4px;
				}
				</style>
				
				<?php
				
				$flag_codes =  array( "en"=>"us", 
				"hi"=>"in", "cs"=>"cz", 
				"he"=>"is", "ja"=>"jp", "ko"=>"sk", "da"=>"dk", 
				);
				
				function get_gs_child_by_parent($locations , $args = array())
				{
					extract($args);
					
					
					
					if(count($locations) > 0)
					{
						$acc_tab_id = $country_code.'_'.$loc_key.'_'.$loc_type;
					?>
						<div class="accordion" id="<?php echo $acc_tab_id; ?>_acc_section" role="tablist" aria-multiselectable="true">

							<div class="panel panel-default">

								<div class="panel-heading" role="tab" id="<?php echo $acc_tab_id; ?>_acc_section_heading">
									<h5 class="panel-title">
										<?php
										echo '<strong>';
										if(isset($title)) echo $title;
										else if(isset($location['loc_title'])) echo mlx_get_lang($location['loc_title']); 
										echo '</strong>';
										?>
									</h5>
									<div class="box-tools pull-right">
										<a class="arrow-r pull-right" data-toggle="collapse" 
												data-parent="#<?php echo $acc_tab_id; ?>_acc_section" 
												href="#<?php echo $acc_tab_id; ?>_acc_collapse_section" 
												aria-expanded="false" 
												aria-controls="<?php echo $acc_tab_id; ?>_acc_collapse_section">
												<i class="fa fa-angle-down rotate-icon"></i>
										</a>
									</div>
								</div>

								<div id="<?php echo $acc_tab_id; ?>_acc_collapse_section" class="panel-collapse collapse" role="tabpanel" aria-labelledby="<?php echo $country_code.'_'.$loc_type; ?>_acc_section_heading">
						<ul class="list-group">
					<?php
						
						
						
						foreach($locations as $loc_key => $location)
						{
							if(empty($location)) continue;
							echo '<li class="list-group-item">';
							echo mlx_get_lang($location);
							echo $rem_url = " <a href='#' data-elem='$loc_key' data-id='$location'  
								data-elem_type='$loc_type' class='rem-loc btn btn-xs btn-danger pull-right'><i class='fa fa-trash'></i></a>";
							echo '</li>';
						}
					?>
						</ul>
								</div>
							</div>
						</div>
					<?php					
					}
				}
				
				
				function get_child_by_parent($locations , $args = array())
				{
					extract($args);
					if(count($locations) > 0)
					{
						/*
						echo "<ul ";
						if(isset($no_state_enable) && $no_state_enable == true)
						{
							echo ' style="margin-left:-40px;" ';
						}
						echo ">";
						if(isset($title))
							echo "<li>".mlx_get_lang($title). "</li> <ul>";
						*/
						
						
						$acc_id = $country_code;
						if(isset($loc_cus_type)) 
							$acc_id .= '_'.$loc_cus_type
						?>
						<div class="accordion" id="<?php echo $acc_id; ?>_acc_section" role="tablist" aria-multiselectable="true">
						<?php
						foreach($locations as $loc_key => $location)
						{
							if($is_state_enable && $loc_key == 'no_state')
							{
								continue;
							}
							else if(!$is_state_enable && $loc_key == 'no_state' && $is_city_enable)
							{
								
								if(isset($country_code))
									$args['country_code'] = $country_code;
								$args['no_state_enable'] = true;
								$args['is_state_enable'] = true;
								
								get_child_by_parent($location['cities'],$args);
								continue;
							}
							else if($loc_key == 'no_state' && isset($location['cities']) && $is_state_enable)
							{
								if(isset($country_code))
									$args['country_code'] = $country_code;
								$args['no_state_enable'] = true;
								
								get_child_by_parent($location['cities'],$args);
								continue;
							}
							else if(!$is_state_enable)
							{
								continue;
							}
							else
								$args['no_state_enable'] = false;
							$id=0;	
							$ext_link = '';
							ob_start();
								if($location['loc_type'] == 'state' && $is_state_enable && $is_city_enable){ ?>
								<?php 
								if(isset($location['state_type']) && $location['state_type'] == 'custom')
								{
									$id = $location['state_id'];
								}
								else
								{
								?>
								<a href="#" class="add-state-city btn btn-xs btn-info" 
								data-state="<?php echo $loc_key; ?>" 
								data-state_title="<?php echo $location['loc_title']; ?>"
								data-state_id="<?php echo $location['state_id']; ?>"
								data-country_code = "<?php if(isset($args['country_code'])) { echo $args['country_code']; }
								$id=$location['state_id'];
								?>" title="<?php echo mlx_get_lang('Click to Add City'); ?>"
								><i class="fa fa-plus"></i></a>
								<?php } ?>
								
								<a href="#" data-toggle='modal' data-target='#addCityModal' 
									class="btn btn-xs btn-warning"
									data-state="<?php echo $loc_key; ?>" 
									data-state_title="<?php echo $location['loc_title']; ?>"
									data-state_id="<?php echo $location['state_id']; ?>"
									data-country_code = "<?php if(isset($args['country_code'])) { echo $args['country_code']; } ?>"
									data-country_title = "<?php if(isset($args['country_title'])) { echo $args['country_title']; } ?>"
									title="<?php echo mlx_get_lang('Add Custom City'); ?>"
								><i class="fa fa-plus"></i></a>
								<?php } ?>	
								
								<?php if($location['loc_type'] == 'city' && ($is_zipcode_enable || $is_sub_area_enable)){ 
									$ci_text = '';
									if(isset($is_zipcode_enable) && $is_zipcode_enable == true && 
									   isset($is_sub_area_enable) && $is_sub_area_enable == true)
									     $ci_text = mlx_get_lang('Click to Add Zipcode/Sub-Area');
									else if(isset($is_zipcode_enable) && $is_zipcode_enable == true)
									     $ci_text = mlx_get_lang('Click to Add Zipcode');
									else if(isset($is_sub_area_enable) && $is_sub_area_enable == true)
									     $ci_text = mlx_get_lang('Click to Add Sub-Area');
									
										
								?>
									<a href="#" class="add-city-zip-sub-area btn btn-xs btn-warning" 
									data-city="<?php echo $loc_key; ?>" 
									data-city_title="<?php echo $location['loc_title']; ?>"
									data-city_id="<?php echo $location['city_id']; ?>"
									title="<?php echo $ci_text; ?>"
									><i class="fa fa-plus"></i></a>
								<?php 
								$id=$location['city_id'];
								} ?>	
								
							<?php 
							$loc_type = $location['loc_type'];
							
							$ext_link = ob_get_clean();;
						?>
							

								<div class="panel panel-default">

									<div class="panel-heading" role="tab" id="<?php echo $loc_key; ?>_acc_section_heading">
										<h5 class="panel-title">
											<?php
											if(isset($title)) echo '<strong>'.$title.'</strong>';
											else if(isset($location['loc_title'])) echo '<strong>'.mlx_get_lang($location['loc_title']).'</strong>'; 
											
											?>
										</h5>
										<div class="box-tools pull-right">	
											<?php if($loc_type == 'state' && $is_city_enable){ ?>
												<a class="arrow-r pull-right btn btn-xs btn-default" data-toggle="collapse" data-parent="#<?php echo $acc_id; ?>_acc_section" href="#<?php echo $loc_key; ?>_acc_collapse_section" aria-expanded="false" aria-controls="<?php echo $loc_key; ?>_acc_collapse_section"><i class="fa fa-angle-down rotate-icon"></i>
												</a>
											<?php }else if($loc_type == 'city' && ($is_zipcode_enable || $is_sub_area_enable)){ ?>
												<a class="arrow-r pull-right btn btn-xs btn-default" data-toggle="collapse" data-parent="#<?php echo $acc_id; ?>_acc_section" href="#<?php echo $loc_key; ?>_acc_collapse_section" aria-expanded="false" aria-controls="<?php echo $loc_key; ?>_acc_collapse_section"><i class="fa fa-angle-down rotate-icon"></i>
												</a>
											<?php } ?>
											<?php 
											echo "<a href='#' data-elem='$loc_key' data-id='$id'  data-elem_type='$loc_type' class='rem-loc pull-right btn btn-xs btn-danger'><i class='fa fa-trash'></i></a>";
											echo $ext_link; 
											?>
										</div>
									</div>

									<div id="<?php echo $loc_key; ?>_acc_collapse_section" class="panel-collapse collapse" role="tabpanel" aria-labelledby="<?php echo $loc_key; ?>_acc_section_heading">
						<?php
							
							
							if(is_array($location)){
								$id=0;
							?>
							
							<?php
							
							if(!isset($location['loc_type']) && isset($loc_type))
								$loc_type = $loc_type;
							
							if( isset( $location['cities']) && $is_city_enable)
							{
								if(isset($country_code))
									$args['country_code'] = $country_code;
								if(isset($country_title))
									$args['country_title'] = $country_title;
								$args['loc_cus_type'] = 'city';
								get_child_by_parent($location['cities'],$args);
							}
							
							if( isset( $location['zipcodes']) && $is_zipcode_enable){
								$args ['title'] = "Zipcodes";
								$args ['loc_type'] = "zipcodes";
								$args['loc_cus_type'] = 'zipcode';
								$args ['loc_key'] = $loc_key;
								
								get_gs_child_by_parent($location['zipcodes'] , $args);
							}
							
							if( isset( $location['sub_areas']) && $is_sub_area_enable){
								$args ['title'] = "Sub Areas"; 
								$args ['loc_type'] = "sub_areas";
								$args['loc_cus_type'] = 'subarea';
								$args ['loc_key'] = $loc_key;
								get_gs_child_by_parent($location['sub_areas'] , $args);
							}
						
						
							/*echo "</li>";*/
							
							}else{
								
								if(empty($location)) continue;
								
							?>
							<!--	
							<li id="<?php echo $location; ?>" data-jstree='{"opened":true <?php ?> }'>
							<strong> <?php echo mlx_get_lang($location); ?></strong> 
							- <a href='#' data-elem='<?php echo $loc_key; ?>' data-id='<?php echo $location; ?>' 
							data-elem_type='<?php echo $loc_type; ?>' class='rem-loc'> X </a>
							-->
								<?php
							}
							?>
									</div>
								</div>
							
						<?php
						}
						/*
							if(isset($title))
								echo "</ul>";
						echo "</ul>";
						*/
						?>
						</div>
						<?php
					}
				}
				?>
				
				<div id="locations-tree" class="demo" 
				<?php if(!isset($locations) || (isset($locations) && empty($locations))) {
					echo ' style="display:none;" ';
				} 
				?>>
				<?php 
				
				
				
				$is_state_enable = false;
				if(isset($loc_tax_settings) && $loc_tax_settings['state']['enabled'] == 1)
					$is_state_enable = true;
				
				$is_city_enable = false;
				if(isset($loc_tax_settings) && $loc_tax_settings['city']['enabled'] == 1)
					$is_city_enable = true;
				
				$is_zipcode_enable = false;
				if(isset($loc_tax_settings) && $loc_tax_settings['zipcode']['enabled'] == 1)
					$is_zipcode_enable = true;
				
				$is_sub_area_enable = false;
				if(isset($loc_tax_settings) && $loc_tax_settings['sub-area']['enabled'] == 1)
					$is_sub_area_enable = true;
				
				if(isset($locations['countries']) && count($locations['countries']) > 0 ){ ?>
				
				<div class="accordion locations-tree" id="country_acc_section" role="tablist" aria-multiselectable="true">
				<?php 
					
				$countries = $locations['countries'];
					
				foreach($countries as $key => $country){
					$loc_title = $country['loc_title'];
					
					$loc_type = $country['loc_type'];
					$id = $country['country_id'];
					$country_lang = array();
					if(!empty($site_language))
					{
						$site_language_array = json_decode($site_language,true);
						foreach($site_language_array as $slak=>$slav)
						{
							$langExp = explode('~',$slav['language']);
							$lang_name = $langExp[0];
							$lang_code = $langExp[1];
							$language = $slav['language'];
							
							$lc_val = get_option('language_country_'.$lang_code);
							if(!empty($lc_val))
							{
								$exp_lc_val = explode(',',$lc_val);
								if(in_array($key,$exp_lc_val))
									$country_lang[] = $lang_name;
							}
						}
					}
					
					$cont_url = "<small>";
					if(!empty($country_lang))
						$cont_url .= "(".implode(', ',$country_lang).")";
					$cont_url .= "</small> ";
					
					$box_tools = '';
					if(!$is_state_enable)
					{
						if($is_city_enable)
						{
							$box_tools .= "<a  href='#' data-country_name='$loc_title' data-country_code='$key' class='add-country-city btn btn-xs btn-primary' title='".mlx_get_lang('Click to Add City')."'><i class='fa fa-plus'></i></a>"; 
						
							$box_tools .= "<a  href='#' data-state='no_state' data-state_title='No State' data-state_id='0' data-country_title='$loc_title' data-country_code='$key' data-toggle='modal' data-target='#addCityModal' class='btn btn-xs btn-warning' title='".mlx_get_lang('Add Custom City')."'><i class='fa fa-plus'></i></a>";
						}
					}
					else
					{
						$box_tools .= "<a class=' btn btn-xs btn-warning' href='#' data-country_name='$loc_title' data-country_code='$key' data-toggle='modal' data-target='#addStateModal' title='".mlx_get_lang('Add Custom State')."'><i class='fa fa-plus'></i></a>";
					}
					
				?>	
				
				
				

					<div class="panel panel-default">

						<div class="panel-heading" role="tab" id="<?php echo $key; ?>_country_heading">
							<h5 class="panel-title">
								<?php 
								$flag_code = strtolower($key);
								if(array_key_exists($flag_code,$flag_codes))
									$flag_code = $flag_codes[$flag_code];
								?>
								<span class="flag-icon flag-icon-<?php echo $flag_code; ?> "></span>&nbsp;
								<strong><?php echo mlx_get_lang($loc_title); ?></strong> <?php /*echo $cont_url;*/ ?>
							</h5>	
								<div class="box-tools pull-right">
									<?php 
									if(isset($country['states']) && ($is_state_enable || $is_city_enable))
									{
									?>
									<a class="arrow-r pull-right btn btn-xs btn-default" data-toggle="collapse" data-parent="#country_acc_section" href="#<?php echo $key; ?>_collapse_block" aria-expanded="false" aria-controls="<?php echo $key; ?>_collapse_block"><i class="fa fa-angle-down rotate-icon"></i>
									</a>
									<?php } ?>
									
									<?php echo "<a href='#' data-elem='$key'  data-id='$id' data-elem_type='$loc_type' class='rem-loc pull-right btn btn-xs btn-danger'><i class='fa fa-trash'></i></a>"; ?>
									<?php /*echo "<a  href='#' class='element_country pull-right btn btn-xs btn-info' data-country_name='$loc_title' data-country_code='$key' data-toggle='modal' data-target='#locationLanguageModal'><i class='fa fa-cog'></i></a>";*/ ?>
									<?php echo $box_tools; ?>
								</div>
							
						</div>

						
				
				<?php 
				if(isset($country['states']))
				{
				?>
					<div id="<?php echo $key; ?>_collapse_block" class="panel-collapse collapse" role="tabpanel" aria-labelledby="<?php echo $key; ?>_country_heading">
				<?php	
					get_child_by_parent($country['states'],array(
																 'country_title' => $loc_title, 
																 'country_code' => $key, 
																 'is_state_enable' => $is_state_enable, 
																 'is_city_enable' => $is_city_enable, 
																 'is_zipcode_enable' => $is_zipcode_enable,
																 'is_sub_area_enable' => $is_sub_area_enable,
																 'loc_cus_type' => 'state'
																)  
										);
				?>
					</div>
				<?php
				}
				?>
					</div>
				
				<?php } ?>
				</div>		
					
				<?php } ?>
				</div>
				
				<div class="no-location-hierarchy-block" <?php if(isset($locations) && !empty($locations)) {
					echo 'style="display:none;"';
				}
				?>>
					<h4 class="text-center"><?php echo mlx_get_lang('Empty Locations Hierarchy.'); ?></h4>
				</div>
					
				 </div>
                
              </div>
			<?php } ?> 
			 
			
		</div>
		  
		  		<div class="col-md-4">
				
				
				<?php if(!empty($loc_tax_settings)){
					
					
					if($loc_tax_settings['country']['enabled'])
					{
						$this->load->view("locations/admin/template-part/add-countries");
					}
					
					if($loc_tax_settings['state']['enabled'])
						$this->load->view("locations/admin/template-part/add-countries-states");
					
					if($loc_tax_settings['city']['enabled'])
					{
						$this->load->view("locations/admin/template-part/add-countries-states-cities");
						
						if($loc_tax_settings['zipcode']['enabled'] || $loc_tax_settings['sub-area']['enabled']){		
							$this->load->view("locations/admin/template-part/add-cities-zip-sub-area");
							
							$this->load->view("locations/admin/template-part/search-cities-zip-sub-area");
						}	
					}
				?>	
						
				<?php } ?>
				
				</div>
		  
		  
		  </div>  
		  
<script>
	jQuery("document").ready(function($){
		
		$('a.rem-loc').on("click",function(e){
			
			e.preventDefault();
			var thiss = $(this);
			if(confirm("Do you really want to perform this action?"))
			{
				$('.full_sreeen_overlay').show();
				var id = thiss.attr("data-id");
				var elem = thiss.attr("data-elem");
				var elem_type = thiss.attr("data-elem_type");
				var callback = 'remove_element_for_locations';
				
				$.ajax({						
					
					url: base_url+'admin_ajax',
					type: 'POST',						
					success: function (res) 
					{		
						
						if(res == 'success')
						{
							 
							if(elem_type == 'zipcodes' || elem_type == 'sub_areas')
							{
								thiss.parents('.list-group-item').eq(0).remove();
							}
							else if(elem_type == 'city' || elem_type == 'state' )
							{
								/*thiss.parents('.accordion').eq(0).remove();*/
								thiss.parents('.accordion').find('#'+elem+'_acc_section_heading').parent().remove();
							}
							else if(elem_type == 'country')
							{
								/*thiss.parents('.accordion').eq(0).remove();*/
								thiss.parents('.accordion').find('#'+elem+'_country_heading').parent().remove();
							}
							
						}
						$('.full_sreeen_overlay').hide();	
					},						
					data: {	id : id,	elem : elem,	elem_type : elem_type, callback : callback},						
					cache: false					
				});
			}
		});
		
	});	
</script>
		  
		  
			  
			  </form>
        </section>
      </div>
	  
<div id="locationLanguageModal" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content box">
		<form method="POST" class="location_language_form">
		  <div class="modal-header">
			<h4 class="modal-title"><?php echo mlx_get_lang('Location Languages'); ?> <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button></h4>
		  </div>
		  <div class="modal-body">
				<input type="hidden" name="country_code" class="country_code">
				<table class="table table-bordered table-striped" style="margin:0px;">
					<tr>
						<th width="40%"><?php echo mlx_get_lang('Country'); ?></th>
						<td class="country_name"></td>
					</tr>
					<tr>
						<th><?php echo mlx_get_lang('Languages'); ?></th>
						<td class="language_list"></td>
					</tr>
				</table>
		  </div>
		  <div class="modal-footer">
			<button type="submit" class="btn btn-<?php echo get_skin_class(); ?>"><?php echo mlx_get_lang('Save'); ?></button>
			<button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?php echo mlx_get_lang('Close'); ?></button>
		  </div>
		</form>
		<div class="overlay">
		  <i class="fa fa-refresh fa-spin"></i>
		</div>
		
    </div>
  </div>
</div>

<div id="locationLangModal" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content box">
		<form method="POST" class="location_lang_form">
		  <div class="modal-header">
			<h4 class="modal-title"><?php echo mlx_get_lang('Location Languages'); ?> <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button></h4>
		  </div>
			<div class="modal-body table-responsive">
				
			</div>
		  <div class="modal-footer">
			<button type="submit" class="btn btn-<?php echo get_skin_class(); ?>"><?php echo mlx_get_lang('Save'); ?></button>
			<button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?php echo mlx_get_lang('Close'); ?></button>
		  </div>
		</form>
		
		<div class="overlay">
		  <i class="fa fa-refresh fa-spin"></i>
		</div>
		
    </div>
  </div>
</div>

<div id="addStateModal" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content box">
		<form method="POST" class="add_state_form form">
		  <div class="modal-header">
			<h4 class="modal-title"><?php echo mlx_get_lang('Add State'); ?> 
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</h4>
		  </div>
			<div class="modal-body">
				
				<div class="form-group">
					<label for="cs-country_title"><?php echo mlx_get_lang('Country'); ?></label>
					<input type="text" readonly class="form-control" name="" id="cs-country_title">
					<input type="hidden" name="country_id" id="cs-country_id">
				</div>
				
				<div class="form-group">
					<label for="cs-state_id"><?php echo mlx_get_lang('State ID'); ?><span class="required">*</span></label>
					<input type="text" readonly class="form-control" name="state_id" id="cs-state_id" required>
				</div>
				<div class="form-group">
					<label for="cs-state_code"><?php echo mlx_get_lang('State Code'); ?><span class="required">*</span></label>
					<input type="text" class="form-control" name="state_code" id="cs-state_code" required>
				</div>
				<div class="form-group">
					<label for="cs-state_title"><?php echo mlx_get_lang('State Title'); ?><span class="required">*</span></label>
					<input type="text" class="form-control" name="state_title" id="cs-state_title" required>
				</div>
			</div>
		  <div class="modal-footer">
			<button type="submit" class="btn btn-<?php echo get_skin_class(); ?>"><?php echo mlx_get_lang('Submit'); ?></button>
			<button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?php echo mlx_get_lang('Close'); ?></button>
		  </div>
		</form>
		
		<div class="overlay">
		  <i class="fa fa-refresh fa-spin"></i>
		</div>
		
    </div>
  </div>
</div>	

<div id="addCityModal" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content box">
		<form method="POST" class="add_city_form form">
		  <div class="modal-header">
			<h4 class="modal-title"><?php echo mlx_get_lang('Add City'); ?> <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button></h4>
		  </div>
			<div class="modal-body">
				<div class="form-group">
					<label for="cc-country_title"><?php echo mlx_get_lang('Country'); ?></label>
					<input type="text" readonly class="form-control" name="" id="cc-country_title">
					<input type="hidden" name="country_id" id="cc-country_id">
				</div>
				
				<div class="form-group">
					<label for="cc-state_title"><?php echo mlx_get_lang('State'); ?></label>
					<input type="text" readonly class="form-control" name="" id="cc-state_title">
					<input type="hidden" name="state_id" id="cc-state_id">
				</div>
				
				
				<div class="form-group">
					<label for="cc-city_id"><?php echo mlx_get_lang('City ID'); ?><span class="required">*</span></label>
					<input type="text" class="form-control" readonly name="city_id" id="cc-city_id" required>
				</div>
				<div class="form-group">
					<label for="cc-city_code"><?php echo mlx_get_lang('City Code'); ?><span class="required">*</span></label>
					<input type="text" class="form-control" name="city_code" id="cc-city_code" required>
				</div>
				<div class="form-group">
					<label for="cc-city_title"><?php echo mlx_get_lang('City Title'); ?><span class="required">*</span></label>
					<input type="text" class="form-control" name="city_title" id="cc-city_title" required>
				</div>
			</div>
		  <div class="modal-footer">
			<button type="submit" class="btn btn-<?php echo get_skin_class(); ?>"><?php echo mlx_get_lang('Submit'); ?></button>
			<button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?php echo mlx_get_lang('Close'); ?></button>
		  </div>
		</form>
		
		<div class="overlay">
		  <i class="fa fa-refresh fa-spin"></i>
		</div>
		
    </div>
  </div>
</div>	