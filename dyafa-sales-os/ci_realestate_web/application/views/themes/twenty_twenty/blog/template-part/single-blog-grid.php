<?php
$blog_url = $myHelpers->bloglib->get_url($blog_row);
$cat_url = $myHelpers->bloglib->get_cat_url($blog_row);
?>

<div class="blog-inner h-100 bg-light">
	<a href="<?php echo $blog_url; ?>" class="d-block ">
		<?php
		if (!empty($blog_row->image)) {
			if (file_exists('uploads/blog/' . $blog_row->image)) {
				$post_image_url = base_url() . 'uploads/blog/' . $blog_row->image;
				echo '<img src="' . $post_image_url . '" alt="' . ucfirst($blog_row->title) . '" class="img-fluid">';
			} else {
				$post_image_url = $myHelpers->blog_image;
				echo '<img src="' . $post_image_url . '" alt="' . ucfirst($blog_row->title) . '" class="img-fluid">';
			}
		} else {
			$post_image_url =
				$myHelpers->blog_image;
			echo '<img src="' . $post_image_url . '" alt="' . ucfirst($blog_row->title) . '"  class="img-fluid" >';
		}
		?>
	</a>
	<div class="p-2 pt-2">
		<span class="d-block text-secondary small text-uppercase blog-info"><i class="fa fa-calendar"></i> <?php echo date('M d, Y', $blog_row->publish_on); ?>
			<?php if(!empty($blog_row->cat_title) && !empty($cat_url)) { ?>
			<span class="pull-right text-capitalize"><i class="fa fa-tag"></i> <a href="<?php echo $cat_url; ?>"><?php echo ucfirst($blog_row->cat_title); ?></a> </span>
			<?php } ?>
		</span>
		<h2 class="h5 text-black mb-1 mt-2 "><a href="<?php echo $blog_url; ?>" class="blog-title" title="<?php echo ucfirst($blog_row->cat_title); ?>"><?php echo ucfirst($blog_row->title); ?></a></h2>
		<p class="mb-0 blog-desc"><?php echo ucfirst($blog_row->short_description); ?></p>
	</div>
</div>