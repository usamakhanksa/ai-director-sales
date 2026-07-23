<?php 
global $settings;

$limit = 6;
if(isset($settings['show_as']) && $settings['show_as'] == 'grid'){ 
	$limit = $settings['no_of_item_in_grid_list'];
}
else if(isset($settings['show_as']) && $settings['show_as'] == 'grid'){ 
	$limit = $settings['no_of_item_in_carousel'];
}



$def_lang_code = $this->default_language;
$sql = "select * from properties as prop 
		inner join property_lang_details as pld on pld.p_id = prop.p_id and pld.language = '$def_lang_code'
		inner join users as u on u.user_id = prop.created_by and u.user_status = 'Y' ";

$sql = apply_filters("cms_recent_properties_extend_sql" , $sql);


$where = " where prop.status = 'publish' and prop.deleted = 'N'  ";
$where = apply_filters("cms_recent_properties_extend_where" , $where);


$order_by = " order by prop.p_id DESC limit $limit ";
$order_by = apply_filters("cms_recent_properties_extend_order_by" , $order_by);


$sql = $sql . $where . $order_by ;

$recent_properties = $this->Common_model->commonQuery($sql );

if(isset($recent_properties) && $recent_properties->num_rows() > 0){ 

?>
<div class="site-section site-section-sm">
  <div class="container">
	
	<div class="row justify-content-center mb-5">
	  <div class="col-md-10 text-center">
		<div class="site-section-title  sss ddd fff">
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
		if(isset($settings['show_as']) && $settings['show_as'] == 'grid'){ 
	?>
		<div class="row justify-content-center mb-5">
		<?php 
			
		foreach($recent_properties->result() as $prop_row){ 
			
			?>
		<div class="col-md-6 col-lg-4 mb-4">
					<?php include(__DIR__ . '../../property/template-part/single-property-grid.php'); ?>
			  </div>
		<?php } ?>
		</div>
	<?php }else{
		
		?>
		<div class="row mb-5">
		<?php if($recent_properties->num_rows() > 3 ){?>
			<div class="col-md-12">
				<div class="grid-carousel owl-carousel owl-theme mb50" 
									 data-dots="<?php echo $settings['show_nav_dots']; ?>"
									 data-nav="<?php echo $settings['show_nav']; ?>"
									 data-autoplay="<?php echo $settings['auto_start']; ?>"
									 data-interval="<?php echo $settings['carousel_interval']; ?>"> 
					
						<?php 
						 
						foreach($recent_properties->result() as $prop_row){
							
						?>
						<div class="item">
							<?php include(__DIR__ . '../../property/template-part/single-property-grid.php'); ?>
						</div>
					<?php  } ?>
						
				</div>
			</div>
			<?php } else { ?> 
			
			<?php 
				foreach($recent_properties->result() as $prop_row){
			?>
			<div class="col-md-6 col-lg-4 mb-4">
				<div class="item">
					<?php include(__DIR__ . '../../property/template-part/single-property-grid.php'); ?>
				</div>
			</div>	
			<?php  } ?>
			
			
			<?php } ?>	
			
		</div>
	<?php } ?>
	
	<?php if(isset($settings['show_view_more']) && $settings['show_view_more'] == 'yes') { ?>
		<div class="row">
		  <div class="col-md-12 text-center">
			<a href="<?php if(isset($this->enable_multi_lang) && $this->enable_multi_lang == true)
			{
				$def_lang_code = $this->default_lang_code;
				echo site_url(array('property',$def_lang_code)); 
			}
			else
			{
				echo site_url('property'); 
			} ?>" class="btn custom-btn py-2 px-4 rounded-0 text-white"><?php echo mlx_get_lang('View More'); ?></a>
		  </div>  
		</div>
	<?php } ?>
	
  </div>
</div>
<?php } ?>