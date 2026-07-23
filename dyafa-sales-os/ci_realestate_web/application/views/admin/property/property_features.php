<?php 
		/*
		echo '<pre>';
		print_r($property_type_features);
		echo '</pre>';
		*/
	global $property;
?>


<div class="box box-<?php echo get_skin_class(); ?>">
					<div class="box-header with-border">
						<h3 class="box-title"><?php echo mlx_get_lang('Features'); ?></h3>
						<div class="box-tools pull-right">
							<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						</div>
					</div>
					<!--
				<span class="required">*</span>
				required="required"
				-->
					<div class="box-body">
						
						<?php 
						/*****	This is for Size	*****/
							$pt_class = "";
							foreach($property_type_features as $p_type => $pt_features){
								if(in_array("size" , $pt_features)){
									$pt_class .= " property_type_$p_type";	
								}
							}
							
							$size = $size_measure = "";
							if (isset($property->size) && !empty($property->size)){
									
								$s_exp = explode('~', $property->size);
								$size = $s_exp[0];
								$size_measure = (isset($s_exp[1])) ? $s_exp[1] : 'Sq Feet';
							}
							
						?>
						
						<div class="form-group property_feature <?php echo $pt_class; ?>">
							<label for="size"><?php echo mlx_get_lang('Size'); ?> </label>

							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-expand"></i>
								</span>
								<input type="text" class="form-control" name="size" id="size" 
								value="<?php  echo $size; ?>">

								<input type="hidden" class="form-control" 
								value="<?php if ( !empty($size_measure)) echo $size_measure;
												else echo mlx_get_lang('Sq Feet'); ?>" name="size_measure" id="size_measure">
								<div class="input-group-btn">
									<button type="button" class="btn btn-default dropdown-toggle size_measure" data-toggle="dropdown" aria-expanded="false">


										<?php if ( !empty($size_measure)) echo $size_measure;
										else echo mlx_get_lang('Sq Feet'); ?>&nbsp;&nbsp;<span class="fa fa-caret-down"></span></button>
									<ul class="dropdown-menu size_measure_menus">
										<li><a data-val="<?php echo mlx_get_lang('Sq Feet'); ?>"><?php echo mlx_get_lang('Sq Feet'); ?></a></li>
										<li><a data-val="<?php echo mlx_get_lang('Sq Meter'); ?>"><?php echo mlx_get_lang('Sq Meter'); ?></a></li>
										<?php
										if (isset($size_units) && !empty($size_units)) {
											foreach ($size_units as $suv) {
										?>
												<li><a data-val="<?php echo mlx_get_lang($suv); ?>"><?php echo mlx_get_lang($suv); ?></a></li>
										<?php
											}
										}
										?>
									</ul>

								</div>

							</div>
						</div>
						
						
						<?php 
							/*****	This is for Width	*****/
							$pt_class = "";
							foreach($property_type_features as $p_type => $pt_features){
								if(in_array("width" , $pt_features)){
									$pt_class .= " property_type_$p_type";	
								}
							}
							
							$pt_width = $width = $width_measure = "";
							if (isset($property->p_id) && !empty($property->p_id)){
								$pt_width = get_property_meta($property->p_id, "width");
							}
							if (!empty($pt_width)){
									
								$s_exp = explode('~', $pt_width);
								$width = $s_exp[0];
								$width_measure = (isset($s_exp[1])) ? $s_exp[1] : 'Feet';
							}
							
						?>
						
						<div class="form-group property_feature <?php echo $pt_class; ?>">
							<label for="pt_width"><?php echo mlx_get_lang('Width'); ?> </label>

							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-expand"></i>
								</span>
								<input type="text" class="form-control" name="property_meta[width]" id="pt_width" 
									value="<?php if (isset($width) && !empty($width)) echo $width; ?>">

								<input type="hidden" class="form-control" 
									value="<?php if (isset($width_measure) && !empty($width_measure)) 
													echo $width_measure;
												 else echo mlx_get_lang('Feet'); ?>" name="width_measure" id="width_measure">
								<div class="input-group-btn">
									<button type="button" class="btn btn-default dropdown-toggle width_measure" 
										data-toggle="dropdown" aria-expanded="false">


										<?php if (isset($width_measure) && !empty($width_measure)) echo $width_measure;
										else echo mlx_get_lang('Feet'); ?>&nbsp;&nbsp;<span class="fa fa-caret-down"></span></button>
									<ul class="dropdown-menu width_measure_menus">
										<li><a data-val="<?php echo mlx_get_lang('Feet'); ?>"><?php echo mlx_get_lang('Feet'); ?></a></li>
										<li><a data-val="<?php echo mlx_get_lang('Meter'); ?>"><?php echo mlx_get_lang('Meter'); ?></a></li>
										<?php
										if (isset($size_units) && !empty($size_units)) {
											foreach ($size_units as $suv) {
										?>
												<li><a data-val="<?php echo mlx_get_lang($suv); ?>"><?php echo mlx_get_lang($suv); ?></a></li>
										<?php
											}
										}
										?>
									</ul>

								</div>

							</div>
						</div>
						
						
						
						
						<?php 
							/*****	This is for Height	*****/
							$pt_class = "";
							foreach($property_type_features as $p_type => $pt_features){
								if(in_array("height" , $pt_features)){
									$pt_class .= " property_type_$p_type";	
								}
							}
							
							$pt_height = $height = $height_measure = "";
							if (isset($property->p_id) && !empty($property->p_id)){
								$pt_height = get_property_meta($property->p_id, "height");
							}
							if (!empty($pt_height)){
									
								$s_exp = explode('~', $pt_height);
								$height = $s_exp[0];
								$height_measure = (isset($s_exp[1])) ? $s_exp[1] : 'Feet';
							}
							
						?>
						
						<div class="form-group property_feature <?php echo $pt_class; ?>">
							<label for="pt_height"><?php echo mlx_get_lang('Height'); ?> </label>

							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-expand"></i>
								</span>
								<input type="text" class="form-control" name="property_meta[height]" id="pt_height" 
									value="<?php if (isset($height) && !empty($height)) echo $height; ?>">

								<input type="hidden" class="form-control" 
									value="<?php if (isset($height_measure) && !empty($height_measure)) 
													echo $height_measure;
												 else echo mlx_get_lang('Feet'); ?>" name="height_measure" id="height_measure">
								<div class="input-group-btn">
									<button type="button" class="btn btn-default dropdown-toggle height_measure" 
										data-toggle="dropdown" aria-expanded="false">


										<?php if (isset($height_measure) && !empty($height_measure)) echo $height_measure;
										else echo mlx_get_lang('Feet'); ?>&nbsp;&nbsp;<span class="fa fa-caret-down"></span></button>
									<ul class="dropdown-menu height_measure_menus">
										<li><a data-val="<?php echo mlx_get_lang('Feet'); ?>"><?php echo mlx_get_lang('Feet'); ?></a></li>
										<li><a data-val="<?php echo mlx_get_lang('Meter'); ?>"><?php echo mlx_get_lang('Meter'); ?></a></li>
										<?php
										if (isset($size_units) && !empty($size_units)) {
											foreach ($size_units as $suv) {
										?>
												<li><a data-val="<?php echo mlx_get_lang($suv); ?>"><?php echo mlx_get_lang($suv); ?></a></li>
										<?php
											}
										}
										?>
									</ul>

								</div>

							</div>
						</div>
						
						
						
						
						
						
						<?php 
							/*****	This is for Length	*****/
							$pt_class = "";
							foreach($property_type_features as $p_type => $pt_features){
								if(in_array("length" , $pt_features)){
									$pt_class .= " property_type_$p_type";	
								}
							}
							
							$pt_length = $length = $length_measure = "";
							if (isset($property->p_id) && !empty($property->p_id)){
								$pt_length = get_property_meta($property->p_id, "length");
							}
							if (!empty($pt_length)){
									
								$s_exp = explode('~', $pt_length);
								$length = $s_exp[0];
								$length_measure = (isset($s_exp[1])) ? $s_exp[1] : 'Feet';
							}
							
						?>
						
						<div class="form-group property_feature <?php echo $pt_class; ?>">
							<label for="pt_length"><?php echo mlx_get_lang('Length'); ?> </label>

							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-expand"></i>
								</span>
								<input type="text" class="form-control" name="property_meta[length]" id="pt_length" 
									value="<?php if (isset($length) && !empty($length)) echo $length; ?>">

								<input type="hidden" class="form-control" 
									value="<?php if (isset($length_measure) && !empty($length_measure)) 
													echo $length_measure;
												 else echo mlx_get_lang('Feet'); ?>" name="length_measure" id="length_measure">
								<div class="input-group-btn">
									<button type="button" class="btn btn-default dropdown-toggle length_measure" 
										data-toggle="dropdown" aria-expanded="false">


										<?php if (isset($length_measure) && !empty($length_measure)) echo $length_measure;
										else echo mlx_get_lang('Feet'); ?>&nbsp;&nbsp;<span class="fa fa-caret-down"></span></button>
									<ul class="dropdown-menu length_measure_menus">
										<li><a data-val="<?php echo mlx_get_lang('Feet'); ?>"><?php echo mlx_get_lang('Feet'); ?></a></li>
										<li><a data-val="<?php echo mlx_get_lang('Meter'); ?>"><?php echo mlx_get_lang('Meter'); ?></a></li>
										<?php
										if (isset($size_units) && !empty($size_units)) {
											foreach ($size_units as $suv) {
										?>
												<li><a data-val="<?php echo mlx_get_lang($suv); ?>"><?php echo mlx_get_lang($suv); ?></a></li>
										<?php
											}
										}
										?>
									</ul>

								</div>

							</div>
						</div>
						


						<?php 
							$pt_class = "";
							foreach($property_type_features as $p_type => $pt_features){
								if(in_array("bedroom" , $pt_features)){
									$pt_class .= " property_type_$p_type";	
								}
							}
						?>
						<div class="form-group property_feature <?php echo $pt_class; ?>">
							<label for="bedroom"><?php echo mlx_get_lang('Bedroom'); ?> </label>
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-bed"></i>
								</span>
								<input type="text" value="<?php if (isset($bedroom) && !empty($bedroom)) echo $bedroom;
															else echo '0'; ?>" class="form-control" name="bedroom" id="bedroom">
							</div>
						</div>
						
						<?php 
							$pt_class = "";
							foreach($property_type_features as $p_type => $pt_features){
								if(in_array("bathroom" , $pt_features)){
									$pt_class .= " property_type_$p_type";	
								}
							}
						?>

						<div class="form-group property_feature <?php echo $pt_class; ?>">
							<label for="bathroom"><?php echo mlx_get_lang('Bathroom'); ?> </label>
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-bathtub"></i>
								</span>
								<input type="text" value="<?php if (isset($bathroom) && !empty($bathroom)) echo $bathroom;
															else echo '0'; ?>" class="form-control" name="bathroom" id="bathroom">
							</div>
						</div>
						
						<?php 
							$pt_class = "";
							foreach($property_type_features as $p_type => $pt_features){
								if(in_array("garage" , $pt_features)){
									$pt_class .= " property_type_$p_type";	
								}
							}
						?>


						<div class="form-group property_feature <?php echo $pt_class; ?>">
							<label for="garage"><?php echo mlx_get_lang('Garages'); ?> </label>
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-car"></i>
								</span>
								<input type="text" value="<?php if (isset($garage) && !empty($garage)) echo $garage;
															else echo '0'; ?>" class="form-control" name="garage" id="garage">
							</div>
						</div>
						
						<?php do_action("cms_admin_property_edit_additional_features" , $property); ?>
						
						
						
						
					</div>
				</div>