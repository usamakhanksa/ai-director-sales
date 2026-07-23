 <div class="content-wrapper">
 	<section class="content-header">

 		<h1 class="page-title"><i class="fa fa-server"></i> <?php echo mlx_get_lang('Manage Banners'); ?> 
			<a href="<?php echo base_url(array('admin', 'banner', 'add_new')); ?>" 
				class="btn btn-<?php echo get_skin_class(); ?> pull-right content-header-right-link">Add New</a></h1>

 		<?php if (isset($_SESSION['msg']) && !empty($_SESSION['msg'])) {
				echo $_SESSION['msg'];
				unset($_SESSION['msg']);
			}
			?>

 	</section>

 	<section class="content">
 		<div class="box box-<?php echo get_skin_class(); ?>">




 			<div class="box-body content-box">

 				<table id="example2" class="table table-bordered table-hover datatable-element-scrollx">
 					<thead>
 						<tr>

 							<th width="75px" class="pad-right-5"><?php echo mlx_get_lang('S No.'); ?></th>
 							<th class="pad-right-5"><?php echo mlx_get_lang('Title'); ?></th>
 							<th class="pad-right-5" width="150px"><?php echo mlx_get_lang('Image'); ?></th>
 							<th><?php echo mlx_get_lang('Assign To'); ?></th>
 							<th><?php echo mlx_get_lang('Status'); ?></th>
 							<th width="150px" class="pad-right-5"><?php echo mlx_get_lang('Action'); ?></th>
 						</tr>
 					</thead>
 					<tbody>
 						<?php if ($query->num_rows() > 0) {
								$n = 1;
								foreach ($query->result() as $row) {
							?>
 								<tr>
 									<td><?php echo  $n++; ?></td>
 									<td><?php echo  $row->b_title; ?></td>
 									<td>
 										<?php
											//echo file_exists(base_url('uploads/banner/') . $row->b_image);
											if (!empty($row->b_image) && file_exists('uploads/banner/' . $row->b_image)) { ?>
 											<div class="manage-image-container lazy-load-processing">
 												<img class="lazy-img-elem" data-src="<?php echo base_url() . 'uploads/banner/' . $row->b_image; ?>">
 											</div>
 										<?php } ?>
 									</td>
 									<td>
 										<?php


											$assigned_result = $myHelpers->Common_model->commonQuery("
							select * from banner_assigned_to where banner_id = $row->b_id order by banner_id ASC
						");
											if ($assigned_result->num_rows() > 0) {
												foreach ($assigned_result->result() as $arow) {
													if (empty($myHelpers->global_lib->get_lang_title_by_code($arow->for_lang)))
														continue;
													if ($arow->assign_type == 'static') {
														if (isset($static_pages) && isset($static_pages[$arow->assign_id]))
															echo '<strong>' . $static_pages[$arow->assign_id] . '</strong>';
														else
															echo '<strong>' . ucfirst($arow->assign_id) . '</strong>';
														echo ' - ';
														echo $myHelpers->global_lib->get_lang_title_by_code($arow->for_lang);
														echo '<br />';
													} else if ($arow->assign_type == 'property') {
														if (isset($property_list) && $property_list->num_rows() > 0) {
															foreach ($property_list->result() as $prow) {
																if ($prow->p_id == $arow->assign_id) {
																	echo mlx_get_lang('Property');
																	echo ' - ';
																	echo '<strong>' . ucfirst($prow->title) . '</strong>';
																	echo ' - ';
																	echo $myHelpers->global_lib->get_lang_title_by_code($arow->for_lang);
																	echo '<br />';
																}
															}
														}
													} else if ($arow->assign_type == 'page') {
														if (isset($page_list) && $page_list->num_rows() > 0) {
															foreach ($page_list->result() as $prow) {
																if ($prow->page_id == $arow->assign_id) {
																	echo mlx_get_lang('Page');
																	echo ' - ';
																	echo '<strong>' . ucfirst($prow->page_title) . '</strong>';
																	echo ' - ';
																	echo $myHelpers->global_lib->get_lang_title_by_code($arow->for_lang);
																	echo '<br />';
																}
															}
														}
													}
												}
											} else {
												echo mlx_get_lang('Not Assigned Yet');
											}
											?>
 									</td>
 									<td>
 										<?php if ($row->b_status == 'Y') echo '<span class="label label-success">' . mlx_get_lang("Active") . '</span>';
											else if ($row->b_status == 'N') echo '<span class="label label-danger">' . mlx_get_lang("In-Active") . '</span>';
											else echo '-';
											?>
 									</td>

 									<td class="action_block">

 										<a href="<?php $segments = array('admin', 'banner', 'edit', EncryptClientID($row->b_id));
													echo site_url($segments); ?>" title="<?php echo mlx_get_lang('Edit'); ?>" data-toggle="tooltip" class="btn btn-warning btn-xs"><i class="fa fa-edit "></i></a>

 										<a href="<?php $segments = array('admin', 'banner', 'delete', EncryptClientID($row->b_id));
													echo site_url($segments); ?>" title="<?php echo mlx_get_lang('Delete'); ?>" data-toggle="tooltip" class="btn btn-danger btn-xs"><i class="fa fa-trash "></i></a>

 									</td>
 								</tr>
 						<?php 	}
							}	?>




 					</tbody>

 				</table>
 			</div>
 		</div>
 	</section>
 </div>