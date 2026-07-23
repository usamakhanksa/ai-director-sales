<?php 

$is_recaptcha_enable = false;
$isPlugAct = $myHelpers->isPluginActive('blog');
if($isPlugAct == true)
{
	$is_recaptcha_enable = true;
}

if($is_recaptcha_enable)
{
global $settings;

$limit = 6;
if(isset($settings['show_as']) && $settings['show_as'] == 'grid'){ 
	$limit = $settings['no_of_item_in_grid_list'];
}
else if(isset($settings['show_as']) && $settings['show_as'] == 'grid'){ 
	$limit = $settings['no_of_item_in_carousel'];
}

$def_lang_code = $this->default_language;
$today_timestamp = mktime(0,0,0,date('m',time()),date('d',time()),date('Y',time()));

$sql = "select b.image,b.b_id,b.slug,b.publish_on,
		bc.title as cat_title,bc.slug as cat_slug,
		bld.title as title, bld.short_description,
		bld.seo_meta_keywords, bld.seo_meta_description from blogs as b
	inner join blog_lang_details as bld on bld.blog_id = b.b_id and bld.language =  '$def_lang_code'
	left join blog_categories as bc on bc.c_id = b.cat_id and bc.status = 'Y'
	and bld.title != '' and bld.description != ''
	where b.status = 'publish' and b.publish_on <= $today_timestamp order by b.publish_on DESC limit $limit";
$recent_blogs = $this->Common_model->commonQuery($sql );

if(isset($recent_blogs) && $recent_blogs->num_rows() > 0){ 

?>
<div class="site-section site-section-sm ">
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
		if(isset($settings['show_as']) && $settings['show_as'] == 'grid'){ 
	?>
		<div class="row justify-content-center mb-5">
		<?php 
			
		foreach($recent_blogs->result() as $blog_row){ 
			
			?>
		<div class="col-md-6 col-lg-4 mb-4">
					<?php include(__DIR__ . '../../blog/template-part/single-blog-grid.php'); ?>
			  </div>
		<?php } ?>
		</div>
	<?php }else{
		
		?>
		<div class="row mb-5">
		<?php if($recent_blogs->num_rows() > 3 ){?>
			<div class="col-md-12">
				<div class="grid-carousel owl-carousel owl-theme mb50" 
									 data-dots="<?php echo $settings['show_nav_dots']; ?>"
									 data-nav="<?php echo $settings['show_nav']; ?>"
									 data-autoplay="<?php echo $settings['auto_start']; ?>"
									 data-interval="<?php echo $settings['carousel_interval']; ?>"> 
					
						<?php 
						 
						foreach($recent_blogs->result() as $blog_row){
							
						?>
						<div class="item">
							<?php include(__DIR__ . '../../blog/template-part/single-blog-grid.php'); ?>
						</div>
					<?php  } ?>
						
				</div>
			</div>
			<?php } else { ?> 
			
			<?php 
				foreach($recent_blogs->result() as $blog_row){
			?>
			<div class="col-md-6 col-lg-4 mb-4">
				<div class="item">
					<?php include(__DIR__ . '../../blog/template-part/single-blog-grid.php'); ?>
				</div>
			</div>	
			<?php  } ?>
			
			
			<?php } ?>	
			
		</div>
	<?php } ?>
	
	<?php if(isset($settings['show_view_more']) && $settings['show_view_more'] == 'yes') { ?>
		<div class="row">
		  <div class="col-md-12 text-center">
			<a href="
			<?php 
			if(isset($this->enable_multi_lang) && $this->enable_multi_lang == true)
			{
				$def_lang_code = $this->default_language;
				echo site_url(array('blogs',$def_lang_code)); 
			}
			else
			{
				echo site_url('blogs'); 
			}
			 ?>" 
			class="btn custom-btn py-2 px-4 rounded-0 text-white"><?php echo mlx_get_lang('View More'); ?></a>
		  </div>  
		</div>
	<?php } ?>
	
  </div>
</div>
<?php }} ?>