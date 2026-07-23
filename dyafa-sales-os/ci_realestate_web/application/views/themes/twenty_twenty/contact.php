<?php 
$company_address = get_option('company_address');
$company_mob = get_option('company_mob');
$company_tel = get_option('company_tel');
$contact_email = get_option('contact_email');

$company_address = apply_filters("cms_get_details", '','company_address');
$company_mob = apply_filters("cms_get_details", '','company_mob');
$company_tel = apply_filters("cms_get_details", '','company_tel');
$contact_email = apply_filters("cms_get_details", '','contact_email');


?>

<script>
 $(function() {
    $('.alert').delay(5000).fadeOut('slow');
 });
 </script>

<?php if(isset($banner_row) && isset($banner_row->b_image) && !empty($banner_row->b_image) && file_exists('uploads/banner/'.$banner_row->b_image)){ ?>
<section class="page-top-section set-bg" 
	data-setbg="<?php echo base_url(); ?>uploads/banner/<?php echo $banner_row->b_image; ?>" 
	style="background-image: url(<?php echo base_url(); ?>uploads/banner/<?php echo $banner_row->b_image; ?>);">
	<div class="container text-white">
		<h1><?php echo mlx_get_lang('Contact Us'); ?></h1>
	</div>
</section>
<?php } ?>

<div class="site-section">
      <div class="container">
		
        <div class="row">
		<?php if( form_error('contact_name')) 	  { 	echo form_error('contact_name'); 	  } ?>
		<?php if( form_error('contact_email')) 	  { 	echo form_error('contact_email'); 	  } ?>
		<?php if( form_error('contact_subject')) 	  { 	echo form_error('contact_subject'); 	  } ?>
		<?php if( form_error('contact_message')) 	  { 	echo form_error('contact_message'); 	  } ?>


          <div class="col-md-12 col-lg-8 mb-5">
          
			
          <?php 
			$args = array('class' => 'contact_form  text-left', 'id' => 'contact_form',
			'enctype' => 'application/x-www-form-urlencoded');
			echo form_open('index.php/main/contact',$args);?> 
			  <?php if(isset($_SESSION['msg']) && !empty($_SESSION['msg'])) {
					echo $_SESSION['msg'];
					unset($_SESSION['msg']);
			   } ?>
              <div class="row form-group">
                <div class="col-md-12 mb-3 mb-md-0">
                  <label class="font-weight-bold" for="contact_name"><?php echo mlx_get_lang('Full Name'); ?> <span class="required text-danger">*</span></label>
                  <input type="text" id="contact_name" name="contact_name"  required class="form-control" >
                </div>
              </div>
              <div class="row form-group">
                <div class="col-md-12">
                  <label class="font-weight-bold" for="contact_email"><?php echo mlx_get_lang('Email'); ?> <span class="required text-danger">*</span></label>
                  <input type="email" id="contact_email" name="contact_email" required class="form-control" >
                </div>
              </div>
              <div class="row form-group">
                <div class="col-md-12">
                  <label class="font-weight-bold" for="contact_subject"><?php echo mlx_get_lang('Subject'); ?> <span class="required text-danger">*</span></label>
                  <input type="text" required id="contact_subject" name="contact_subject" class="form-control" >
                </div>
              </div>
              

              <div class="row form-group">
                <div class="col-md-12">
                  <label class="font-weight-bold" for="contact_message"><?php echo mlx_get_lang('Message'); ?> <span class="required text-danger">*</span></label> 
                  <textarea required name="contact_message" id="contact_message" cols="30" rows="5" class="form-control" ></textarea>
                </div>
              </div>
			  
			  
			  
			  <?php 	do_action("contact_form_extra_fields");	?>
			  
			  <?php 	do_action("contact_form_extra_field_before_submit");	?>
			  
              <div class="row form-group">
                <div class="col-md-12">
                  <button type="submit" name="submit" id="submit-contact-form"  
							class="btn submit-contact-form-btn py-2 px-4 rounded-0 text-white"><?php echo mlx_get_lang('Send Message'); ?></button>
                </div>
              </div>

  
            </form>
          </div>

          <div class="col-lg-4">
            <div class="p-4 mb-3 bg-white contact-us-right-block">
              <h3 class="h6 text-black mb-3 text-uppercase"><?php echo mlx_get_lang('Contact Info'); ?></h3>
              
			  <?php if(isset($company_address) && !empty($company_address)) { ?>
				  <p class="mb-0 font-weight-bold"><?php echo mlx_get_lang('Address'); ?></p>
				  <p class="mb-4"><?php echo "<pre>".( $company_address)."</pre>"; ?></p>
			  <?php } ?>
              
			  <?php if(isset($company_tel) && !empty($company_tel)) { ?>
				<p class="mb-0 font-weight-bold"><?php echo mlx_get_lang('Phone'); ?></p>
				<p class="mb-4"><a href="tel:<?php echo $company_tel; ?>"><?php echo $company_tel; ?></a></p>
			  <?php } ?>
			  
			  <?php if(isset($contact_email) && !empty($contact_email)) { ?>
				  <p class="mb-0 font-weight-bold"><?php echo mlx_get_lang('Email Address'); ?></p>
				  <p class="mb-0"><a href="mailto:<?php echo $contact_email; ?>"><?php echo $contact_email; ?></a></p>
			  <?php } ?>
            </div>
            
          </div>
		  
		  
		  
        </div>
      </div>
    </div>
	
	

<?php

do_action("cms_footer_scripts", "contact_form_scripts");


?>	