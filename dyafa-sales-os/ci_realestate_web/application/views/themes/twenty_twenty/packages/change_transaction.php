<?php
	
	if(isset($transaction) && $transaction->num_rows() > 0)
	{
		
		$row = $transaction->row();
		
		$t_id = $row->transaction_id;
		$transaction_key = $row->transaction_key;
		$packages_id = $row->packages_id;
		$package_detail = $row->package_detail;
		$user_id = $row->user_id;
		$payment_mode = $row->payment_mode;
		$transaction_amount = $row->transaction_amount;
		$transaction_date = $row->transaction_date;
		$status = $row->status;
		 if(isset($package_detail)){ 
		$package_data = json_decode($package_detail,true);
		$currency = $package_data['package_currency'];
		
		$package_name = $package_data['package_name'];
		}
		
	}
	
?>
   
      <div class="content-wrapper">
        <section class="content-header">
          <h1 class="page-title"><i class="fa fa-edit"></i> <?php echo mlx_get_lang('Edit Status'); ?> </h1>
          <?php echo validation_errors(); ?>
        </section>
		<section class="content">
             <?php  $attributes = array('name' => 'add_form_post','class' => 'form');		 			
			echo form_open_multipart('packages/change',$attributes); ?>
			<input type="hidden" name="t_id" class="t_id" value="<?php echo $myHelpers->EncryptClientId($t_id); ?>">
			
				<div class="row">
					<div class="col-md-8">   
						<div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?>">
									<div class="box-header with-border">
										<h3 class="box-title"><?php echo mlx_get_lang('Transaction Details'); ?></h3>
										<div class="box-tools pull-right">
											<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
											
										</div>
									</div>
								
									<div class="box-body">
										
												<div class="form-group">
												<label for="package"><?php echo mlx_get_lang('Transaction Key'); ?> <span class="text-red">*</span></label>
												<input type="text" class="form-control" required="required" name="transaction_key" id="transaction_key" 
												value="<?php if(isset($transaction_key)){ echo $transaction_key; } ?>" disabled="false">
												</div>
												<div class="form-group">
												<label for="packages_price"><?php echo mlx_get_lang('Package Detail'); ?><span class="text-red">*</span></label>

												<input type="text" class="form-control"  id="packages_name" name="packages_name" 
												value="<?php if(isset($package_name)){ 
													
														echo $package_name;
													 } ?>" disabled="false">
													 <input type="hidden" name="package_id" value="<?php echo $packages_id; ?>"/>
												</div>

											<div class="form-group">
												<label for="number_of_listing"><?php echo mlx_get_lang('Payment Mode'); ?><span class="text-red">*</span></label>
												<input type="text" class="form-control"  id="payment_mode" name="payment_mode" 
												value="<?php if(isset($payment_mode)){ echo $payment_mode; } ?>" disabled="false">
											</div>	
											<div class="form-group">
												<label for="auto_active"><?php echo mlx_get_lang('Transaction Amount'); ?><span class="text-red">*</span></label>
												<input type="text" class="form-control"  id="transaction_amount" name="transaction_amount" 
												value="<?php if(isset($transaction_amount)){ echo $currency.' '.$transaction_amount; } ?>" disabled="false">
											</div>	
											<div class="form-group">
												<label for="auto_active"><?php echo mlx_get_lang('Transaction Date'); ?><span class="text-red">*</span></label>
												<input type="text" class="form-control"  id="date" name="transaction_date" 
												value="<?php if(isset($transaction_date)){ echo date('M d, Y h:i A',$transaction_date); } ?>" disabled="false">
											</div>	
											<div class="form-group">
												<label for="user_name"><?php echo mlx_get_lang('User Name'); ?><span class="text-red">*</span></label>
												<input type="text" class="form-control"  id="user_name" name="user_name" 
												value="<?php if(isset($user_id)){ echo $this->global_lib->get_user_meta($user_id,'first_name'); } ?>" disabled="false">
												<input type="hidden" name="user_id" value="<?php echo $user_id; ?>"/>
											</div>										
										</div>
											
										
									</div>
							</div>
					 
			
					<div class="col-md-4">
						<div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?>">
						<div class="box-header with-border">
							<h3 class="box-title"><?php echo mlx_get_lang('Status'); ?></h3>
							<div class="box-tools pull-right">
								<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
								
							</div>
							</div>
							<div class="box-body">
							
							<div class="form-group">
								<?php if(!empty($status) && $status != 'Completed'){?>
								<label><?php echo mlx_get_lang('Transaction Status'); ?><span class="required">*</span></label>
								<select class="form-control select2_elem" name="status" id="status" required>
									<option value=""><?php echo mlx_get_lang('Select Payment Status'); ?></option>
									<option value="Completed" <?php if(isset($status) && $status== 'Completed'){ echo 'selected'; }?>>
									<?php echo mlx_get_lang('Completed'); ?></option>
									<option value="Pending" <?php if(isset($status) && $status== 'Pending'){ echo 'selected'; }?>><?php echo mlx_get_lang('Pending'); ?></option>
									<option value="Cancel" <?php if(isset($status) && $status== 'Cancel'){ echo 'selected'; }?>><?php echo mlx_get_lang('Cancel'); ?></option>
								</select>
								<?php } ?>
								<h3 class="box-title"><?php echo $status; ?></h3>
								</div>	
							</div>
							<div class="box-footer">
								<?php if(!empty($status) && $status != 'Completed'){?>
								<button name="submit" type="submit" class="btn btn-<?php echo $myHelpers->global_lib->get_skin_class(); ?> pull-right" id="save_publish"><?php echo mlx_get_lang('Save Publish'); ?></button>
								<?php } ?>
							</div>
						</div>
						</div>
						
					</div> 

				</div>				
			</form>
        </section>
      </div>