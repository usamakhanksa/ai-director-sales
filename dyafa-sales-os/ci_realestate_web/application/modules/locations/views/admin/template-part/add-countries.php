<?php 

$countries = cms_file_get_contents(base_url("locations/json/countries.json"));	
$countries = json_decode($countries, true);


?>	
<div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?>">
	<div class="box-header with-border">
		<h3 class="box-title"><?php echo mlx_get_lang('Add Countries'); ?></h3>
	</div>
	<div class="box-body">
		<?php 
		$attributes = array('name' => 'add_form_post','class' => 'form');		 			
		echo form_open_multipart('',$attributes); ?>
		<input type="hidden" name="user_id" class="user_id" value="<?php echo $this->session->userdata('user_id'); ?>">
		
			<div class="form-group">
			  <label for="country"><?php echo mlx_get_lang('Select Countries'); ?> <span class="required">*</span></label>
			  <select name="country[]" multiple class="form-control no_clean select2_elem"  required id="country" >
			  <option value=""><?php echo mlx_get_lang('Select Countries'); ?></option> 
			  <?php
			  foreach($countries as $country){
					echo '<option value="'.$country['countryCode'].'~'.$country['countryName'].'~'.$country['geonameId'].'">'.mlx_get_lang($country['countryName']).'</option>';	
				}
			  ?>
			  </select>
			</div>
			<button name="add_country" type="submit" class="btn btn-<?php echo $myHelpers->global_lib->get_skin_class(); ?>" id="add_country"><?php echo mlx_get_lang('Add'); ?></button>
		</form>
	</div>
</div>
		
				