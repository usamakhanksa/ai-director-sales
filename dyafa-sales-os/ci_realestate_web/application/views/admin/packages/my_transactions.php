<div class="content-wrapper">
	<section class="content-header">
		<h1 class="page-title"><i class="fa fa-list"></i> <?php echo mlx_get_lang('My Transactions'); ?> </h1>


		<?php if (isset($_SESSION['msg']) && !empty($_SESSION['msg'])) {
			echo $_SESSION['msg'];
			unset($_SESSION['msg']);
		}
		?>
	</section>

	<section class="content">

		<div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?>">

			<div class="box-body content-box">
				<table id="example2" class="table table-bordered table-hover datatable-element-scrollx">
					<thead>
						<tr>
							<th width="30px"><?php echo mlx_get_lang('S.No.'); ?></th>
							<th><?php echo mlx_get_lang('Transaction ID'); ?></th>
							<th><?php echo mlx_get_lang('Package Name'); ?></th>
							<th><?php echo mlx_get_lang('Package Amount'); ?></th>

							<th><?php echo mlx_get_lang('Payment Mode'); ?></th>
							<th><?php echo mlx_get_lang('Status'); ?></th>

							<th><?php echo mlx_get_lang('Date'); ?></th>
							<!-- <th><?php echo mlx_get_lang('Update Status'); ?></th> -->
						</tr>
					</thead>
					<tbody>
						<?php 
							
							
							
							if ($query->num_rows() > 0) {
							$i = 0;
							foreach ($query->result() as $row) {

								$i++;

						?>
								<tr>

									<td><?php echo  $i; ?></td>

									<td> <?php echo ucfirst($row->transaction_key); ?></td>
									<td> <?php
											
											echo $myHelpers->package_lib->package_name_by($row->package_id);

											?></td>
									<td> <?php

											$amount = ucfirst($row->transaction_amount);
											$package_detail = json_decode($row->package_detail, true);
											
											
											/*if (isset($package_detail['package_currency']))
												$package_currency = $package_detail['package_currency'];
											else*/
												$package_currency = $row->package_currency;
											/*$amount = $package_currency . " " . $amount;*/
											$amount = show_price_with_currency($amount , $package_currency);
											echo $amount; ?></td>

									<td> <?php if (strpos($row->payment_mode, '_') !== false) {
												echo str_replace('_', ' ', $row->payment_mode);
											} else {
												echo  ucfirst($row->payment_mode);
											} ?></td>
									<td> <?php echo ucfirst($row->status); ?>

									</td>
									<td> <?php echo date('M d, Y h:i A', $row->transaction_date); ?></td>
								</tr>
						<?php 	}
						}	?>
					</tbody>

				</table>
			</div>
		</div>
	</section>
</div>