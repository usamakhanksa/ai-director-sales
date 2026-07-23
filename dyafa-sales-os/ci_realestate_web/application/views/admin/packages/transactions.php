<div class="content-wrapper">
	<section class="content-header">
		<h1 class="page-title"><i class="fa fa-list"></i> <?php echo mlx_get_lang('Manage Transactions'); ?> </h1>


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
							<th width="30px"><?php echo mlx_get_lang('S.No.'); ?></th>
							<th><?php echo mlx_get_lang('Transaction ID'); ?></th>
							<th><?php echo mlx_get_lang('Package Details'); ?></th>
							<th><?php echo mlx_get_lang('Users'); ?></th>
							<th><?php echo mlx_get_lang('Payment Mode'); ?></th>
							<th><?php echo mlx_get_lang('Status'); ?></th>
							<th><?php echo mlx_get_lang('Update'); ?></th>
							<th><?php echo mlx_get_lang('Date'); ?></th>
							<!-- <th><?php echo mlx_get_lang('Update Status'); ?></th> -->
						</tr>
					</thead>
					<tbody>
						<?php if ($query->num_rows() > 0) {
							$i = 0;
							foreach ($query->result() as $row) {
								//var_dump($query->result());exit;
								$i++;

						?>
								<tr>

									<td><?php echo  $i; ?></td>

									<td> <?php echo ucfirst($row->order_key); ?></td>

									<td> <?php
											$package_name = $myHelpers->package_lib->package_name_by($row->package_id);
											echo '<b>' . $package_name . '</b></br>';
											$details =  $myHelpers->package_lib->get_features_by_package_id($row->package_id);
											
											foreach ($details as $key => $value) {

												if (!empty($value['details'])) {
													
													echo  $value['title'] . ' :- <span class="badge alert-info">' . $value['details'] . '</span></br>';
												}
											}
											$price = show_price_with_currency($row->order_price , $row->package_currency);
											echo 'Package Price :- ' . $price;

											?></td>

									<td> <?php
											echo 'First Name:- ' . ucfirst( get_user_meta($row->customer_id, 'first_name')) . '</br>';
											echo 'Last Name:- ' . ucfirst( get_user_meta($row->customer_id, 'last_name')) . '</br>';
											echo 'Contact No:- ' . get_user_meta($row->customer_id, 'mobile_no') . '</br>';
											?></td>
									<td> <?php if (strpos($row->payment_method, '_') !== false) {
												echo str_replace('_', ' ', $row->payment_method);
											} else {
												echo  ucfirst($row->payment_method);
											} ?></td>
									<td> <?php

											if (strpos($row->order_status, '_') !== false) {
												echo str_replace('_', ' ', $row->order_status);
												echo ' <span  class="btn-danger btn-xs" data-toggle="tooltip" data-placement="top" title="Please Complete the Process"><i class="fa fa-info"></i></span>';
											} else {
												echo  ucfirst($row->order_status);
											}
											?>
									<td> <a href="<?php $segments = array('admin','packages', 'change', EncryptClientID($row->order_id));
													echo site_url($segments); ?>" title="<?php echo mlx_get_lang('Change'); ?>" class="btn btn-warning btn-xs hide "><i class="fa fa-edit"></i></a>
										<?php if ($row->order_status == 'temp_order') { ?>
											<button title="<?php echo mlx_get_lang('Details'); ?>" class="btn btn-warning btn-xs update_process" data-order_id="<?php echo EncryptClientID($row->order_id); ?>" data-toggle='modal' data-target='#update_ccw'><i class="fa fa-gear"></i></button>
										<?php } ?>
									</td>
									</td>
									<td> <?php echo date('M d, Y h:i A', $row->order_created_on); ?></td>
								</tr>
						<?php 	}
						}	?>
					</tbody>

				</table>
			</div>
		</div>
	</section>
</div>

<div id="update_ccw" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content box">
			<form method="POST" class="add_state_form form">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">

				</div>
				
			</form>

			<div class="overlay">
				<i class="fa fa-refresh fa-spin"></i>
			</div>

		</div>
	</div>
</div>
<script>
	$(document).ready(function(e) {
		$('#update_ccw').on('shown.bs.modal', function() {
			var callback = 'get_order_details';
			var order_id = $('.update_process').data('order_id');
			$.ajax({
				url: base_url + 'admin_ajax',
				type: 'POST',
				success: function(res) {
					console.log(res.modal_body);
					$('#update_ccw').find('.modal-body').html(res.modal_body);
					$('#update_ccw .overlay').hide();
				},
				data: {
					order_id,
					callback
				},
				cache: false
			});
		});
	});
</script>