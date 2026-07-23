

<div class="content-wrapper">
<section class="content-header">
  <h1 class="page-title"><i class="fa fa-money"></i> <?php echo mlx_get_lang('COD Confirmation'); ?>  
  

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
		   <?php echo mlx_get_lang('Thank you for using COD service. Your credit will reflect soon when admin will approve your transaction.'); ?>	
        </div>

  </div>
</div>
</section>
</div>