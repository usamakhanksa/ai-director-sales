<div class="content-wrapper">
	<section class="content-header">
		<h1 class="page-title"><i class="fa fa-themeisle"></i> <?php echo mlx_get_lang('Themes'); ?></h1>
		<?php if (isset($_SESSION['msg']) && !empty($_SESSION['msg'])) {
			echo $_SESSION['msg'];
			unset($_SESSION['msg']);
		}
		?>
	</section>

	<section class="content">
		<?php
		$attributes = array('name' => 'add_form_post', 'class' => 'homepage_section_form');
		//echo form_open_multipart('main/home_page',$attributes); 
		?>
		<div class="row">
			<div class="col-md-12">

				<div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?> homepage_section_container">
					<div class="box-body">
						<div class="row">
							<?php


							$front_url = site_url();
							$front_url = str_replace("/admin", "", $front_url);

							?>

							<?php foreach ($front_end_themes as $theme_key => $theme_details) { ?>

								<div class="col-md-3 selected">
									<figure style="margin:0px;">
										<img src="<?php echo site_url('application/views/themes/') . $theme_key . '/' . $theme_details['theme_image']; ?>" style="width:100%; height:auto;" />
									</figure>

									<div style="margin-top:5px;" class="text-center"><strong><?php echo mlx_get_lang($theme_details['name']); ?></strong> </div>
								</div>
							<?php
							} ?>
						</div>
					</div>

				</div>

			</div>
			</form>
	</section>
</div>