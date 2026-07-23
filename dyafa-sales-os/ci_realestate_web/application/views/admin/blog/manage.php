<div class="content-wrapper">
	<section class="content-header">
		<h1 class="page-title"><i class="fa fa-newspaper-o"></i> <?php echo mlx_get_lang('Manage Blogs'); ?>
			<a href="<?php echo base_url(array('admin/blog', 'add_new')); ?>" class="btn btn-<?php echo get_skin_class(); ?> pull-right content-header-right-link">Add New</a>
		</h1>
		<?php if (isset($_SESSION['msg']) && !empty($_SESSION['msg'])) {
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
								<a href="<?php echo base_url(array('admin', 'blog', 'manage', 'all')); ?>" class="text-secondary">
									<div class="card shadow-none m-0 br-0 <?php if (isset($cur_active_tab) && $cur_active_tab == 'all') echo 'active';  ?>">
										<div class="card-body text-center">
											<i class="fa fa-newspaper-o text-muted" style="font-size: 24px;"></i>
											<h3><span><?php if (isset($all_blogs)) {
															echo $all_blogs->num_rows();
														} else echo '0';  ?></span></h3>
											<p class="text-muted font-15 mb-0"><?php echo mlx_get_lang('All Blogs'); ?></p>
										</div>
									</div>
								</a>
							</div>
							<div class="col-sm-2 col-xs-2">
								<a href="<?php echo base_url(array('admin', 'blog', 'manage', 'active')); ?>" class="text-secondary">
									<div class="card shadow-none m-0 br-0 <?php if ((isset($cur_active_tab) && ($cur_active_tab == 'active' || $cur_active_tab == '')) || !isset($cur_active_tab)) echo 'active';  ?>">
										<div class="card-body text-center">
											<i class="fa fa-link text-muted" style="font-size: 24px;"></i>
											<h3><span><?php if (isset($active_blogs)) {
															echo $active_blogs->num_rows();
														} else echo '0';  ?></span></h3>
											<p class="text-muted font-15 mb-0"><?php echo mlx_get_lang('Active Blogs'); ?></p>
										</div>
									</div>
								</a>
							</div>

							<div class="col-sm-2 col-xs-2">
								<a href="<?php echo base_url(array('admin', 'blog', 'manage', 'inactive')); ?>" class="text-secondary">
									<div class="card shadow-none m-0 border-left br-0 <?php if (isset($cur_active_tab) && $cur_active_tab == 'inactive') echo 'active';  ?>">
										<div class="card-body text-center">
											<i class="fa fa-chain-broken text-muted" style="font-size: 24px;"></i>
											<h3><span><?php if (isset($inactive_blogs)) {
															echo $inactive_blogs->num_rows();
														} else echo '0';  ?></span></h3>
											<p class="text-muted font-15 mb-0"><?php echo mlx_get_lang('Inactive Blogs'); ?></p>
										</div>
									</div>
								</a>
							</div>

							<div class="col-sm-2 col-xs-2">
								<a href="<?php echo base_url(array('admin', 'blog', 'manage', 'pending')); ?>" class="text-secondary">
									<div class="card shadow-none m-0 border-left br-0 <?php if (isset($cur_active_tab) && $cur_active_tab == 'pending') echo 'active';  ?>">
										<div class="card-body text-center">
											<i class="fa fa-clock-o text-muted" style="font-size: 24px;"></i>
											<h3><span><?php if (isset($pending_blogs)) {
															echo $pending_blogs->num_rows();
														} else echo '0';  ?></span></h3>
											<p class="text-muted font-15 mb-0"><?php echo mlx_get_lang('Pending Blogs'); ?></p>
										</div>
									</div>
								</a>
							</div>

							<div class="col-sm-2 col-xs-2">
								<a href="<?php echo base_url(array('admin', 'blog', 'manage', 'future_publish')); ?>" class="text-secondary">
									<div class="card shadow-none m-0 border-left br-0 <?php if (isset($cur_active_tab) && $cur_active_tab == 'future_publish') echo 'active';  ?>">
										<div class="card-body text-center">
											<i class="fa fa-clock-o text-muted" style="font-size: 24px;"></i>
											<h3><span><?php if (isset($future_publish_blogs)) {
															echo $future_publish_blogs->num_rows();
														} else echo '0';  ?></span></h3>
											<p class="text-muted font-15 mb-0"><?php echo mlx_get_lang('Future Publish Blogs'); ?></p>
										</div>
									</div>
								</a>
							</div>

							<div class="col-sm-2 col-xs-2">
								<a href="<?php echo base_url(array('admin', 'blog', 'manage', 'rejected')); ?>" class="text-secondary">
									<div class="card shadow-none m-0 border-left  <?php if (isset($cur_active_tab) && $cur_active_tab == 'rejected') echo 'active';  ?>">
										<div class="card-body text-center">
											<i class="fa fa-ban text-muted" style="font-size: 24px;"></i>
											<h3><span><?php if (isset($rejected_blogs)) {
															echo $rejected_blogs->num_rows();
														} else echo '0';  ?></span></h3>
											<p class="text-muted font-15 mb-0"><?php echo mlx_get_lang('Rejected Blogs'); ?></p>
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

			<div class="box-body content-box">
				<table id="example2" class="table table-bordered table-hover datatable-element-scrollx">
					<thead>
						<tr>

							<th width="30px"><?php echo mlx_get_lang('S.No.'); ?></th>
							<th width="150px"><?php echo mlx_get_lang('Image'); ?></th>
							<th><?php echo mlx_get_lang('Title'); ?></th>
							<th><?php echo mlx_get_lang('Publish On'); ?></th>
							<th><?php echo mlx_get_lang('Created On'); ?></th>
							<th><?php echo mlx_get_lang('Status'); ?></th>
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
										$post_image_url = base_url() . 'application/views/' . $theme . '/assets/images/no-property-image.jpg';
										if (!empty($row->image)) {
											if (file_exists('uploads/blog/' . $row->image)) {
												$post_image_url = base_url() . 'uploads/blog/' . $row->image;
											}
										}
										echo '<div class="manage-image-container lazy-load-processing">
							<img class="lazy-img-elem" data-src="' . $post_image_url . '">
							</div>';
										?>
									</td>
									<td> <?php echo ucfirst($row->title); ?></td>
									<td>
										<?php
										echo date('M d, Y h:i A', $row->publish_on);
										?>
									</td>
									<td>
										<?php
										echo date('M d, Y h:i A', $row->created_on);
										?>
									</td>
									<td> <?php
											if ($row->publish_on > $row->created_on) echo '<span class="label label-primary">' . mlx_get_lang('Future Publish') . '</span>';
											else if ($row->status == 'draft') echo '<span class="label label-info">' . ucfirst($row->status) . '</span>';
											else if ($row->status == 'pending') echo '<span class="label label-warning">' . ucfirst($row->status) . '</span>';
											else if ($row->status == 'publish') echo '<span class="label label-success">' . ucfirst($row->status) . '</span>';
											else echo '-';
											?>
									</td>
									<td class="action_block">

										<a href="<?php $segments = array('admin', 'blog', $row->slug);
													echo str_replace('admin/', '', base_url($segments)); ?>" title="View" target="_blank" class="btn btn-info btn-xs"><i class="fa fa-eye"></i></a>

										<a href="<?php $segments = array('admin', 'blog', 'edit', EncryptClientID($row->b_id));
													echo site_url($segments); ?>" title="<?php echo mlx_get_lang('Edit'); ?>" class="btn btn-warning btn-xs"><i class="fa fa-edit"></i></a>

			<a href="<?php $segments = array('admin', 'blog', 'delete', EncryptClientID($row->b_id));
						echo site_url($segments); ?>" title="<?php echo mlx_get_lang('Delete'); ?>" onclick="return confirm('<?php echo mlx_get_lang('Are you sure?'); ?>');" class="btn btn-danger btn-xs"><i class="fa fa-remove"></i></a>

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