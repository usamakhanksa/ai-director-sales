<?php $user_type = $this->session->userdata('user_type'); ?>

<div class="content-wrapper">
	<section class="content-header">
		<h1 class="page-title"><i class="fa fa-building"></i> <?php if (isset($page_heading)) echo mlx_get_lang($page_heading);
																else echo mlx_get_lang('Manage Properties'); ?>
			<a href="<?php echo base_url(array('admin', 'property', 'add_new')); ?>" 
			class="btn btn-<?php echo get_skin_class(); ?> pull-right content-header-right-link">Add New</a>
		</h1>
	<?php 	do_action("cms_notifications");		?>
		<?php
		if (isset($_SESSION['msg']) && !empty($_SESSION['msg'])) {
			echo $_SESSION['msg'];
			unset($_SESSION['msg']);
		}
		?>

		<div class="row">
			<div class="col-md-12">
				<div class="card widget-inline">
					<div class="card-body p-0">
						<div class="row no-gutters">
							<div class="col-sm-2 col-xs-2">
								<a href="<?php echo base_url(array('admin', 'property', 'manage', 'all')); ?>" class="text-secondary">
									<div class="card shadow-none m-0 br-0 <?php if (isset($cur_active_tab) && $cur_active_tab == 'all') echo 'active';  ?>">
										<div class="card-body text-center">
											<i class="fa fa-building-o text-muted" style="font-size: 24px;"></i>
											<h3><span><?php if (isset($all_properties)) {
															echo $all_properties->num_rows();
														} else echo '0';  ?></span></h3>
											<p class="text-muted font-15 mb-0"><?php echo mlx_get_lang('All Properties'); ?></p>
										</div>
									</div>
								</a>
							</div>
							<div class="col-sm-2 col-xs-2">
								<a href="<?php echo base_url(array('admin', 'property', 'manage', 'active')); ?>" class="text-secondary">
									<div class="card shadow-none m-0 br-0 <?php if ((isset($cur_active_tab) && ($cur_active_tab == 'active' || $cur_active_tab == '')) || !isset($cur_active_tab)) echo 'active';  ?>">
										<div class="card-body text-center">
											<i class="fa fa-link text-muted" style="font-size: 24px;"></i>
											<h3><span><?php if (isset($active_properties)) {
															echo $active_properties->num_rows();
														} else echo '0';  ?></span></h3>
											<p class="text-muted font-15 mb-0"><?php echo mlx_get_lang('Active Properties'); ?></p>
										</div>
									</div>
								</a>
							</div>

							<div class="col-sm-2 col-xs-2">
								<a href="<?php echo base_url(array('admin', 'property', 'manage', 'inactive')); ?>" class="text-secondary">
									<div class="card shadow-none m-0 border-left br-0 <?php if (isset($cur_active_tab) && $cur_active_tab == 'inactive') echo 'active';  ?>">
										<div class="card-body text-center">
											<i class="fa fa-chain-broken text-muted" style="font-size: 24px;"></i>
											<h3><span><?php if (isset($inactive_properties)) {
															echo $inactive_properties->num_rows();
														} else echo '0';  ?></span></h3>
											<p class="text-muted font-15 mb-0"><?php echo mlx_get_lang('Inactive Properties'); ?></p>
										</div>
									</div>
								</a>
							</div>

							<div class="col-sm-2 col-xs-2">
								<a href="<?php echo base_url(array('admin', 'property', 'manage', 'pending')); ?>" class="text-secondary">
									<div class="card shadow-none m-0 border-left br-0 <?php if (isset($cur_active_tab) && $cur_active_tab == 'pending') echo 'active';  ?>">
										<div class="card-body text-center">
											<i class="fa fa-clock-o text-muted" style="font-size: 24px;"></i>
											<h3><span><?php if (isset($pending_properties)) {
															echo $pending_properties->num_rows();
														} else echo '0';  ?></span></h3>
											<p class="text-muted font-15 mb-0"><?php echo mlx_get_lang('Pending Properties'); ?></p>
										</div>
									</div>
								</a>
							</div>

							<div class="col-sm-2 col-xs-2">
								<a href="<?php echo base_url(array('admin', 'property', 'manage', 'featured')); ?>" class="text-secondary">
									<div class="card shadow-none m-0 border-left br-0 <?php if (isset($cur_active_tab) && $cur_active_tab == 'featured') echo 'active';  ?>">
										<div class="card-body text-center">
											<i class="fa fa-star-o text-muted" style="font-size: 24px;"></i>
											<h3><span><?php if (isset($featured_properties)) {
															echo $featured_properties->num_rows();
														} else echo '0';  ?></span></h3>
											<p class="text-muted font-15 mb-0"><?php echo mlx_get_lang('Featured Properties'); ?></p>
										</div>
									</div>
								</a>
							</div>

							<div class="col-sm-2 col-xs-2">
								<a href="<?php echo base_url(array('admin', 'property', 'manage', 'rejected')); ?>" class="text-secondary">
									<div class="card shadow-none m-0 border-left  <?php if (isset($cur_active_tab) && $cur_active_tab == 'rejected') echo 'active';  ?>">
										<div class="card-body text-center">
											<i class="fa fa-ban text-muted" style="font-size: 24px;"></i>
											<h3><span><?php if (isset($rejected_properties)) {
															echo $rejected_properties->num_rows();
														} else echo '0';  ?></span></h3>
											<p class="text-muted font-15 mb-0"><?php echo mlx_get_lang('Rejected Properties'); ?></p>
										</div>
									</div>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

	</section>

	<section class="content">

		<div class="box box-<?php echo get_skin_class(); ?>">

			<?php if (isset($cur_active_tab) && $cur_active_tab == 'rejected') { ?>
				<div class="box-header with-border">
					<h3 class="text-red box-title"> <strong><?php echo mlx_get_lang("Note"); ?>:</strong> <?php echo mlx_get_lang("Please click the eyeball action for the respective property to see comments on why your listing was not approved."); ?></h3>
				</div>
			<?php } ?>
			<div class="box-body content-box">

				<?php
				/*$loc_tax_settings = get_option('loc_tax_settings');
			$loc_tax_settings = json_decode($loc_tax_settings, true);
			if (empty($loc_tax_settings)) {
				$loc_tax_settings = array();
			}
			echo "<pre>"; print_r($loc_tax_settings);echo "</pre>"; */
			
				
				?>


				<table id="example21" class="table table-bordered table-hover datatable-element-scrollx">
					<thead>
						<tr>

							<th width="30px"><?php echo mlx_get_lang('S.No.'); ?></th>
							<?php do_action("cms_manage_properties_table_header_before"); ?>
							
							<th width="100px"><?php echo mlx_get_lang('Image'); ?></th>
							<th><?php echo mlx_get_lang('Title'); ?></th>
							
							<th><?php echo mlx_get_lang('Price'); ?></th>
							
							<th><?php echo mlx_get_lang('Location'); ?></th>

							<?php //if($user_type == 'admin'){ 
							?>
							<th><?php echo mlx_get_lang('Is Featured?'); ?></th>
							<?php //} 
							?>
							<th><?php echo mlx_get_lang('Status'); ?></th>
							<th><?php echo mlx_get_lang('Created On'); ?></th>
							<?php if ($user_type == 'admin') { ?>
								<th><?php echo mlx_get_lang('Created By'); ?></th>
							<?php } ?>
							
							<?php do_action("cms_manage_properties_table_header_after"); ?>
							<th class="action_block"><?php echo mlx_get_lang('Action'); ?></th>
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
									
						<?php do_action("cms_manage_properties_table_data_before" , $row);?>
									
									<td>
										<?php
										$post_image_url = base_url() . 'application/views/' . $theme . '/assets/images/no-property-image.png';

										if (!empty($row->property_images)) {
											$p_images = $myHelpers->ajax_images_lib->get_property_image($row->p_id, 'thumbnail');

											if (!empty($p_images) && file_exists($p_images[0])) {
												$post_image_url = base_url() . $p_images[0];
											}
										}
										echo '<div class="manage-image-container lazy-load-processing" style="max-height: 80px;min-height: 80px;"><img class="lazy-img-elem" data-src="' . $post_image_url . '"></div>';
										?>
									</td>
									<td> <?php 
									echo ucfirst(stripslashes($row->title))."<br/>"; 
									echo "Type:<strong>".ucfirst($row->prop_type_title). "</strong><br/>";
									echo "For: <strong>".ucfirst($row->property_for). "</strong><br/>";
									
									echo "Size: <strong>";
										$size = "";
										$size_field = (!empty($row->size)) ? $row->size : "";
										$size_val = explode("~", $size_field);
										if (isset($size_val[0])) $size = $size_val[0];
										if (isset($size_val[1])) $size .= " " . $size_val[1];
										
									echo $size. "</strong><br/>";
									
									?></td>
									
									
									<td>
										<?php /*echo $currency_symbol; */
										$args = array("currency_symbol" => $currency_symbol);
										echo moneyFormatDollar($row->price, $args);

										if ($row->property_for == 'Rent') {
											echo '/' . mlx_get_lang('Month');
										} ?></td>
									

									<td style="width: 180px;">
										<?php
										/*$enable_property_for_cities = $myHelpers->global_lib->get_option('enable_property_for_cities');
										if ($enable_property_for_cities == 'Y' && !empty($row->city)) {
											echo 'City: <b>' . ucfirst($myHelpers->global_lib->get_org_country_state_city_title_callback_func($row->city, 'city')) . '</b><br/>';
										} else {
											echo 'City: N/A</br>';
										}
										$enable_property_for_states = $myHelpers->global_lib->get_option('enable_property_for_states');
										if ($enable_property_for_states == 'Y' && !empty($row->state)) {
											echo 'State: <b>' . ucfirst($myHelpers->global_lib->get_org_country_state_city_title_callback_func($row->state, 'state')) . '</b><br/>';
										} else {
											echo 'State: N/A</br>';
										}
										if (!empty($row->state)) {

											echo 'Country: <b>' . ucfirst($myHelpers->global_lib->get_org_country_state_city_title_callback_func($row->country, 'country')) . '</b>';
										} else {
											echo 'Country: N/A';
										}*/
										if ( !empty($row->city)) {
											echo 'City: <b>' . $row->city . '</b><br/>';
										} else {
											echo 'City: N/A</br>';
										}
										if ( !empty($row->state)) {
											echo 'State: <b>' . $row->state . '</b><br/>';
										} else {
											echo 'State: N/A</br>';
										}
										if (!empty($row->country)) {

											echo 'Country: <b>' . $row->country . '</b>';
										} else {
											echo 'Country: N/A';
										}
										?>
									</td>

									<td> <input type="checkbox" data-p_id="<?php echo EncryptClientID($row->p_id); ?>" <?php if ($row->is_feat == 'Y') echo 'checked="checked"'; ?> class="minimal featured-prod-checkbox">
									</td>
									<?php //} 
									?>
									<td>
										<?php if ($row->status == 'publish') echo '<span class="label label-success">' . mlx_get_lang('Publish') . '</span>';
										else if ($row->status == 'draft') echo '<span class="label label-info">' . mlx_get_lang('Draft') . '</span>';
										else if ($row->status == 'pending') echo '<span class="label label-warning">' . mlx_get_lang('Pending') . '</span>';
										else if ($row->status == 'reject') echo '<span class="label label-danger">' . mlx_get_lang('Reject') . '</span>';
										else echo '-';
										?>
									<td>
										<?php
										echo date('M d, Y', $row->created_on);
										echo '<br>';
										echo date('h:i A', $row->created_on);
										?>
									</td>
									<?php if ($user_type == 'admin') { ?>
										<td>
											<?php
											if ($row->created_by != 1) {
												echo '<a href="' . site_url(array('user', 'edit', EncryptClientID($row->created_by))) . '">';
											}
											echo $myHelpers->global_lib->get_user_meta($row->created_by, 'first_name');
											echo '&nbsp;';
											echo $myHelpers->global_lib->get_user_meta($row->created_by, 'last_name');
											if ($row->created_by != 1) {
												echo '</a>';
											}
											?>
										</td>
									<?php }

									?>
									
									
									<?php do_action("cms_manage_properties_table_data_after" , $row);?>
									
									<td class="action_block">

		<?php do_action('admin_property_actions',array('p_id'=> EncryptClientID($row->p_id))); ?>

		<?php if ($row->status == 'publish') { ?>
			<a target="_blank" href="<?php $segments = array('admin', 'property', $row->slug . "~" . $row->p_id);
		echo str_replace("/admin", "", site_url($segments)); ?>" title="<?php echo mlx_get_lang('View'); ?>" data-toggle="tooltip" 
			class="btn btn-info btn-xs"><i class="fa fa-eye fa-2x"></i></a>
		<?php } else { ?>
			<a href="<?php $segments = array('admin', 'property', 'view', EncryptClientID($row->p_id));
						echo site_url($segments); ?>" title="<?php echo mlx_get_lang('View'); ?>" data-toggle="tooltip" 
						class="btn btn-info btn-xs"><i class="fa fa-eye fa-2x"></i></a>
		<?php } ?>
		
		<?php if ($user_type == 'admin') { ?>
		<a href="<?php $segments = array('admin', 'property', 'clone', EncryptClientID($row->p_id));
						echo site_url($segments); ?>" title="<?php echo mlx_get_lang('Clone'); ?>" data-toggle="tooltip" 
						class="btn btn-primary btn-xs"><i class="fa fa-clone fa-2x"></i></a>
		<?php } ?>
		
		<?php if ($user_type == 'admin' || ($row->status != 'pending' && $row->status != 'reject')) { ?>
			<a href="<?php $segments = array('admin', 'property', 'edit', EncryptClientID($row->p_id));
						echo site_url($segments); ?>" title="<?php echo mlx_get_lang('Edit'); ?>" data-toggle="tooltip" 
						class="btn btn-warning btn-xs"><i class="fa fa-edit fa-2x"></i></a>
		<?php } ?>

		<a href="<?php $segments = array('admin', 'property', 'delete', EncryptClientID($row->p_id),$return);
					echo site_url($segments); ?>" title="<?php echo mlx_get_lang('Delete'); ?>" data-toggle="tooltip" 
					class="btn btn-danger  btn-xs delete-property"><i class="fa fa-trash fa-2x"></i></a>

									</td>
								</tr>
						<?php 	}
						}	?>




					</tbody>

				</table>
			</div>
		</div><!-- /.box -->

		<!-- /.row -->

	</section><!-- /.content -->
</div><!-- /.content-wrapper -->

<?php

do_action("admin_footer_scripts", "admin_property_manage");

?>