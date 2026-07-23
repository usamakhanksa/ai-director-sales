
<?php if(isset($banner_row) && isset($banner_row->b_image) && !empty($banner_row->b_image) && file_exists('uploads/banner/'.$banner_row->b_image)){ ?>
<section class="page-top-section set-bg" 
	data-setbg="<?php echo base_url(); ?>uploads/banner/<?php echo $banner_row->b_image; ?>" 
	style="background-image: url(<?php echo base_url(); ?>uploads/banner/<?php echo $banner_row->b_image; ?>);">
	<div class="container text-white">
		<h1><?php if(isset($page_row->page_title)) echo ucfirst($page_row->page_title); ?></h1>
	</div>
</section>
<?php } ?>

<div class="site-section">
  <div class="container">
	<div class="row">
	  <div class="col-md-12 page-content" >
		<?php if(isset($page_row->page_content)) echo $page_row->page_content; ?>
	  </div>
	</div>
  </div>
</div>
