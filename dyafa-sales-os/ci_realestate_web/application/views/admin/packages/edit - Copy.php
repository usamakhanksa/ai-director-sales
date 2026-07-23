<?php
	$site_users = $myHelpers->config->item("site_users");
	if(isset($blog_meta) && $blog_meta->num_rows() > 0)
	{
		
		$row = $blog_meta->row();
		
		$p_id = $row->package_id;
		$title = $row->package_name;
		$price = $row->package_price;
		$package_currency = $row->package_currency;
		$package_life = $row->package_life;
		$applicable_for = $row->applicable_for;
		if(!empty($applicable_for))
			$applicable_for_array = explode(',',$applicable_for);
		$purchase_limit  = $row->purchase_limit ;
		$purchase_button_text = $row->purchase_button_text;
		$package_order = $row->package_order;
		// $is_default = $row->is_default;
	}
	
?>
<?php 
$enable_subscription = get_option('enable_subscription');
$enable_property_posting = get_option('enable_property_posting');
$enable_featured_property_posting = get_option('enable_featured_property_posting');
$enable_blog_posting = get_option('enable_blog_posting');
?>

   
      <div class="content-wrapper">
        <section class="content-header">
          <h1 class="page-title"><i class="fa fa-edit"></i> <?php echo mlx_get_lang('Edit Package'); ?> </h1>
          <?php echo validation_errors(); ?>
		  <?php 
		  if($enable_subscription != 'Y' && $enable_property_posting != 'Y' && $enable_featured_property_posting != 'Y' && $enable_blog_posting != 'Y')
		 {
		 ?>
			<div class="alert alert-warning alert-dismissable show_always" style="margin-top:10px; margin-bottom:0px;">
				<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
				<?php echo mlx_get_lang('Please choose either subscription or property posting or blog posting from Settings -> Payment Settings.'); ?>
			</div>
		 <?php 
		 }
		  ?>
        </section>
		<section class="content">
             <?php  $attributes = array('name' => 'add_form_post','class' => 'form');		 			
			echo form_open_multipart('admin/packages/edit',$attributes); ?>
			<input type="hidden" name="b_id" class="b_id" value="<?php echo $myHelpers->EncryptClientId($p_id); ?>">
			
			<div class="row">
				<div class="col-md-8">   
					<div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?>">
						<div class="box-header with-border">
							<h3 class="box-title"><?php echo mlx_get_lang('Package Details'); ?></h3>
							<div class="box-tools pull-right">
								<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
								
							</div>
						</div>
					
						<div class="box-body">
										
							<div class="form-group">
							<label for="package"><?php echo mlx_get_lang('Package Name'); ?> <span class="text-red">*</span></label>
							<input type="text" class="form-control" required="required" name="package" id="package" 
							value="<?php if(isset($title)){ echo $title; } ?>">
							</div>
							
							<div class="form-group">
							<label for="packages_price"><?php echo mlx_get_lang('Package Price'); ?><span class="text-red">*</span></label>

							<input type="number" class="form-control"  id="packages_price" name="packages_price" 
							value="<?php if(isset($price)){ echo $price; } ?>">
							</div>
							
							<div class="form-group">
						<label for="currency_code"><?php echo mlx_get_lang('Currency'); ?> <span class="required">*</span></label>
						
						<select class="form-control select2_elem" name="currency_code" id="currency_code" required>
							<option value=""><?php echo mlx_get_lang('Select Currency'); ?></option>
							<?php if(isset($currency_symbols) && !empty($currency_symbols)) {
									foreach($currency_symbols as $k=>$v)
									{
										
										echo '<option value="'.$k.'"';
										if(isset($package_currency) && $package_currency == $k )
											echo ' selected="selected" ';
										echo '>'.$k.' - '.$v.'</option>';
									}
								}
								?>
						</select>
						</div>	
						<?php if($enable_subscription == 'Y') { ?>
							<div class="form-group">
								<?php 
								$sub_result = $this->Common_model->commonQuery("select feature_type,feature_value from package_features where package_id = $p_id and feature_for='subscription' ");	
								//var_dump($sub_result->result());exit;
								if($sub_result->num_rows() > 0)
								{
									$sub_row = $sub_result->row();
								}
								
								?>

							 <input type="checkbox" id="is_subscription" name="feature[is_subscription][enable]" value="1" class="minimal" 
								<?php if($sub_result->num_rows() > 0) echo ' checked="checked" '; ?>
							 /> &nbsp;
								<label for="is_subscription"> <?php echo mlx_get_lang('Is Subscription'); ?></label>
							</div>

							<div class="form-group child-form-group" id="subscription_area" <?php if($sub_result->num_rows() > 0) echo ' style="display:block;" '; ?>>
									<label for="size"><?php echo mlx_get_lang('Validity'); ?> </label>
									<div class="input-group">
								<?php
									
									$prop_result = $this->Common_model->commonQuery("select feature_type,feature_value from package_features where package_id = $p_id and feature_for = 'property'");	
										$package_life_const = "Days";
										$package_life_num = "0";
										$subscription_package_life = "0 days";
									if(isset($sub_row) )
									{
										//&& isset($sub_row->feature_value)
										//$package_life = $sub_row->feature_value;
										if(preg_match("/daily/", $sub_row->feature_type))
										{
											$package_life_const = "Days";
											$package_life_num = $sub_row->feature_value;
										}
										if(preg_match("/weekly/", $sub_row->feature_type))
										{
											$package_life_const = "Weeks";
											$package_life_num = $sub_row->feature_value;
										}	
										if(preg_match("/monthly/", $sub_row->feature_type))
										{
											$package_life_const = "Months";
											$package_life_num = $sub_row->feature_value;
										}
										if(preg_match("/yearly/", $sub_row->feature_type))
										{
											$package_life_const = "Years";
											$package_life_num = $sub_row->feature_value;
										}
										$subscription_package_life = $package_life_num." ".strtolower($package_life_const);
										
									}
									
									
									

									?>		
												<input type="number" class="form-control" min="0" name="" id="sub_validity" 
												value="<?php  echo $package_life_num; ?>">
												<input type="hidden" class="form-control" 
												value="<?php  echo $subscription_package_life;  ?>" 
												name="feature[is_subscription][subscription_validity]" id="sub_val_type">
												
												<div class="input-group-btn">
												  <button type="button" class="btn btn-default dropdown-toggle sub_val_type" data-toggle="dropdown" 
												  data-btn-val="<?php echo strtolower($package_life_const); ?>"
												  aria-expanded="false"><?php  echo mlx_get_lang($package_life_const); ?>&nbsp;&nbsp;<span class="fa fa-caret-down"></span></button>
												  <ul class="dropdown-menu is_subscription_menus">
													<li><a data-val="<?php echo "days"; ?>"><?php echo mlx_get_lang('Days'); ?></a></li>
													<li><a data-val="<?php echo 'weeks'; ?>"><?php echo mlx_get_lang('Weeks'); ?></a></li>
													<li><a data-val="<?php echo 'months'; ?>"><?php echo mlx_get_lang('Months'); ?></a></li>
													<li><a data-val="<?php echo 'year'; ?>"><?php echo mlx_get_lang('Year'); ?></a></li>
												  </ul>
												</div>
											</div>
										</div>
										<?php } ?>
										
										<?php if($enable_property_posting == 'Y' || $enable_featured_property_posting == 'Y') { ?>										
											<div class="form-group">
												
												<input type="checkbox" id="is_property" name="feature[property][enable]" value="1" class="minimal" 
												<?php if($prop_result->num_rows() > 0) echo ' checked="checked" '; ?> /> &nbsp;
												<label for="is_property"> <?php echo mlx_get_lang('Provide Property Posting'); ?></label>
											</div>
											
											<div class=" child-form-group" id="property_area" <?php if($prop_result->num_rows() > 0) echo ' style="display:block;" '; ?>>
												<?php 
												if($prop_result->num_rows() > 0)
												{
													foreach($prop_result->result() as $prop_row)
													{
														${$prop_row->feature_type} = $prop_row->feature_value;
													}
												}
												
												?>
												<?php if($enable_property_posting == 'Y') { ?>
												<div class="form-group">
													<label for="properties"><?php echo mlx_get_lang('Number Of Properties'); ?></label>
													<input type="number" class="form-control " min="0" id="properties" name="feature[property][post_property]" 
													value="<?php if(isset($post_property) && !empty($post_property)) echo $post_property; ?>"/>
											
												</div>
												<?php } ?>
												<?php if($enable_featured_property_posting == 'Y') { ?>
												<div class="form-group">
													<label for="featured_properties"><?php echo mlx_get_lang('Number Of Feature Properties'); ?></label>
													<input type="number" class="form-control " min="0"  id="featured_properties" name="feature[property][featured_property]"
													value="<?php if(isset($featured_property) && !empty($featured_property)) echo $featured_property; ?>"/>
												</div>
												<?php } ?>
												
											</div>
											<?php } ?>
											
											<?php	do_action("package_additional_features");    ?>
			
											<?php if($enable_blog_posting == 'Y') { ?>
											<div class="form-group">
												<?php 
												$blog_result = $this->Common_model->commonQuery("select feature_type,feature_value from package_features where package_id = $p_id and feature_for = 'blog'");	
												if($blog_result->num_rows() > 0)
												{
													$blog_row = $blog_result->row();
												}
												?>
												<input type="checkbox" id="is_blog" name="feature[blog][enable]" value="1" class="minimal" <?php if($blog_result->num_rows() > 0) echo ' checked="checked" '; ?>/> &nbsp;
												<label for="is_blog"> <?php echo mlx_get_lang('Provide Blog Posting'); ?></label>
											</div>
											
											<div class="form-group child-form-group" id="blog_area" <?php if($blog_result->num_rows() > 0) echo ' style="display:block;" '; ?>>
												<label for="blog"><?php echo mlx_get_lang('Number Of Blog'); ?></label>
												<input type="number" class="form-control "  id="blog" name="feature[blog][post_blog]" 
												value="<?php if(isset($blog_row) && isset($blog_row->feature_value)) echo $blog_row->feature_value; ?>"/>
											</div>
											<?php } ?>
											
											<div class="form-group">
												<label for="applicable_for"><?php echo mlx_get_lang('Applicable For'); ?><span class="text-red">*</span></label>
												<br/>
												  
												<?php 
												
												foreach($site_users as $k => $user){ if($k == 'admin') continue; ?>
													<label for="<?php echo 'user_for'.$k; ?>">
														<input type="checkbox" id="<?php echo 'user_for_'.$k; ?>" class="minimal"  name="user_types[]"  value="<?php echo $k; ?>" 
														<?php if(isset($applicable_for_array) && in_array($k,$applicable_for_array)) echo ' checked="checked" '; ?>/>&nbsp;
															<?php echo ucfirst($user['title']); ?> &nbsp;&nbsp;		
													</label>		
												<?php } ?>
											</div>
											
							<div class="form-group">
								<label for="package_lifetime"><?php echo mlx_get_lang('Package Expire After Purchase'); ?> <span class="text-red">*</span></label></label>
								<!-- <input type="number" class="form-control"  id="package_lifetime" name="package_lifetime" value="0"> -->
								
								<?php
								
								
								
								$package_life_const = "Days";
								$package_life_num = "0";
								if(preg_match("/days/", $package_life))
								{
									$package_life_const = "Days";
									$package_life_num = trim(str_replace("days","",$package_life));
								}
								if(preg_match("/weeks/", $package_life))
								{
									$package_life_const = "Weeks";
									$package_life_num = trim(str_replace("weeks","",$package_life));
								}
								if(preg_match("/months/", $package_life))
								{
									$package_life_const = "Months";
									$package_life_num = trim(str_replace("months","",$package_life));
								}
								if(preg_match("/year/", $package_life))
								{
									$package_life_const = "Year";
									$package_life_num = trim(str_replace("year","",$package_life));
								}

								?>
								<div class="input-group">
										
									<input type="number" class="form-control" min="0" name=""  id="package_lifetime" name="" 
									value="<?php  echo $package_life_num ; ?>">
								
									<input type="hidden" class="form-control" 
									name="package_lifetime" id="package_lifetime_val" value="<?php  echo $package_life ; ?>">
									
									<div class="input-group-btn">
									<button type="button" class="btn btn-default dropdown-toggle package_lifetime_val" 
									data-toggle="dropdown" data-btn-val="<?php echo strtolower($package_life_const); ?>"
									aria-expanded="false"
									><?php echo mlx_get_lang($package_life_const); ?>&nbsp;&nbsp;<span class="fa fa-caret-down"></span></button>
									<ul class="dropdown-menu package_life_menus">
									<li><a data-val="<?php echo "days"; ?>"><?php echo mlx_get_lang('Days'); ?></a></li>
									<li><a data-val="<?php echo 'weeks'; ?>"><?php echo mlx_get_lang('Weeks'); ?></a></li>
									<li><a data-val="<?php echo 'months'; ?>"><?php echo mlx_get_lang('Months'); ?></a></li>
									<li><a data-val="<?php echo 'year'; ?>"><?php echo mlx_get_lang('Year'); ?></a></li>
									</ul>
									</div>
								</div>
									<p> 
									<?php echo mlx_get_lang('Enter 0 for unlimited ,Not Applicatble for is subscription.'); ?>
									</p>
								
							</div>	
											
											<div class="form-group">
												<label for="limit_purchase_by_user"><?php echo mlx_get_lang('Limit Purchase By Account'); ?><span class="text-red">*</span></label></label>
												<input type="number" class="form-control"  id="limit_purchase_by_user" name="limit_purchase_by_user" 
												value="<?php if(isset($purchase_limit)){ echo $purchase_limit; } ?>" >
											</div>

											<div class="form-group">
												<label for="purchase_button_text"><?php echo mlx_get_lang('Purchase button Text'); ?> <span class="text-red">*</span></label>
												<input type="text" class="form-control" required="required" name="purchase_button_text" id="purchase_button_text" 
												value="<?php if(isset($purchase_button_text)){ echo $purchase_button_text; } ?>">
											</div>
											<div class="form-group">
												<label for="package_order"><?php echo mlx_get_lang('Package Order'); ?><span class="text-red">*</span></label></label>
												<input type="number" class="form-control"  id="package_order" name="package_order" 
												value="<?php if(isset($package_order)){ echo $package_order; } ?>">
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
							
							<div class="box-footer">
							<?php 
							$dis_attr = '';
							  if($enable_subscription != 'Y' && $enable_property_posting != 'Y' && $enable_featured_property_posting != 'Y' && $enable_blog_posting != 'Y')
							 {
								 $dis_attr = ' disabled="disabled" ';
							 }
							 ?>
								<button <?php echo $dis_attr; ?> name="submit" type="submit" class="btn btn-<?php echo $myHelpers->global_lib->get_skin_class(); ?> pull-right" id="save_publish"><?php echo mlx_get_lang('Save Package'); ?></button>
							</div>
						</div>
						</div>
						
					</div> 

				</div>				
			</form>
        </section>
      </div>
