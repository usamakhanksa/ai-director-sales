<?php

		$CI = &get_instance();
		
		
		
		$app_menu_static_pages = apply_filters("app_menu_static_pages_append_menu_items");
		
?>
<div class="box box-<?php echo get_skin_class(); ?> collapsed-box">
	<div class="box-header with-border">
	  <h3 class="box-title"><?php echo mlx_get_lang('Static Pages'); ?></h3>
	  <div class="box-tools pull-right">
		<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
	  </div>
	</div>
	<input type="hidden" class="menu_type" value="static">
	<div class="box-body menu-option-list">
		
		<?php
				if(count($app_menu_static_pages) > 0){
					
					foreach($app_menu_static_pages as  $key => $static_page){
		?>
		<div class="checkbox">
			<label >
			  <input type="checkbox" class="minimal no-validate" value="static~<?php echo $static_page['keyword']; ?>" 
			  data-title="<?php echo $static_page['title']; ?>">
			  &nbsp; <?php echo mlx_get_lang($static_page['title']); ?>
			</label>
		</div>
		<?php
					}
				}
		?>
		
	</div>
	 <div class="box-footer">
		<button name="submit" type="button" class="btn btn-default pull-right add_to_menu" ><?php echo mlx_get_lang('Add to Menu'); ?></button>
	  </div>
</div>