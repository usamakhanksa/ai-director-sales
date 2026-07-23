

<div class="content-wrapper">
<section class="content-header">
  <h1 class="page-title"><i class="fa fa-check"></i> <?php echo mlx_get_lang('Sucessful'); ?>  
  

  <?php if(isset($_SESSION['msg']) && !empty($_SESSION['msg']))
			{
				echo $_SESSION['msg'];
				unset($_SESSION['msg']);
			}
	?> 
</section>

<section class="content">

  <div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?>">
	
	<div class="box-body content-box">
        
        <div class="col-md-12">
            <?php if(isset($_SESSION['payment_method']) && $_SESSION['payment_method'] == 'Cod'){?>
                <h3>
				<?php echo mlx_get_lang('Thanks You have successfull bought  the package and after the Payment collection  Packege will be availble.'); ?>
				</h3>
            <?php unset($_SESSION['payment_method']);}
            elseif(isset($_SESSION['payment_method']) &&  $_SESSION['payment_method'] == 'bank'){?>
                <h3>
				<?php echo mlx_get_lang('Thanks You have successfull bought the package After Bank Account reflect the Balance Packege will be availble.'); ?>
				 </h3>
            <?php unset($_SESSION['payment_method']); }
            elseif(
			isset($_SESSION['payment_method']) 
			/*(isset($_SESSION['payment_method']) 
			&& $_SESSION['payment_method'] == 'stripe') || 
            (isset($_SESSION['payment_method']) && $_SESSION['payment_method'] == 'paypal') || 
            (isset($_SESSION['payment_method']) && $_SESSION['payment_method'] == 'razorpay')*/
			){?>
                <h3>
				<?php echo mlx_get_lang('Thanks You have successfull bought the package and Packege will be availble.'); ?>
				 </h3>
            <?php unset($_SESSION['payment_method']); }
			/*else{
              redirect('packages/front_package_page');
            }*/ 
			?>
        </div>

  </div>
</div>
</section>
</div>