<?php $this->load->view("$theme/sidebar-left"); ?>
<style>
	.box-widget {
		box-shadow: none;
	}

	.widget-user i {
		font-size: 3em;
		color: #fff;
	}

	.widget-user .widget-user-image {
		position: absolute;
		top: 73px;
		left: 50%;
		margin-left: 0%;
		transform: translateX(-50%);
		padding: 18px;
		border: 3px solid #fff;
		border-radius: 50%;
		width: 90px;
		text-align: center;
	}

	.widget-user .box-footer {
		padding-top: 45px;
		border: 1px solid #ddd;
		border-top: 1px none;
	}
</style>

<?php
$plugin_list = $myHelpers->plugins_lib->get_plugin_headers();

$plugin_json_list = $myHelpers->plugins_lib->get_plugin_header_from_json();

if (!empty($plugin_json_list)) {
	$array_keys = array_keys($plugin_json_list);
	foreach ($array_keys as $akk => $avv) {
		if (isset($plugin_list[$avv])) {
			unset($plugin_list[$avv]);
			$plugin_list[$avv] = $plugin_json_list[$avv];
		}
	}
}

ksort($plugin_list);


?>

<script>
	$(document).ready(function() {

		$('.change-status-btn').click(function() {
			var thiss = $(this);
			$(this).parents('.box-widget').find('.overlay').show();
			var cur_status = $(this).attr('data-status');
			var plugin_name = $(this).parents('.box-widget').attr('data-p_name');
			$.ajax({
				url: base_url + 'admin/ajax/update_plugins_setting_callback_func',
				type: 'POST',
				success: function(res) {
					if (res == 'success') {
						thiss.parents('.box-widget').find('.cur_status').val(cur_status);
						thiss.parents('.box-widget').find('.deactive-block,.active-block').hide();
						if (cur_status == 'Y') {
							thiss.parents('.box-widget').find('.deactive-block').show();
						} else {
							thiss.parents('.box-widget').find('.active-block').show();
						}
						setTimeout(function() {
							window.location.reload();
						}, 100);
					} else {
						window.location.reload();
					}
					thiss.parents('.box-widget').find('.overlay').hide();

				},
				data: {
					plugin_name: plugin_name,
					cur_status: cur_status
				},
				cache: false
			});
			return false;
		});
	});
</script>

<div class="content-wrapper">
	<section class="content-header">
		<h1 class="page-title"><i class="fa fa-plug"></i> <?php echo mlx_get_lang('Manage Plugins'); ?> 

		<a href="<?php echo base_url(array('admin/plugins', 'add_new')); ?>" class="btn btn-<?php echo $myHelpers->global_lib->get_skin_class(); ?> pull-right content-header-right-link">Add New</a>
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

					<?php

					$skin_class = $myHelpers->global_lib->get_skin_class();
					if ($skin_class == 'default')
						$skin_class = 'gray';
					else if ($skin_class == 'success')
						$skin_class = 'green';
					else if ($skin_class == 'danger')
						$skin_class = 'red';
					else if ($skin_class == 'warning')
						$skin_class = 'yellow';
					else if ($skin_class == 'primary')
						$skin_class = 'blue';

					if (isset($plugin_list) && !empty($plugin_list)) {
						$n = 0;
						foreach ($plugin_list as $k => $v) {
							$n++;
					?>
							<div class="col-md-4">
								<div class="box box-widget widget-user" data-p_name="<?php echo $k; ?>">
									<div class="overlay" style="display:none;">
										<i class="fa fa-refresh fa-spin"></i>
									</div>
									<input type="hidden" class="cur_status" value="<?php if (isset($v['status'])) echo $v['status'];
																					else echo 'N'; ?>">
									<div class="widget-user-header bg-<?php echo $skin_class; ?>-active">
										<h4 class="widget-user-username text-center"><?php echo ucwords($v['plugin_name']); ?></h4>

									</div>
									<!--
								<div class="widget-user-image bg-<?php echo $skin_class; ?>">
								  <i class="fa <?php echo $v['icon']; ?>"></i>
								</div>
								-->
									<div class="box-footer" style="padding-top:15px;">
										<h5 class="widget-user-desc text-center"><?php echo ucwords($v['plugin_description']); ?></h5>
										<div class="row">
											<div class="col-sm-12 text-center active-block" style="<?php if (isset($v['status']) && $v['status'] == 'Y') echo 'display:none;'; ?>">
												<button class="btn btn-success btn-block change-status-btn" data-status="Y">Activate</button>
											</div>
											<div class="deactive-block" style="<?php if ((isset($v['status']) && $v['status'] == 'N') || empty($v['status'])) echo 'display:none;'; ?>">
												<div class="col-sm-12">
													<button class="btn btn-block btn-warning  change-status-btn" data-status="N">Deactivate</button>
												</div>
												<!--
										<div class="col-sm-6">
											<?php if (isset($v['settings'])) { ?>
												<a href="<?php echo base_url(array($v['settings'])); ?>" class="btn btn-block btn-default">Settings</a>
											<?php } else if (isset($v['help'])) { ?>
												<a href="<?php echo base_url(array($v['help'])); ?>" class="btn btn-block btn-default">Help</a>
											<?php  } ?>
										</div>
										-->
											</div>
										</div>
									</div>
								</div>
							</div>
					<?php
							if ($n % 3 == 0)
								echo '<div class="clearfix"></div>';
						}
					}
					?>

				</div>
			</div>
		</div>
	</section>
</div>