<style>
#subscription_area{
	display:none;
}
#property_area{
	display:none;
}
#blog_area{
	display:none;
}
</style>
<script>
		jQuery(document).ready(function(){
			
			$("#is_subscription").on('ifChanged',function(){

				if ($(this).prop('checked')) {
						$("#subscription_area").show();
					}else{
						$("#subscription_area").hide();
					}
			});


			$("#is_property").on('ifChanged',function(){

			if ($(this).prop('checked')) {
					$("#property_area").show();
				}else{
					$("#property_area").hide();
				}
			});

			$("#is_blog").on('ifChanged',function(){

				if ($(this).prop('checked')) {
						$("#blog_area").show();
					}else{
						$("#blog_area").hide();
					}
				});
			
			var combination;

			$('.dropdown-menu.is_subscription_menus li').click(function() {

				var data_val = $(this).find('a').attr('data-val');
				var data_text = $(this).find('a').text();
					
				$(this).parents('.input-group-btn').find('.dropdown-toggle')
				.html(data_text+'&nbsp;&nbsp;<span class="fa fa-caret-down"></span>')
				.attr('data-btn-val',data_val);
				$(this).parents('.input-group-btn').removeClass('open');
				$(this).parents('.input-group').find('#sub_val_type').val(data_val);
				combination = $("#sub_validity").val()+' '+$("#sub_val_type").val();
				$("#sub_val_type").val(combination);
				return false;

			});

			$("#sub_validity").on('change',function(){
				//var data_value =$('.dropdown-menu.size_measure_menus li').find('a').attr('data-val');
				var data_value = $(this).parent().find('button.sub_val_type').attr('data-btn-val');
				$("#sub_val_type").val($(this).val()+' '+ data_value);
				//console.log($(this).val()	);
			});
		

			
			$('.dropdown-menu.package_life_menus li').click(function() {

				var data_val = $(this).find('a').attr('data-val');
				var data_text = $(this).find('a').text();

				$(this).parents('.input-group-btn').find('.dropdown-toggle')
							.html(data_text+'&nbsp;&nbsp;<span class="fa fa-caret-down"></span>')
							.attr('data-btn-val',data_val);
				$(this).parents('.input-group-btn').removeClass('open');
				$(this).parents('.input-group').find('#package_lifetime_val').val(data_val);
				var life = $("#package_lifetime").val()+' '+$("#package_lifetime_val").val();
				$("#package_lifetime_val").val(life);
				return false;

			});

				$("#package_lifetime").on('change',function(){
					//var data_value =$('.dropdown-menu.package_life_menus li').find('a').attr('data-val');
					var data_value = $(this).parent().find('button.package_lifetime_val').attr('data-btn-val');
					$("#package_lifetime_val").val($(this).val()+' '+ data_value);
				});

	});
</script>