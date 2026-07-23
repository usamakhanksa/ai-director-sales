<?php
$attributes = array('name' => 'add_form_post', 'class' => 'form');
echo form_open_multipart('admin/plugins/add_new', $attributes);
?>
<input type="hidden" name="user_id" class="user_id" value="<?php echo $this->session->userdata('user_id'); ?>">
<div class="content-wrapper">
	<section class="content-header">
		<h1 class="page-title"><i class="fa fa-plug"></i> <?php echo mlx_get_lang('Add New Plugin'); ?>

		</h1>
		<?php if (isset($_SESSION['msg']) && !empty($_SESSION['msg'])) {
			echo $_SESSION['msg'];
			unset($_SESSION['msg']);
		}
		?>
	</section>

	<section class="content">
		<div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?>">
			<div class="box-body content-box">
				<div class="row">
					<div class="col-lg-12 mt-lg-0 mt-4">
						<div class="pl_zip_container">
							<label class="custom-file-upload form-control" data-element_id="" data-type="application/modules" id="pl_file_uploader_1">
								<?php echo mlx_get_lang('Drop Zip File Here'); ?>
								<br>
								<strong><?php echo mlx_get_lang('OR'); ?></strong>
								<br>
								<?php echo mlx_get_lang('Click Here to Select Zip'); ?>
							</label>
							<progress class="pl_file_progress" value="0" max="100" style="display:none;"></progress>

							<a class="pl_file_link" href="" download="" style="display:none;">
								<img src="">
								<span class="pl_file_remove_img btn btn-danger btn-sm px-1 py-0 position-absolute m-0 end-6 mt-0" title="Remove Image" style="display:none;"><i data-feather="trash" width="14"></i></span>
							</a>
							<input type="hidden" name="" value="" class="pl_file_hidden">
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
</form>