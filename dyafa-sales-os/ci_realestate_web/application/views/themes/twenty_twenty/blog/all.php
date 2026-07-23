<?php
// echo "<pre>";
// print_r($myHelpers->router->routes); 
?>
<?php if (isset($banner_row) && isset($banner_row->b_image) && !empty($banner_row->b_image) && file_exists('uploads/banner/' . $banner_row->b_image)) { ?>
	<section class="page-top-section set-bg" data-setbg="<?php echo base_url(); ?>uploads/banner/<?php echo $banner_row->b_image; ?>" style="background-image: url(<?php echo base_url(); ?>uploads/banner/<?php echo $banner_row->b_image; ?>);">
		<div class="container text-white">
			<h1><?php if (isset($banner_title)) echo $banner_title;
				else echo mlx_get_lang('Blogs'); ?></h1>
		</div>
	</section>
<?php } ?>
<style>
	.blog-inner img {
		max-height: 210px;
		min-height: 210px;
		width: 100%;
		max-width: 100%;
	}

	.blog-inner {
		border: 1px solid #f2f2f2;
	}
</style>
<div class="site-section">
	<div class="container">
		<div class="row justify-content-center">
			<?php if (isset($blog_list) && $blog_list->num_rows() > 0) {
				foreach ($blog_list->result() as $row) {
					$blog_url = $myHelpers->bloglib->get_url($row);
					$cat_url = $myHelpers->bloglib->get_cat_url($row);
			?>
					<div class="col-md-6 col-lg-4 mb-4 ">
						<div class="blog-inner h-100 bg-light">
							<a href="<?php echo $blog_url; ?>" class="d-block ">
								<?php
								if (!empty($row->image)) {
									if (file_exists('uploads/blog/' . $row->image)) {
										$post_image_url = base_url() . 'uploads/blog/' . $row->image;
										echo '<img src="' . $post_image_url . '" alt="' . ucfirst($row->title) . '" class="img-fluid">';
									} else {
										$post_image_url =
											$myHelpers->blog_image;
										echo '<img src="' . $post_image_url . '" alt="' . ucfirst($row->title) . '" class="img-fluid">';
									}
								} else {
									$post_image_url =
										$myHelpers->blog_image;
									echo '<img src="' . $post_image_url . '" alt="' . ucfirst($row->title) . '"  class="img-fluid" >';
								}
								?>
							</a>
							<div class="p-2 pt-2">
								<span class="d-block text-secondary small text-uppercase blog-info"><i class="fa fa-calendar"></i> <?php echo date('M d, Y', $row->publish_on); ?>
									<span class="pull-right text-capitalize"><i class="fa fa-tag"></i> <a href="<?php echo $cat_url; ?>"><?php echo ucfirst($row->cat_title); ?></a> </span>
								</span>
								<h2 class="h5 text-black mb-1 mt-2 "><a href="<?php echo $blog_url; ?>" class="blog-title" title="<?php echo ucfirst($row->cat_title); ?>"><?php echo ucfirst($row->title); ?></a></h2>
								<p class="mb-0 blog-desc"><?php echo ucfirst($row->short_description); ?></p>
							</div>
						</div>
					</div>

			<?php
				}
			} else {
				echo '<h3 class="text-center" style="display:block; width:100%;">No Blogs Available</h3>';
			} ?>
		</div>
	</div>
</div>