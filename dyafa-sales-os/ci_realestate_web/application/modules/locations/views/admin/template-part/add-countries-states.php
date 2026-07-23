<?php 

$locations = json_decode($locations, true);
$loc_tax_settings = json_decode($loc_tax_settings,true);
		
if(isset($locations['countries']) && count($locations['countries']) > 0 ){
	$countries = $locations['countries'];
?>	
<div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?>" id="country-states">
	<div class="box-header with-border">
		<h3 class="box-title"><?php echo mlx_get_lang('Add States to Countries'); ?></h3>
	</div>
	<div class="box-body">
		
		<?php 
		$attributes = array('name' => 'add_form_post','class' => 'form');		 			
		echo form_open_multipart('',$attributes); ?>
		<input type="hidden" name="user_id" class="user_id" value="<?php echo $this->session->userdata('user_id'); ?>">
		
			<div class="form-group">
			  <label for="country"><?php echo mlx_get_lang('Select Country'); ?> <span class="required">*</span></label>
			  <select name="cnt_country" class="form-control country select2_elem" required="required"  id="cnt-country" >
			  <option value="select"><?php echo mlx_get_lang('Select Country'); ?></option> 
			  <?php
			  foreach($countries as $key =>$country){
					echo '<option value="'.$key.'">'.mlx_get_lang($country['loc_title']).'</option>';	
				}
			  ?>
			  </select>
			  
			  
			</div>
			
			<div class="form-group">
			  <label for="state"><?php echo mlx_get_lang('Select States'); ?> <span class="required">*</span></label>
			  <select name="cnt_state[]" multiple class="form-control no_clean select2_elem" required  id="cnt-states" >
			  <!--<option value="select">Select States</option> -->
			  <?php
			  /*foreach($countries as $country){
					echo '<option value="'.$country['id'].'">'.$country['name'].'</option>';	
				}*/
			  ?>
			  </select>
			  
			  
			</div>
		
			<button name="add_country_state" type="submit" class="btn btn-<?php echo $myHelpers->global_lib->get_skin_class(); ?> " id="add_country_state"><?php echo mlx_get_lang('Add'); ?></button>
		</form>
	
	</div>
	<div class="overlay" style="display:none;">
		<i class="fa fa-spinner fa-spin"></i>
	</div>
</div>
<?php } ?>
<script>

jQuery(document).ready(function($){
		
	var base_url = '<?php echo base_url();?>';	
	

	$('.country').on('change',function() {
		var thiss = $(this);
		$('#country-states').find('.overlay').show();
		var country =  thiss.val();
		thiss.parents('#country-states').find('#cnt-states').html('');
		$.ajax({						
			url: base_url+'admin/ajax_locations/get_states_from_countries',						
			type: 'POST',						
			success: function (res) 
			{		
				if(res != 'state not found')
					thiss.parents('#country-states').find('#cnt-states').html(res);
				$('#country-states').find('.overlay').hide();	
			},						
			data: {	country : country},						
			cache: false					
		});	
		return false;
	});
	
	$('#addStateModal').on('hidden.bs.modal', function (e) {
	  $(this)
		.find("input,textarea,select")
		   .val('')
		   .end()
		.find("input[type=checkbox], input[type=radio]")
		   .prop("checked", "")
		   .end();
		
		$(this).find('.overlay').show();
	});
	
	$('#addStateModal').on('shown.bs.modal', function (e) {
		$('#addStateModal .overlay').show();
		var thiss = $(this);
		var $invoker = $(e.relatedTarget);
		var country_code = $invoker.attr('data-country_code');
		var country_name = $invoker.attr('data-country_name');
		
		$('#addStateModal').find('#cs-country_title').val(country_name);
		$('#addStateModal').find('#cs-country_id').val(country_code);
		
		$.ajax({						
			url: base_url+'admin/ajax_locations/generate_state_id_callback_func',						
			type: 'POST',						
			success: function (res) 
			{		
				$('#addStateModal').find('#cs-state_id').val(res.state_id);
				$('#addStateModal .overlay').hide();
			},						
			data: {	},						
			cache: false					
		});
		
	});
	
	$('.add_state_form').submit(function() {
		
		$('#addStateModal .overlay').show();
		var thiss = $(this);
		$.ajax({						
			url: base_url+'admin/ajax_locations/add_custom_state_callback_func',						
			type: 'POST',						
			success: function (res) 
			{		
				$('#addStateModal').modal('hide');
				window.location.reload();
			},						
			data: thiss.serialize(),						
			cache: false					
		});
		return false;
	});
		
});

</script>


				