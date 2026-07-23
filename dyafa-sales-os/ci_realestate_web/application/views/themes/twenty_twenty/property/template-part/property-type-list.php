<?php

	/*$type = $myHelpers->menu_lib->get_url('type='.mlx_get_norm_string(strtolower($prop_row->title)));*/
	
	
	$prop_type_slug = $prop_row->slug;
	$type = $myHelpers->menu_lib->get_url('type='.mlx_get_norm_string($prop_type_slug));
	
?><div class="single-property-type-block">
	<a href="<?php echo $type; ?>">
	<?php 
		if(isset($prop_row->img_url) && !empty($prop_row->img_url) && file_exists('uploads/prop_type/'.$prop_row->img_url))
		{
			$post_image_url = base_url().'uploads/prop_type/'.$prop_row->img_url;
			
			echo '<img src="'.$post_image_url.'" class="img-fluid">';
			
		}
		else
		{
			$post_image_url = base_url().'application/views/'.$theme.'/assets/images/no-property-image.jpg';
			echo '<img src="'.$post_image_url.'" class="img-fluid">';
		}
		?>
  </a>
  
  <div class="pt-title text-center">
		<a href="<?php echo $type; ?>"><?php echo mlx_get_lang(ucfirst($prop_row->title)); ?></a>
  </div>
</div>






