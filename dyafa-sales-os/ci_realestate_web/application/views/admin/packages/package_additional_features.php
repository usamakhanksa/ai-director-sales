<?php


	/*print_r($package_features);*/
	
	if( isset($current_package_features)  &&  $current_package_features->num_rows() > 0)
	{
		foreach($current_package_features->result() as $prop_row)
		{
			${$prop_row->feature_type} = $prop_row->feature_value;
			${$prop_row->feature_for} = 1;
		}
	}
	
	if(count($package_features) > 0 ){
		foreach($package_features as $pfkey => $pck_features){
			
?>
	<div class="form-group">
	<input type="checkbox" id="<?php echo $pfkey;	?>" name="feature[<?php echo $pfkey;	?>][enable]" 
		<?php if(isset($$pfkey)) echo ' checked="checked" '; ?>
		value="1" 
		class="minimal child_show_hide" data-child="<?php echo $pfkey;	?>_area" /> &nbsp;
	<label for="<?php echo $pfkey;	?>"> <?php echo mlx_get_lang($pck_features['package_title']); ?> </label>
	</div>

	<div class="child-form-group <?php echo $pfkey;	?>_area <?php if(!isset($$pfkey)) echo ' hide_child_form '; ?>" >
	
	<?php if(isset($pck_features['features'])){
		
			foreach($pck_features['features'] as $feature){	
				$feat = $feature['feature_type'];
	?>
	
	<div class="form-group">
		<label for="<?php echo $feature['feature_type'];	?>"><?php echo mlx_get_lang( $feature['feature_title']); ?>  </label>   
		<input type="number" class="form-control " min="0" 
				id="<?php echo $feature['feature_type'];	?>" 
				name="feature[<?php echo $pfkey;	?>][<?php echo $feature['feature_type'];	?>]" 
				value="<?php if(isset($$feat) ) echo $$feat; ?>"
				/>

	</div>
			<?php }	
			} ?>
	
	
	</div>

		<?php	}
		
	}?>