

<div class="content-wrapper">
<section class="content-header">
  <h1 class="page-title"><!--<i class="fa fa-cc-stripe"></i> --> <?php //echo mlx_get_lang('Stripe CheckOut'); ?>  
  </h1>

  <?php if(isset($_SESSION['msg']) && !empty($_SESSION['msg']))
			{
				echo $_SESSION['msg'];
				unset($_SESSION['msg']);
			}
	?> 
</section>

<section class="content">
	<?php 
	
	$attributes = array('name' => 'add_form_post','class' => 'form add_package_form');		 			
	echo form_open_multipart('admin/packages/confirmation',$attributes); 
	?>
	
	<input type="hidden" name="user_id" class="user_id" value="<?php echo $this->session->userdata('user_id'); ?>">
	
	<div class="row">
	<div class="col-md-12">   
	   
	  <div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?>">
		<div class="box-header with-border">
		  <h3 class="box-title"><?php echo mlx_get_lang('Package Confirmation'); ?></h3>
		  <div class="box-tools pull-right">
			<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		  </div>
		</div>
		  <div class="box-body">
						<input title="item_id" name="item_id" type="hidden" value="<?php echo $query->row()->package_id; ?>">
						<input title="item_name" name="item_name" type="hidden" value="<?php echo $query->row()->package_name; ?>">
                       
                        <input title="item_description" name="item_description" type="hidden" value="<?php echo $query->row()->package_type; ?>">
                        
                        <input title="item_price" name="item_price" type="hidden" value="<?php echo $query->row()->package_price; ?>">
						
						<input title="item_currency" name="item_currency" type="hidden" value="<?php echo $query->row()->package_currency; ?>">
                        
            <h4><?php echo mlx_get_lang('You have Selected'); ?> :- <?php echo $query->row()->package_name; ?></h4>

            <h5><?php echo mlx_get_lang('Price of Package'); ?> :- <?php echo $query->row()->package_currency .' '. $query->row()->package_price; ?></h5>

            <h6><?php echo mlx_get_lang('Package Type'); ?> :- <?php  if($query->row()->package_type == 'subscription') { echo $query->row()->package_type; } else{
                echo $query->row()->package_type;
            } ?></h6>
            <h6><?php echo mlx_get_lang('Package Duration'); ?> :- <?php  if($query->row()->package_life == '0 days') { echo 'Unlimited'; } else{
                echo $query->row()->package_life;
            } ?></h6>
							
			<button type="submit" name="submit" class="btn btn-<?php echo $myHelpers->global_lib->get_skin_class(); ?> pull-right submit-form-btn" id="save_publish"><?php echo mlx_get_lang('Pay Now'); ?></button>
		
		 </div>
		
	  </div>
</div>
 
	  </form>
</section>
</div>