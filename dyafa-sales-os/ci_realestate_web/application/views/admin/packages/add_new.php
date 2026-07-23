<?php 
	$site_users = $myHelpers->config->item("site_users");
?>

<?php 
$enable_subscription = get_option('enable_subscription');
$enable_property_posting = get_option('enable_property_posting');
$enable_featured_property_posting = get_option('enable_featured_property_posting');
$enable_blog_posting = get_option('enable_blog_posting');
?>
<div class="content-wrapper">
<section class="content-header">
  <h1 class="page-title"><i class="fa fa-plus"></i> <?php echo mlx_get_lang('Add New Package'); ?> </h1>
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
	<?php 
	$attributes = array('name' => 'add_form_post','class' => 'form add_package_form');		 			
	echo form_open_multipart('admin/packages/add_new',$attributes); ?>
	<input type="hidden" name="user_id" class="user_id" value="<?php echo $this->session->userdata('user_id'); ?>">
	
	<div class="row">
	<div class="col-md-8">   
	   
	  <div class="box box-<?php echo get_skin_class(); ?>">
		<div class="box-header with-border">
		  <h3 class="box-title"><?php echo mlx_get_lang('Package Details'); ?></h3>
		  <div class="box-tools pull-right">
			<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		  </div>
		</div>
		  <div class="box-body">
				
				<div class="form-group">
				  <label for="package"><?php echo mlx_get_lang('Package Name'); ?> <span class="text-red">*</span></label>
				  <input type="text" class="form-control" required="required" name="package" id="package">
				</div>
				
				<div class="form-group">
				  <label for="packages_price"><?php echo mlx_get_lang('Package Price'); ?> <span class="text-red">*</span></label>
				  <input type="number" class="form-control"  id="packages_price" required name="packages_price" min="0">
				 
				</div>
				
				<div class="form-group">
			  <label for="currency_code"><?php echo mlx_get_lang('Currency'); ?> <span class="required">*</span></label>
			  
			  <select class="form-control select2_elem" name="currency_code" id="currency_code" required>
				  <option value=""><?php echo mlx_get_lang('Select Currency'); ?></option>
				  <?php if(isset($currency_symbols) && !empty($currency_symbols)) {
						foreach($currency_symbols as $k=>$v)
						{
							
							echo '<option value="'.$k.'">'.$k.' - '.$v.'</option>';
						}
					}
					?>
				  
			  </select>
			</div>		
		

			<?php if($enable_subscription == 'Y') { ?>
				<div class="form-group">
				 <input type="checkbox" id="is_subscription" name="feature[is_subscription][enable]" value="1" 
						class="minimal child_show_hide" data-child="subscription_area" /> &nbsp;
					<label for="is_subscription"> <?php echo mlx_get_lang('Is Subscription'); ?></label>
				</div>

				<div class="form-group child-form-group subscription_area hide_child_form" id="subscription_area">
						<label for="size"><?php echo mlx_get_lang('Validity'); ?> </label>
						<div class="input-group">
							
							<input type="number" class="form-control" min="0" name="" value="0" id="sub_validity" >
							<input type="hidden" class="form-control" value="0 days" 
							name="feature[is_subscription][subscription_validity]" id="sub_val_type">
							<div class="input-group-btn">
							  <button type="button" class="btn btn-default dropdown-toggle sub_val_type" data-toggle="dropdown" 
							  data-btn-val="days"
							  aria-expanded="false"><?php echo mlx_get_lang('Days'); ?>&nbsp;&nbsp;<span class="fa fa-caret-down"></span></button>
							  <ul class="dropdown-menu is_subscription_menu">
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
				 <input type="checkbox" id="is_property" name="feature[property][enable]" value="1" 
						class="minimal child_show_hide" data-child="property_area" /> &nbsp;
					<label for="is_property"> <?php echo mlx_get_lang('Provide Property Posting'); ?> </label>
				</div>
				
				<div class="child-form-group property_area hide_child_form" id="property_area">
					<?php if($enable_property_posting == 'Y') { ?>
					<div class="form-group">
						<label for="properties"><?php echo mlx_get_lang('Number Of Properties'); ?> </label>
						<input type="number" class="form-control " min="0" id="properties" name="feature[property][post_property]"/>
				
					</div>
					<?php } ?>
					<?php if($enable_featured_property_posting == 'Y') { ?>
					<div class="form-group">
						<label for="featured_properties"><?php echo mlx_get_lang('Number Of Featured Properties'); ?> </label>
						<input type="number" class="form-control " min="0"  id="featured_properties" name="feature[property][featured_property]"/>
					</div>
					<?php } ?>
					
				</div>
			<?php } ?>
			
			
			<?php	do_action("package_additional_features");    ?>
			
			<?php if($enable_blog_posting == 'Y') { ?>
			
				<div class="form-group">
				 <input type="checkbox" id="is_blog" name="feature[blog][enable]" value="1" 
						class="minimal child_show_hide" data-child="blog_area" /> &nbsp;
						<label for="is_blog"> <?php echo mlx_get_lang('Provide Blog Posting'); ?></label>
				</div>
				
				<div class="form-group child-form-group blog_area hide_child_form" id="blog_area">
					<label for="blog"><?php echo mlx_get_lang('Number Of Blog'); ?></label>
					<input type="number" class="form-control "  id="blog" name="feature[blog][post_blog]"/>
				</div>
			<?php } ?>
			
			<div class="form-group">
				<label for="applicable_for"><?php echo mlx_get_lang('Applicable For'); ?><span class="required">*</span></label>
				<br/>
				  
				<?php 
				
				foreach($site_users as $k => $user){ if($k == 'admin') continue; ?>
					<label for="<?php echo 'user_for'.$k; ?>">
						<input type="checkbox" id="<?php echo 'user_for_'.$k; ?>" required class="minimal"  name="user_types[]"  value="<?php echo $k; ?>"/>&nbsp;
							<?php echo ucfirst($user['title']); ?> &nbsp;&nbsp;		
					</label>		
				<?php } ?>
			</div>
			<div class="form-group">
				<label for="package_lifetime"><?php echo mlx_get_lang('Package Expire After Purchase'); ?> <span class="text-red">*</span></label></label>
				<!-- <input type="number" class="form-control"  id="package_lifetime" name="package_lifetime" value="0"> -->
				
				
				<div class="input-group">
						
					<input type="number" class="form-control" min="0" name=""  id="package_lifetime" name="package_lifetimes" value="0">
				
					<input type="hidden" class="form-control" value="0 days" 
					name="package_lifetime" id="package_lifetime_val">
					
					<div class="input-group-btn">
					  <button type="button" class="btn btn-default dropdown-toggle package_lifetime_val" data-toggle="dropdown" 
					  data-btn-val="days"
					  aria-expanded="false">
					  <?php echo mlx_get_lang('Days'); ?>&nbsp;&nbsp;<span class="fa fa-caret-down"></span></button>
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
				<label for="limit_purchase_by_user"><?php echo mlx_get_lang('Limit Purchase By Account'); ?> <span class="text-red">*</span></label></label>
				<input type="number" class="form-control"  id="limit_purchase_by_user" name="limit_purchase_by_user" value="5">
					<p> 
					<?php echo mlx_get_lang('Enter 0 to Unlimited for this package'); ?>
					</p>
			</div>

			<div class="form-group">
				  <label for="purchase_button_text"><?php echo mlx_get_lang('Purchase button Text'); ?> <span class="text-red">*</span></label>
				  <input type="text" class="form-control" required="required" name="purchase_button_text" id="purchase_button_text" value="Buy Now">
			</div>
			<div class="form-group">
				<label for="package_order"><?php echo mlx_get_lang('Package Order'); ?> <span class="text-red">*</span></label></label>
				<input type="number" class="form-control"  id="package_order" name="package_order" value="0">
			</div>

		 </div>
		
	  </div>
</div>
  <div class="col-md-4">
	  <div class="box box-<?php echo get_skin_class(); ?>">
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
			<button <?php echo $dis_attr; ?> type="submit" name="submit" class="btn btn-<?php echo get_skin_class(); ?> pull-right submit-form-btn" 
					id="save_publish"><?php echo mlx_get_lang('Save Package'); ?></button>
		</div>
	  </div>

	
	
  </div>
  
  </div>
	  </form>
</section>
</div>

<?php   $this->load->view("$theme/packages/package_update_script");?>


<?php	do_action("admin_footer_scripts", "admin_package_updates");		?>