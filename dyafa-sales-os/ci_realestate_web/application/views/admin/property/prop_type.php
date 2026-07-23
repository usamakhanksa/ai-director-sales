<?php
$user_type = $this->session->userdata('user_type');
?>
<div class="content-wrapper">
	<section class="content-header">
		<h1 class="page-title"><i class="fa fa-sitemap"></i> <?php echo mlx_get_lang('Manage Property Types'); ?> </h1>
		<?php if (isset($_SESSION['msg']) && !empty($_SESSION['msg'])) {
			echo $_SESSION['msg'];
			unset($_SESSION['msg']);
		}
		?>
	</section>

	<section class="content">
		<div class="row">
			<div class="col-md-4 col-md-push-8">
				<?php
				$attributes = array('name' => 'add_form_post', 'class' => 'form');
				echo form_open_multipart('admin/property/prop_type', $attributes); ?>
				<input type="hidden" name="property_type_id" class="property_type_id" value="<?php if (isset($property_type_id) && !empty($property_type_id)) echo EncryptClientID($property_type_id); ?>">
				<div class="box box-<?php echo get_skin_class(); ?>">
					<div class="box-header with-border">
						<?php if (isset($property_type_id) && !empty($property_type_id)) { ?>
							<h3 class="box-title"><?php echo mlx_get_lang('Edit Property Type'); ?></h3>
						<?php } else { ?>
							<h3 class="box-title"><?php echo mlx_get_lang('Add Property Type'); ?></h3>
						<?php } ?>
					</div>
					<div class="box-body">

						<div class="form-group">
							<label for="property_type_title"><?php echo mlx_get_lang('Title'); ?> <span class="required">*</span></label>
							<input type="text" class="form-control" required="required" name="property_type_title" id="property_type_title" value="<?php if (isset($property_type_title) && !empty($property_type_title)) echo $property_type_title; ?>">
						</div>

						<div class="form-group">
							<label for="property_type_title"><?php echo mlx_get_lang('Slug'); ?> <span class="required">*</span></label>
							<input type="text" class="form-control" required="required" name="property_type_slug" id="property_type_slug" value="<?php if (isset($property_type_slug) && !empty($property_type_slug)) echo $property_type_slug; ?>">
							
							<input type="hidden" name="old_slug" value="<?php if (isset($property_type_slug) && !empty($property_type_slug)) echo $property_type_slug; ?>" />
						</div>

						<div class="form-group">
							<label for="exampleInputFile" style="display: block;"><?php echo mlx_get_lang('Photo'); ?></label>
							<?php
							$photo_url = '';
							if (isset($property_type_img) && !empty($property_type_img))
								$photo_url = $property_type_img;
							$thumb_photo = $myHelpers->global_lib->get_image_type('uploads/prop_type/', $photo_url, 'thumb'); ?>
							<div class="pl_image_container">
								<label class="custom-file-upload" data-element_id="<?php if (isset($b_id) && !empty($b_id)) echo $myHelpers->EncryptClientId($b_id); ?>" data-type="prop_type" id="pl_file_uploader_1" <?php if (isset($thumb_photo) && !empty($thumb_photo)) {
																																																							echo 'style="display:none;"';
																																																						} ?>>
									<?php echo mlx_get_lang('Drop images here'); ?>
									<br>
									<strong><?php echo mlx_get_lang('OR'); ?></strong>
									<br>
									<?php echo mlx_get_lang('Click here to select images'); ?>
								</label>
								<progress class="pl_file_progress" value="0" max="100" style="display:none;"></progress>
								<?php if (isset($thumb_photo) && !empty($thumb_photo)) { ?>

									<a class="pl_file_link" href="<?php echo base_url() . 'uploads/prop_type/' . $photo_url; ?>" download="<?php echo $photo_url; ?>" style="">
										<img src="<?php echo base_url() . 'uploads/prop_type/' . $thumb_photo; ?>" style="width:100%;">
									</a>

									<a class="pl_file_remove_img" title="Remove Image" href="#"><i class="fa fa-remove"></i></a>
								<?php } else { ?>
									<a class="pl_file_link" href="" download="" style="display:none;">
										<img src="" style="width:100%;">
									</a>
									<a class="pl_file_remove_img" title="Remove Image" href="#" style="display:none;"><i class="fa fa-remove"></i></a>
								<?php } ?>
								<input type="hidden" name="img_url" value="<?php if (isset($photo_url) && !empty($photo_url)) {
																				echo $photo_url;
																			} ?>" class="pl_file_hidden">
							</div>
						</div>

						<div class="form-group">
							<label for="property_type_status"><?php echo mlx_get_lang('Status'); ?></label>
							<div class="radio_toggle_wrapper ">
								<input type="radio" checked="checked" id="status_y" value="Y" name="status" class="toggle-radio-button" <?php
																																		if ((isset($status) && $status == 'Y') || !isset($status))
																																			echo ' checked="checked" ';
																																		?>>
								<label for="status_y"><?php echo mlx_get_lang('Active'); ?></label>

								<input type="radio" id="status_n" value="N" name="status" class="toggle-radio-button" 
							<?php
								if (isset($status) && $status == 'N')
									echo ' checked="checked" ';
								?>>
								<label for="status_n"><?php echo mlx_get_lang('In-Active'); ?></label>
							</div>
						</div>
						<label for="advance_search_options" style="margin-bottom:10px;font-size:15px;">
							<?php echo mlx_get_lang('Advance Search Options'); ?></label>
						<?php

						if (isset($meta_options)) {
							$meta_options = json_decode($meta_options, true);
							//print_r($meta_options);
							if (is_array($meta_options)) {
								foreach ($meta_options as $k => $v) {
									$$k = $v;
									extract($$k);
								}
							}
						}

						?>
						<div class="form-group">
							<label for="enable_min_bed"><?php echo mlx_get_lang('Enable Min Bed'); ?></label>
							<div class="radio_toggle_wrapper ">
								<input type="radio" id="min_bed_yes" value="Y" <?php
																				if (isset($enable_min_bed) && $enable_min_bed == 'Y') {
																					echo ' checked="checked" ';
																				}
																				?> name="adv_search_options[enable_min_bed]" class="toggle-radio-button">
								<label for="min_bed_yes"><?php echo mlx_get_lang('Yes'); ?></label>

								<input type="radio" id="min_bed_no" <?php
																	if ((isset($enable_min_bed) && $enable_min_bed == 'N') ||
																		!isset($enable_min_bed)
																	) {
																		echo ' checked="checked" ';
																	}
																	?> value="N" name="adv_search_options[enable_min_bed]" class="toggle-radio-button">
								<label for="min_bed_no"><?php echo mlx_get_lang('No'); ?></label>
							</div>
						</div>

						<div class="form-group">
							<label for="enable_min_bath"><?php echo mlx_get_lang('Enable Min Bath'); ?></label>
							<div class="radio_toggle_wrapper ">
								<input type="radio" id="min_bath_yes" value="Y" <?php
																				if (isset($enable_min_bath) && $enable_min_bath == 'Y') {
																					echo ' checked="checked" ';
																				}
																				?> name="adv_search_options[enable_min_bath]" class="toggle-radio-button">
								<label for="min_bath_yes"><?php echo mlx_get_lang('Yes'); ?></label>

								<input type="radio" id="min_bath_no" <?php
																		if ((isset($enable_min_bath) && $enable_min_bath == 'N') ||
																			!isset($enable_min_bath)
																		) {
																			echo ' checked="checked" ';
																		}
																		?> value="N" name="adv_search_options[enable_min_bath]" class="toggle-radio-button">
								<label for="min_bath_no"><?php echo mlx_get_lang('No'); ?></label>
							</div>
						</div>

					</div>
					<div class="box-footer">
						<button name="submit" type="submit" class="btn btn-<?php echo get_skin_class(); ?> pull-right" id="save_publish"><?php echo mlx_get_lang('Save'); ?></button>
					</div>
				</div>
				</form>
			</div>
			<div class="col-md-8 col-md-pull-4">
				<div class="box box-<?php echo get_skin_class(); ?>">

					<div class="box-body content-box">


						<table id="example2" class="table table-bordered table-hover datatable-element-scrollx">
							<thead>
								<tr>

									<th width="30px"><?php echo mlx_get_lang('S.No.'); ?></th>
									<th width="80px"><?php echo mlx_get_lang('Image'); ?></th>
									<th><?php echo mlx_get_lang('Title'); ?></th>
									<th><?php echo mlx_get_lang('Status'); ?></th>
									<th><?php echo mlx_get_lang('Created On'); ?></th>
									<th><?php echo mlx_get_lang('Action'); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php if ($query->num_rows() > 0) {
									$i = 0;

									foreach ($query->result() as $row) {
										$i++;

								?>
										<tr>

											<td><?php echo  $i; ?></td>
											<td>
												<?php

												if (!empty($row->img_url)) {

													if (file_exists('uploads/prop_type/' . $row->img_url)) {
														$post_image_url = base_url() . 'uploads/prop_type/' . $row->img_url;
														echo '<img src="' . $post_image_url . '" width="100">';
													} else if (file_exists('application/views/' . $theme . '/assets/images/no-property-image.png')) {
														$post_image_url = base_url() . 'application/views/' . $theme . '/assets/images/no-property-image.png';
														echo '<img src="' . $post_image_url . '" width="100">';
													}
												} else if (file_exists('application/views/' . $theme . '/assets/images/no-property-image.png')) {
													$post_image_url = base_url() . 'application/views/' . $theme . '/assets/images/no-property-image.png';
													echo '<img src="' . $post_image_url . '" width="100" >';
												}
												?>
											</td>
											<td> <?php echo ucfirst($row->title); ?></td>
											<td> <?php if ($row->status == 'Y') echo '<span class="label label-success">Active</span>';
													else if ($row->status == 'N') echo '<span class="label label-danger">In-Active</span>';
													else echo '-';
													?></td>
											<td>
												<?php
												echo date('M d, Y h:i A', $row->created_on);
												?>
											</td>
											<td class="action_block">


												<a href="<?php $segments = array('admin', 'property', 'prop_type', EncryptClientID($row->pt_id));
															echo site_url($segments); ?>" title="Edit" data-toggle="tooltip" class="btn btn-warning btn-xs"><i class="fa fa-edit fa-2x"></i></a>

												<a href="<?php $segments = array('admin', 'property', 'delete_type', EncryptClientID($row->pt_id));
															echo site_url($segments); ?>" title="Delete" data-toggle="tooltip" class="btn btn-danger  btn-xs delete-property"><i class="fa fa-trash fa-2x"></i></a>

											</td>
										</tr>
								<?php 	}
								}	?>




							</tbody>

						</table>

					</div>
				</div><!-- /.box -->
			</div>
		</div>
		<!-- /.row -->

	</section><!-- /.content -->
</div><!-- /.content-wrapper -->