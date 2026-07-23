      
      <div class="content-wrapper">
        <section class="content-header">
          <h1 class="page-title"><i class="fa fa-briefcase"></i> <?php echo mlx_get_lang('Property Amenities'); ?> </h1>
		  <?php if(isset($_SESSION['msg']) && !empty($_SESSION['msg']))
					{
						echo $_SESSION['msg'];
						unset($_SESSION['msg']);
					}
			?> 
        </section>
		
        <section class="content">
		  <?php 
			$attributes = array('name' => 'add_form_post','class' => 'form');		 			
			echo form_open_multipart('admin/property/amenities',$attributes); ?>
			<input type="hidden" name="user_id" class="user_id" value="<?php echo $this->session->userdata('user_id'); ?>">

			<div class="row">
				<div class="col-md-8">   
				  <div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?>">
					  <div class="box-header with-border">
						  <h3 class="box-title"><?php echo mlx_get_lang('Indoor Amenities'); ?> </h3>
						  <div class="box-tools pull-right">
							<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						  </div>
						</div>
					  <div class="box-body">
							<select name="amenities[indoor_amenities][]" multiple="multiple" class="amenities_list form-control">
								<?php 
								if(isset($amenities_list['indoor_amenities']) && !empty($amenities_list['indoor_amenities']))
								{
									foreach($amenities_list['indoor_amenities'] as $k=>$v)
									{
										echo '<option selected="selected">'.$v.'</option>';
									}
								}
								?>
							</select>
					  </div>
				  </div>
				  
				  <div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?>">
					  <div class="box-header with-border">
						  <h3 class="box-title"><?php echo mlx_get_lang('Outdoor Amenities'); ?> </h3>
						  <div class="box-tools pull-right">
							<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						  </div>
						</div>
					  <div class="box-body">
							<select name="amenities[outdoor_amenities][]" multiple="multiple" class="amenities_list form-control">
								<?php 
								if(isset($amenities_list['outdoor_amenities']) && !empty($amenities_list['outdoor_amenities']))
								{
									foreach($amenities_list['outdoor_amenities'] as $k=>$v)
									{
										echo '<option selected="selected">'.$v.'</option>';
									}
								}
								?>
							</select>
					  </div>
				  </div>
				</div>
		  
			  <div class="col-md-4">
				  <div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?>">
					<div class="box-header with-border">
					  <h3 class="box-title"><?php echo mlx_get_lang('Status'); ?></h3>
					</div>
					<div class="box-footer">
						<button name="submit" type="submit" class="btn btn-<?php echo $myHelpers->global_lib->get_skin_class(); ?> pull-right" id="save_publish"><?php echo mlx_get_lang('Save'); ?></button>
					</div>
				  </div>
			  </div>
		  </div>
	  </form>
	</section>
</div>

