<?php 

	global $property;
?>


<div class="box box-<?php echo get_skin_class(); ?>">
	<div class="box-header with-border">
		<h3 class="box-title"><?php echo mlx_get_lang('Other Details'); ?></h3>
		<div class="box-tools pull-right">
			<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>
	<div class="box-body">


		<div class="form-group">
			<label for="property_type_status"><?php echo mlx_get_lang('Property For'); ?> <span class="required">*</span></label>

			<div class="radio_toggle_wrapper ">
				<input type="radio" id="property_for_sale" value="sale" 
					name="property_for" 
					<?php
					if ((isset($property->property_for) && strtolower($property->property_for) == 'sale') 
						|| !isset($property->property_for) 
						|| (isset($property->property_for) && $property->property_for == ''))
						echo ' checked="checked" ';
					?> class="toggle-radio-button">
				<label for="property_for_sale"><?php echo mlx_get_lang('Sale'); ?></label>

				<input type="radio" id="property_for_rent" value="rent" 
					name="property_for" class="toggle-radio-button" 
					<?php
					if (isset($property->property_for) && strtolower($property->property_for) == 'rent')
						echo ' checked="checked" ';
					?>>
				<label for="property_for_rent"><?php echo mlx_get_lang('Rent'); ?></label>
			</div>
		</div>

		<?php

		$user_type = $this->session->userdata('user_type');
		if ($user_type == 'admin') {
			
			
		?>
			<div class="row">
				<div class="form-group col-md-6">
					<label for="user_id"><?php echo mlx_get_lang('Property Added By'); ?> <span class="required">*</span></label>

					<select class="form-control select2_elem" name="user_id" id="user_id" required>
						<option value=""><?php echo mlx_get_lang('Select Any User'); ?></option>
						<?php
						if (isset($user_list) && $user_list->num_rows() > 0) {
							foreach ($user_list->result() as $u_row) {
								$first_name = get_user_meta($u_row->user_id, 'first_name');
								$last_name = get_user_meta($u_row->user_id, 'last_name');

								if (!empty($last_name))
									$first_name .= ' ' . $last_name;
								echo '<option value="' . EncryptClientID($u_row->user_id) . '"';
								if (isset($property->created_by) && $property->created_by == $u_row->user_id)
									echo ' selected="selected" ';
								echo '>' . ucfirst($first_name) . ' (' . ucfirst($u_row->user_type) . ')</option>';
							}
						}
						?>
					</select>
				</div>
			</div>
		<?php
		} else {

			echo '<input type="hidden" name="user_id" class="user_id" value="' . EncryptClientID($this->session->userdata("user_id")) . '">';
		}
		?>
	

		<div class="form-group">
			<label class="control-label"><?php echo mlx_get_lang('Property Type'); ?> <span class="required">*</span></label>

			<?php
			if (isset($property_types) && $property_types->num_rows() > 0) { ?>
				<div class="radio_toggle_wrapper ">
					<?php
					foreach ($property_types->result() as $row) {
					?>
						<input type="radio" <?php
											if (isset($property->property_type) && $property->property_type == $row->pt_id)
												echo ' checked="checked" ';
											?> 
											id="property_type_<?php echo EncryptClientID($row->pt_id); ?>" 
											value="<?php echo 	EncryptClientID($row->pt_id); ?>" 
											data-slug="<?php echo 	($row->slug); ?>" 
											name="property_type" class="toggle-radio-button">
						<label for="property_type_<?php echo EncryptClientID($row->pt_id); ?>"><?php echo ucfirst($row->title); ?></label>
					<?php } ?>
				</div>
			<?php } else { ?>
				<p class="no-margin"><?php echo mlx_get_lang('Property Type Not Available Now'); ?></p>
			<?php } ?>
		</div>

	</div>
		
		
</div>