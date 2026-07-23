


<div class="content-wrapper">
<section class="content-header">
  <h1 class="page-title"><i class="fa fa-bars"></i> <?php echo mlx_get_lang('Manage Menus'); ?>
  </h1>
  <?php if(isset($_SESSION['msg']) && !empty($_SESSION['msg']))
		{
			echo $_SESSION['msg'];
			unset($_SESSION['msg']);
		}
?> 
</section>

<section class="content">
	 
	
	<div class="row">
		
		<div class="col-md-12">   
			<?php 
			$attributes = array('name' => 'add_form_post','class' => 'form');		 			
			echo form_open_multipart('',$attributes); ?>
			<div class="box box-<?php echo get_skin_class(); ?>" style="border-top:0px;">
				<div class="box-body">
					<div class="row">
						<div class="col-md-4">
							  <label><?php echo mlx_get_lang('Select Menu Locations'); ?></label>
							  <select class="form-control select2_elem" name="menu_locations">
									<option value="primary_menu" 
									<?php 
									if(isset($_POST['menu_locations']) && $_POST['menu_locations'] == 'primary_menu')
										echo ' selected="selected" ';
									else if(isset($cur_menu) && $cur_menu == 'primary_menu')
										echo ' selected="selected" ';
									?>
									><?php echo mlx_get_lang('Primary Menu'); ?></option>
									<option value="footer_menu" 
									<?php if(isset($_POST['menu_locations']) && $_POST['menu_locations'] == 'footer_menu')
										echo ' selected="selected" ';
									else if(isset($cur_menu) && $cur_menu == 'footer_menu')
										echo ' selected="selected" ';
									?>><?php echo mlx_get_lang('Footer Menu'); ?></option>
							  </select>
						</div>
						<div class="col-md-4">
							<label style="display:block;">&nbsp;</label>
							<input type="submit" name="menu_location_submit" class="btn btn-<?php echo get_skin_class(); ?>" value="Submit">
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="col-md-8">   
			<?php 
			$attributes = array('name' => 'add_form_post','class' => 'form');		 			
			echo form_open_multipart('',$attributes); ?>
			<input type="hidden" name="user_id" class="user_id" value="<?php echo $this->session->userdata('user_id'); ?>">
			<input type="hidden" name="cur_menu" value="<?php if(isset($_POST['menu_locations'])) echo $_POST['menu_locations'];?>">
			<div class="box box-<?php echo get_skin_class(); ?>">
			
				<div class="box-header with-border">
					<h3 class="box-title"><?php echo mlx_get_lang($menu_type); ?></h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div>
				
				
				<div class="box-body nestable_menus">
					<div class="dd nestable">
						<ol class="dd-list"> 
							<?php if(isset($menu_list) && !empty($menu_list)) { ?>
								<?php 
								$dec_data = json_decode($menu_list,true);
								if(!empty($dec_data))
								{
									foreach($dec_data as $dk=>$dv)
									{
										/*if($dv['id'] == 'static~blog')
										{
											$isPlugAct = $myHelpers->isPluginActive('google_recaptcha');
											if($isPlugAct != true)
											{
												continue;
											}
											
										}*/
									?>
										<li class="dd-item" data-id="<?php echo $dv['id']; ?>" data-name="<?php echo $dv['name']; ?>" data-new="1" data-deleted="0" 
										data-menu_type="<?php echo $dv['menu_type']; ?>">
											<div class="dd-handle"><?php echo $dv['name']; ?><span><?php echo $dv['menu_type']; ?></span></div> 
											<span class="button-delete btn btn-default btn-xs pull-right" data-owner-id="<?php echo $dv['id']; ?>"> 
												<i class="fa fa-times-circle-o" aria-hidden="true"></i> 
											</span>
											<?php 
											if(isset($dv['children']) && !empty($dv['children']))
											{
												echo '<ol class="dd-list">';
													foreach($dv['children'] as $ck=>$cv)
													{
														?>
														<li class="dd-item" data-id="<?php echo $cv['id']; ?>" data-name="<?php echo $cv['name']; ?>" data-new="1" data-deleted="0" 
														data-menu_type="<?php echo $cv['menu_type']; ?>">
															<div class="dd-handle"><?php echo $cv['name']; ?><span><?php echo $cv['menu_type']; ?></span></div> 
															<span class="button-delete btn btn-default btn-xs pull-right" data-owner-id="<?php echo $cv['id']; ?>"> 
																<i class="fa fa-times-circle-o" aria-hidden="true"></i> 
															</span>
														</li>
														<?php
													}
												echo '</ol>';
											}
											?>
										</li>
										<?php
									}
								}
								?>
							<?php }else{ ?>
								<li class="dd-item" data-id="static~homepage" data-name="<?php echo mlx_get_lang('Home'); ?>" data-new="0" data-deleted="0" data-menu_type="<?php echo mlx_get_lang('Static Page'); ?>">
									<div class="dd-handle"><?php echo mlx_get_lang('Home'); ?><span><?php echo mlx_get_lang('Static Page'); ?></span></div> 
									<span class="button-delete btn btn-default btn-xs pull-right" data-owner-id="static~homepage"> 
										<i class="fa fa-times-circle-o" aria-hidden="true"></i> 
									</span>
								</li>
								<li class="dd-item" data-id="static~property-for-sale" data-name="<?php echo mlx_get_lang('Sale'); ?>" data-new="0" data-deleted="0" data-menu_type="<?php echo mlx_get_lang('Static Page'); ?>">
									<div class="dd-handle"><?php echo mlx_get_lang('Sale'); ?><span><?php echo mlx_get_lang('Static Page'); ?></span></div> 
									<span class="button-delete btn btn-default btn-xs pull-right" data-owner-id="static~property-for-sale"> 
										<i class="fa fa-times-circle-o" aria-hidden="true"></i> 
									</span>
								</li>
								<li class="dd-item" data-id="static~property-for-rent" data-name="<?php echo mlx_get_lang('Rent'); ?>" data-new="0" data-deleted="0" data-menu_type="<?php echo mlx_get_lang('Static Page'); ?>">
									<div class="dd-handle"><?php echo mlx_get_lang('Rent'); ?><span><?php echo mlx_get_lang('Static Page'); ?></span></div> 
									<span class="button-delete btn btn-default btn-xs pull-right" data-owner-id="static~property-for-rent"> 
										<i class="fa fa-times-circle-o" aria-hidden="true"></i> 
									</span>
								</li>
								<li class="dd-item" data-id="static~contact" data-name="<?php echo mlx_get_lang('Contact Us'); ?>" data-new="0" data-deleted="0" data-menu_type="<?php echo mlx_get_lang('Static Page'); ?>">
									<div class="dd-handle"><?php echo mlx_get_lang('Contact Us'); ?><span><?php echo mlx_get_lang('Static Page'); ?></span></div> 
									<span class="button-delete btn btn-default btn-xs pull-right" data-owner-id="static~contact"> 
										<i class="fa fa-times-circle-o" aria-hidden="true"></i> 
									</span>
								</li>
							<?php } ?>
						</ol>
					</div>
					  
					
					<input type="hidden" class="form-control" name="options[<?php echo $menu_slug; ?>]" id="json-output">
				</div>
				<div class="box-footer">
					<button name="submit" type="submit" class="btn btn-<?php echo get_skin_class(); ?> pull-right"><?php echo mlx_get_lang('Save'); ?></button>
				</div>
			</div>
			</form>
		</div>
		
	  <div class="col-md-4">
			
			<?php do_action("cms_app_menu_static_pages"); ?>
			
			
			
          <?php if(isset($page_list) && $page_list->num_rows() > 0) { ?>
			<div class="box box-<?php echo get_skin_class(); ?> collapsed-box">
			  <div class="box-header with-border">
				  <h3 class="box-title"><?php echo mlx_get_lang('Pages'); ?></h3>
				  <div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
				  </div>
				</div>
				<input type="hidden" class="menu_type" value="page">
				<div class="box-body menu-option-list">
					<?php 
					foreach($page_list->result() as $p_row)
					{
					?>
						<div class="checkbox">
							<label >
							  <input type="checkbox" class="minimal no-validate" value="page~<?php echo $myHelpers->EncryptClientId($p_row->page_id); ?>" data-title="<?php echo ucfirst($p_row->page_title); ?>">
							  &nbsp; <?php echo ucfirst($p_row->page_title); ?>
							</label>
						</div>
					<?php
					}
					?>
				</div>
				 <div class="box-footer">
					<button name="submit" type="button" class="btn btn-default pull-right add_to_menu" ><?php echo mlx_get_lang('Add to Menu'); ?></button>
				  </div>
			</div>
			<?php } ?>
			
			<?php if(isset($property_type_list) && $property_type_list->num_rows() > 0) { ?>
			<div class="box box-<?php echo get_skin_class(); ?> collapsed-box">
			  <div class="box-header with-border">
				  <h3 class="box-title"><?php echo mlx_get_lang('Property Types'); ?></h3>
				  <div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
				  </div>
				</div>
				<input type="hidden" class="menu_type" value="property_type">
				<div class="box-body menu-option-list">
					
					<div class="row">
						<div class="col-md-12">
					<?php 
					foreach($property_type_list->result() as $pt_row)
					{
					?>
						<div class="checkbox">
							<label >
							  <input type="checkbox" class="minimal no-validate" value="property_type~<?php echo $myHelpers->EncryptClientId($pt_row->pt_id); ?>" data-title="<?php echo ucfirst($pt_row->title); ?>">
							  &nbsp; <?php echo ucfirst($pt_row->title); ?>
							</label>
						</div>
					<?php
					}
					?>
						</div>	
					</div>
				</div>
				 <div class="box-footer">
					<button name="submit" type="button" class="btn btn-default pull-right add_to_menu" ><?php echo mlx_get_lang('Add to Menu'); ?></button>
				  </div>
			</div>
			<?php } ?>
			
			<div class="box box-<?php echo get_skin_class(); ?> collapsed-box">
			  <div class="box-header with-border">
				  <h3 class="box-title"><?php echo mlx_get_lang('Custom Links'); ?></h3>
				  <div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
				  </div>
				</div>
				<input type="hidden" class="menu_type" value="custom_link">
				<div class="box-body menu-option-list">
					<div class="form-group">
						<label><?php echo mlx_get_lang('URL'); ?></label>
						<input type="url" class="form-control cl_url" value="http://">
					</div>
					<div class="form-group">
						<label><?php echo mlx_get_lang('Link Title'); ?></label>
						<input type="text" class="form-control cl_title" >
					</div>
				</div>
				 <div class="box-footer">
					<button name="submit" type="button" class="btn btn-default pull-right add_to_menu" ><?php echo mlx_get_lang('Add to Menu'); ?></button>
				  </div>
			</div>
			
	  </div>
  </div>
	  
</section>
</div>
<script>
$(document).ready(function() {
	
	var max_depth = <?php 
	if(isset($_POST['menu_locations']) && $_POST['menu_locations'] == 'primary_menu')
		echo '2';
	else if(isset($cur_menu) && $cur_menu == 'primary_menu')
		echo '2';
	else if(isset($_POST['menu_locations']) && $_POST['menu_locations'] == 'footer_menu')
		echo '1';
	else if(isset($cur_menu) && $cur_menu == 'footer_menu')
		echo '1';
	else
		echo '2';
	?>;
	$('.nestable_menus .nestable').nestable({
		maxDepth: max_depth
	}).on('change', updateOutput);
});
</script>