<?php if (isset($banner_row) && isset($banner_row->b_image) && !empty($banner_row->b_image) && file_exists('uploads/banner/' . $banner_row->b_image)) { ?>
	<section class="page-top-section set-bg" data-setbg="<?php echo base_url(); ?>uploads/banner/<?php echo $banner_row->b_image; ?>" style="background-image: url(<?php echo base_url(); ?>uploads/banner/<?php echo $banner_row->b_image; ?>);">
		<div class="container text-white">
			<h1><?php echo ucwords($blog_row->title); ?></h1>
		</div>
	</section>
<?php } ?>

<div class="site-section">
	<div class="container">
		<div class="row">

			<div class="col-md-8 page-content">
				<?php
				if (!empty($blog_row->image)) {
					if (file_exists('uploads/blog/' . $blog_row->image)) {
						$post_image_url = base_url() . 'uploads/blog/' . $blog_row->image;
						echo '<img src="' . $post_image_url . '" class="img-fluid mb-3" alt="' . ucwords($blog_row->title) . '">';
					} else {
						$post_image_url = $myHelpers->blog_image;
						echo '<img src="' . $post_image_url . '" class="img-fluid mb-3" alt="' . ucwords($blog_row->title) . '">';
					}
				} else {
					$post_image_url = $myHelpers->blog_image;
					echo '<img src="' . $post_image_url . '" class="img-fluid mb-3" alt="' . ucwords($blog_row->title) . '">';
				}
				$cat_url = $myHelpers->bloglib->get_cat_url($blog_row);
				?>
				<h2 class="mb-2 font-weight-light text-black h3"><?php echo ucwords($blog_row->title); ?></h2>
				<span class="d-block mb-3 text-white-opacity-05">
					<i class="fa fa-calendar"></i> <?php echo date('M d, Y', $blog_row->publish_on); ?>
					<span class="ml-3"><i class="fa fa-tag"></i> <a href="<?php echo $cat_url; ?>"><?php echo ucfirst($blog_row->cat_title); ?></a> </span>
				</span>

				<div class="blog_description">
					<?php echo ucfirst($blog_row->description); ?>
				</div>

				<script>
					$(document).ready(function() {
						var w = 600;
						var h = 400;
						var left = (screen.width / 2) - (w / 2);
						var top = (screen.height / 2) - (h / 2);

						$('.social-share-btn').each(function() {
							$(this).attr('onclick', $(this).attr('onclick').replace('LEFT_POS', left));
							$(this).attr('onclick', $(this).attr('onclick').replace('TOP_POST', top));
						});
					});
				</script>
				<ul class="shares">
					<li class="shareslabel">
						<h3>Share This Blog</h3>
					</li>
					<li>
						<a class="w-inline-block social-share-btn share-facebook" title="Share on Facebook" target="_blank" onclick="window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(document.URL) + '&t=' + encodeURIComponent(document.URL), 'targetWindow', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=600,height=400,left=LEFT_POS,top=TOP_POST'); return false;">
							<i class="fa fa-facebook"></i>
						</a>
					</li>
					<li>
						<a class="w-inline-block social-share-btn share-twitter" target="_blank" title="Tweet" onclick="window.open('https://twitter.com/intent/tweet?text=' + encodeURIComponent(document.title) + ' :%20 ' + encodeURIComponent(document.URL), 'targetWindow', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=600,height=400,left=LEFT_POS,top=TOP_POST'); return false;">
							<i class="fa fa-twitter"></i>
						</a>
					</li>
					<li>
						<a class="w-inline-block social-share-btn share-googleplus" target="_blank" title="Share on Google+" onclick="window.open('https://plus.google.com/share?url=' + encodeURIComponent(document.URL), 'targetWindow', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=600,height=400,left=LEFT_POS,top=TOP_POST'); return false;">
							<i class="fa fa-google-plus"></i>
						</a>
					</li>
					<li>
						<a class="w-inline-block social-share-btn share-pinterest" target="_blank" title="Pin it" onclick="window.open('http://pinterest.com/pin/create/button/?url=' + encodeURIComponent(document.URL) + '&description=' + encodeURIComponent(document.title), 'targetWindow', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=600,height=400,left=LEFT_POS,top=TOP_POST'); return false;">
							<i class="fa fa-pinterest"></i>
						</a>
					</li>
					<li>
						<a class="w-inline-block social-share-btn share-tumblr" target="_blank" title="Post to Tumblr" onclick="window.open('http://www.tumblr.com/share?v=3&u=' + encodeURIComponent(document.URL) + '&t=' + encodeURIComponent(document.title), 'targetWindow', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=600,height=400,left=LEFT_POS,top=TOP_POST'); return false;">
							<i class="fa fa-tumblr"></i>
						</a>
					</li>
					<li>
						<a class="w-inline-block social-share-btn share-email" target="_blank" title="Email" onclick="window.open('mailto:?subject=' + encodeURIComponent(document.title) + '&body=' + encodeURIComponent(document.URL), 'targetWindow', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=600,height=400,left=LEFT_POS,top=TOP_POST'); return false;">
							<i class="fa fa-envelope"></i>
						</a>
					</li>

					<li>
						<a class="w-inline-block social-share-btn share-linkedin" target="_blank" title="Share on LinkedIn" onclick="window.open('http://www.linkedin.com/shareArticle?mini=true&url=' + encodeURIComponent(document.URL) + '&title=' + encodeURIComponent(document.title), 'targetWindow', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=600,height=400,left=LEFT_POS,top=TOP_POST'); return false;">
							<i class="fa fa-linkedin"></i>
						</a>
					</li>
					<li>
						<a class="w-inline-block social-share-btn share-reddit" target="_blank" title="Submit to Reddit" onclick="window.open('http://www.reddit.com/submit?url=' + encodeURIComponent(document.URL) + '&title=' + encodeURIComponent(document.title), 'targetWindow', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=600,height=400,left=LEFT_POS,top=TOP_POST'); return false;">
							<i class="fa fa-reddit"></i>
						</a>
					</li>
				</ul>
			</div>

			<div class="col-md-4 sidebar">
				<?php if (isset($blog_categories) && $blog_categories->num_rows() > 0) { ?>
					<div class="widget widget_categories">
						<div class="widget_title">
							<h4><span><?php echo mlx_get_lang('Blog Categories'); ?></span></h4>
						</div>
						<ul class="arrows_list list_style">
							<?php foreach ($blog_categories->result() as $b_row) {
								$cat_url = $myHelpers->bloglib->get_cat_url($b_row);
								echo '<li><a href="' . $cat_url . '" >' . ucfirst($b_row->title);
								if ($b_row->total_blog > 0)
									echo ' (' . $b_row->total_blog . ')';
								echo '</a></li>';
							} ?>
						</ul>
					</div>
				<?php } ?>


				<?php if (isset($recent_blogs) && $recent_blogs->num_rows() > 0) { ?>
					<div class="widget widget_categories">
						<div class="widget_title">
							<h4><span><?php echo mlx_get_lang('Recent Blog'); ?></span></h4>
						</div>
						<ul class="arrows_list list_style">
							<?php foreach ($recent_blogs->result() as $b_row) {
								$blog_url = $myHelpers->bloglib->get_url($b_row);
								echo '<li><a href="' . $blog_url . '" >' . ucfirst($b_row->title);
								echo '</a></li>';
							} ?>
						</ul>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>