

<div class="content-wrapper">
<section class="content-header">
  <h1 class="page-title"><i class="fa fa-list"></i> <?php echo mlx_get_lang('Payment Methods'); ?>  
  

  <?php if(isset($_SESSION['msg']) && !empty($_SESSION['msg']))
			{
				echo $_SESSION['msg'];
				unset($_SESSION['msg']);
			}
	?> 
</section>

<section class="content">

  <div class="box box-<?php echo get_skin_class(); ?>">
	<?php 
	$attributes = array('name' => 'checkout','class' => 'form');		 			
	echo form_open_multipart('packages/confirmation/'.$package_id,$attributes);  
	
	
	?>
	<div class="box-header with-border">
	  <h3 class="box-title"><?php echo mlx_get_lang('Please Choose Any Payment Method'); ?></h3>
	</div>
	<div class="box-body content-box">
        
		<input type="hidden" name="user_id" class="user_id" value="<?php echo $this->session->userdata('user_id'); ?>">
        <input type="hidden" name="package_currency" class="user_id" value="<?php echo $package_currency; ?>">
		
        <?php 
        $c=0;
		
		
        foreach (json_decode($methods) as $key => $value) {
          if($value->is_enable === 'Y'){   
            $method = explode('_',$key);
           $c++;
			
			$payment_method = $method[2];
			
		 if( isset($payment_method_currency_supports) 
			 && array_key_exists($payment_method , $payment_method_currency_supports )
			 && !in_array($package_currency,$payment_method_currency_supports[$payment_method])
		 ) continue;
          ?>
		  <div class="box-header with-border">
			<h4 class="box-title">
			
			  <input type="radio" name="payment_method" required id="<?php echo 'radio_'.$method[2];?>" value="<?php echo $method[2];?>" class="minimal ">
			  &nbsp;
			  <a data-toggle="collapse" data-parent="#accordion" id="<?php echo 'btn_'.$method[2];?>" href="#<?php echo $c;?>" aria-expanded="false" class="collapsed payment-method text-black">
				<?php  if($method[2] === 'stripe'){ echo ucfirst($method[2]);}elseif($method[2] === 'bank'){ echo ucfirst($method[2]); }elseif($method[2] === 'paypal'){echo ucfirst($method[2]); }elseif($method[2] == 'cod'){ echo ucfirst($method[2]); }else echo ucfirst($method[2]); ?>
			  </a>

			</h4>
		  </div>
		  <div id="<?php echo $c;?>" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
			<div class="box-body">
			<?php
			//echo $method[2];

			if($method[2] === 'stripe'){ 
				 echo $value->method_stripe;
			  }elseif($method[2] === 'bank'){?> 
				  <h3><?php echo $value->method_bank_transfer; ?></h3> 
				<h4><?php echo $value->bank_transfer_guide; ?></h4> 
			  <?php }elseif($method[2] === 'paypal'){ 
				?>
				  Pay With Paypal 
			  <?php }elseif($method[2] == 'cod'){?>
				<h3><?php echo $value->method_cod; ?></h3> 
				<h4><?php echo $value->cod_payment_guide; ?></h4> 
			  <?php } ?>
			</div>
		  </div>
          <?php }
        }
        ?>
       
    </div>
	 <div class="box-footer">
			<button type="submit" name="submit" class="btn btn-<?php echo $myHelpers->global_lib->get_skin_class(); ?> submit-form-btn" id="save_publish"><?php echo mlx_get_lang('CheckOut'); ?></button>
		</div>
	</form>
</section>
</div>
<script>
  $(document).ready(function(){
    $("a.payment-method").click(function(){
      /*$("#<?php echo 'radio_'.$method[2];?>").prop('checked', true);
      $(this).parent().find('input').prop('checked', 'checked');*/
    });
  })
</script>