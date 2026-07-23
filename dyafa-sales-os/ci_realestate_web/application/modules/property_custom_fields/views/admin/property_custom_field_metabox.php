<div class="box box-<?php echo $CI->global_lib->get_skin_class(); ?>">
	<div class="box-header with-border">
	  <h3 class="box-title"><?php echo mlx_get_lang('Custom Fields'); ?></h3>
	  <div class="box-tools pull-right">
		<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	  </div>
	</div>
	<div class="box-body">
		
		<?php 
		$n=0;
		
		foreach($custom_field_list as $cfk=>$cfv)
		{
			$n++;
			$hasChecked = '';
			$curValue = '';
		?>
			<div class="form-group">
			  <label for="custom_field_<?php echo $n; ?>"><?php echo mlx_get_lang($cfv['title']); ?> </label>
			  <input type="text" class="form-control" 
					name="custom_fields[<?php echo $cfv['slug']; ?>]" 
					id="custom_field_<?php echo $n; ?>">
			</div>
			
		<?php } ?>
				
	</div>
</div>