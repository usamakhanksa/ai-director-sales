<?php
$user_type = $this->session->userdata('user_type');
?>
<div class="content-wrapper">
	<section class="content-header">
		<h1 class="page-title"><i class="fa fa-sitemap"></i> <?php echo mlx_get_lang('Manage Blog Categories'); ?> </h1>
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
				echo form_open_multipart('admin/blog/category', $attributes); ?>
				<input type="hidden" name="cat_id" class="cat_id" 
				value="<?php if (isset($cat_id) && !empty($cat_id)) echo EncryptClientID($cat_id); ?>">
				<div class="box box-<?php echo get_skin_class(); ?>">
					<div class="box-header with-border">
						<?php if (isset($cat_id) && !empty($cat_id)) { ?>
							<h3 class="box-title"><?php echo mlx_get_lang('Edit Blog Category'); ?></h3>
						<?php } else { ?>
							<h3 class="box-title"><?php echo mlx_get_lang('Add Blog Category'); ?></h3>
						<?php } ?>
					</div>
					<div class="box-body">

						<div class="form-group">
							<label for="title"><?php echo mlx_get_lang('Title'); ?> <span class="required">*</span></label>
							<input type="text" class="form-control" required="required" name="title" id="title" value="<?php if (isset($title) && !empty($title)) echo $title; ?>">
						</div>

						<div class="form-group">
							<label for="blog_category_status"><?php echo mlx_get_lang('Status'); ?></label>
							<div class="radio_toggle_wrapper ">
								<input type="radio" checked="checked" id="status_y" value="Y" name="status" class="toggle-radio-button" <?php
																																		if ((isset($status) && $status == 'Y') || !isset($status))
																																			echo ' checked="checked" ';
																																		?>>
								<label for="status_y"><?php echo mlx_get_lang('Active'); ?></label>

								<input type="radio" id="status_n" value="N" name="status" class="toggle-radio-button" <?php
																														if (isset($status) && $status == 'N')
																															echo ' checked="checked" ';
																														?>>
								<label for="status_n"><?php echo mlx_get_lang('In-Active'); ?></label>
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


												<a href="<?php $segments = array('admin/blog', 'category', EncryptClientID($row->c_id));
															echo site_url($segments); ?>" title="Edit" data-toggle="tooltip" class="btn btn-warning btn-xs"><i class="fa fa-edit fa-2x"></i></a>

												<a href="<?php $segments = array('admin/blog', 'delete_cat', EncryptClientID($row->c_id));
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