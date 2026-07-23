

<div class="content-wrapper">
<section class="content-header">
  <h1 class="page-title"><i class="fa fa-building"></i> <?php echo mlx_get_lang('Paid Successfully'); ?>  
  

  <?php if(isset($_SESSION['msg']) && !empty($_SESSION['msg']))
			{
				echo $_SESSION['msg'];
				unset($_SESSION['msg']);
			}
	?> 
</section>

<section class="content">

  <div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?>">
	
	<div class="starter-template">
        <h1>PayPal Payment</h1>
        <p class="lead">Canceld order</p>
    </div>

    <div class="contact-form">

        <div>
            <h3 style="font-family: 'quicksandbold'; font-size:16px; color:#313131; padding-bottom:8px;">Dear Member</h3>
            <span style="color:#D70000; font-size:16px; font-weight:bold;">We are sorry! Your last transaction was cancelled.</span>
        </div>
    </div>

  </div>
</div>
</section>
</div>

