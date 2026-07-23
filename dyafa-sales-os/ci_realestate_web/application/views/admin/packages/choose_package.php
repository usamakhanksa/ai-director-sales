<style>
	.widget-user-username1 {
		text-overflow: ellipsis;
		white-space: nowrap;
		overflow: hidden;
	}

	.box-widget {
		box-shadow: 0 0px 10px 2px rgba(0, 0, 0, .1);
	}
</style>
<div class="content-wrapper">
	<section class="content-header">
		<h1 class="page-title"><i class="fa fa-money"></i> <?php echo mlx_get_lang('Packages'); ?>


			<?php if (isset($_SESSION['msg']) && !empty($_SESSION['msg'])) {
				echo $_SESSION['msg'];
				unset($_SESSION['msg']);
			}
			?>
	</section>

	<section class="content">

		<div class="box box-<?php echo get_skin_class(); ?>">
			<div class="box-header with-border">
				<h3 class="box-title"><?php echo mlx_get_lang('Choose Package'); ?></h3>

			</div>
			<div class="box-body content-box">

				<div class="row">

					<?php
					/*
			echo date("d/m/Y h:i:s" , 1694671498).PHP_EOL;		
			echo date("d/m/Y h:i:s" , 1697263498).PHP_EOL;	

				*/
					
					$user_id = $this->session->userdata('user_id');

					$skin_class = get_skin_class();
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

					foreach ($query->result() as $data) {

						if ($data->purchase_limit > 0) {
							$query2 = "SELECT count(package_id) as total_bought_package from transaction where package_id=" . $data->package_id . " and user_id=" . $user_id . "
					  and status = 'completed'";

							$transactions = $myHelpers->Common_model->commonQuery($query2);
							$total_bought_package = 0;
							if ($transactions->num_rows() > 0) {
								$total_bought_package = $transactions->row()->total_bought_package;
							}
							if ($data->purchase_limit  <=  $total_bought_package)
								continue;
						}
					?>

						<div class="col-md-6 col-sm-6 col-xs-12 col-lg-4" style="margin-bottom:30px;">

							<div class="box box-widget widget-user-2">

								<div class="widget-user-header bg-<?php echo $skin_class; ?>">
									<h3 class="widget-user-username1" title="<?php echo ucwords($data->package_name); ?>"><?php echo ucwords($data->package_name); ?></h3>
									<!--<h5 class="widget-user-desc1"><?php //echo ucfirst($data->package_type); 
																		?></h5>-->
								</div>
								<div class="box-footer no-padding">
									<ul class="nav nav-stacked">
										<?php
										$package_features =  $myHelpers->package_lib->get_features_by_package_id($data->package_id);
										//echo "<pre>"; print_r($package_features); echo "</pre>";
										
										foreach ($package_features as $package_feature) {
											$bg_color = 'bg-blue';

											if (!empty($package_feature['details'])) {
												$details =  $package_feature['details'];
											} else {
												if(empty($package_feature['title'])) continue;
												$details = 	'<i class="fa fa-remove"></i>';
												$bg_color = 'bg-red';
											}
										?>


											<li><a><?php echo $package_feature['title']; ?>
													<span class="pull-right badge <?php echo $bg_color; ?>"><?php echo $details; ?></span></a></li>

										<?php } ?>



									</ul>
									<div class="lead style">
							<?php	echo 	show_price_with_currency($data->package_price , $data->package_currency);		?>
										<div class="pull-right">
											<a class="btn btn-<?php echo get_skin_class(); ?> submit-form-btn" name="submit" href="<?php echo base_url('packages/subscribe/')  . $myHelpers->global_lib->EncryptClientId($data->package_id); ?>"><?php echo ucfirst($data->purchase_button_text); ?></a>
										</div>
									</div>
								</div>

							</div><!-- /.widget-user -->
						</div>


					<?php } ?>

				</div>

			</div>


		</div>
	</section>
</div>

<style>
	.style {
		padding: 10px 15px;
	}
</style>