<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<?php
		
		$website_logo_text = get_option('website_logo_text');
	if(empty($website_logo_text)) $website_logo_text = "Real Estate Web - with Agency Portal";
	
	$front_end_register = get_option('enbale_front_end_registration');
	if(empty($front_end_register) || !$front_end_register) $front_end_register = "N";
	?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
<meta name="description"  content=""/>
<meta name="keywords" content=""/>
<meta name="robots" content="ALL,FOLLOW"/>
<meta name="Author" content="AIT"/>
<meta http-equiv="imagetoolbar" content="no"/>
<title><?php echo mlx_get_lang('Login'); ?> | <?php echo mlx_get_lang($website_logo_text); ?></title>
<?php
echo link_tag("application/views/$theme/assets/bootstrap/css/bootstrap.min.css");
echo link_tag("application/views/$theme/assets/css/AdminLTE.min.css");
echo link_tag("application/views/$theme/assets/css/font-awesome.min.css");
echo link_tag("application/views/$theme/assets/custom-styles.css");
?>
<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
	<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
<style>
	<?php 
	$login_page_bg_type = get_option('login_page_bg_type');
	$login_bg_image = get_option('login_bg_image');
	$login_bg_color = get_option('login_bg_color');
	if($login_page_bg_type == 'color' && !empty($login_bg_color))
	{
		echo '.login-page { background-color:#'.$login_bg_color.';
							background-image: unset;
						  }';
	}
	else if($login_page_bg_type == 'image' && !empty($login_bg_image) && file_exists('uploads/media/'.$login_bg_image))
	{
		echo '.login-page { background-image: url("'.base_url().'uploads/media/'.$login_bg_image.'");
						  }';
	}
	else if($login_page_bg_type == 'image' && empty($login_bg_image) && !empty($login_bg_color))
	{
		echo '.login-page { background-color:#'.$login_bg_color.';
							background-image: unset;
						  }';
	}
	else
	{
		echo '.login-page { 
							background-image: url("'.base_url().'application/views/'.$theme.'/assets/images/login-bg.jpg");
						  }';
	}
	?>
	
	.login-logo img.logo-img {
		max-width: 100%;
		padding: 5px 10px;
	}
	
</style>
</head>

<body class="hold-transition login-page">
	<div class="login-box">
	
		<?php 
		$website_logo = 	get_option('website_logo');
		?>
	
      <div class="login-logo">
        <a href="#"><?php /*echo mlx_get_lang($website_logo_text);*/ ?>
		<?php if(!empty($website_logo) || !empty($website_logo_text)){ ?>
				
				<?php if ( !empty($website_logo) && file_exists('uploads/media/' . $website_logo)) { ?>
					<img class="logo-img" src="<?php echo site_url() . 'uploads/media/' . $website_logo; ?>" 
						alt="<?php echo $website_logo_text; ?>">
				<?php } else if (isset($website_logo_text)) {
					echo '<strong">' . $website_logo_text . '</strong>';
				}
				?>
				
		 <?php }?>
		</a>
      </div>
      <div class="login-box-body">
        <p class="login-box-msg"><?php echo mlx_get_lang('Sign in to Start Your Session'); ?></p>
		<?php 
		if(isset($_SESSION['msg']) & !empty($_SESSION['msg'])) { 
			echo $_SESSION['msg'];
			unset($_SESSION['msg']);
		}
		?>
			<?php 	$attributes = array('name' => 'login_form');		 			
			echo form_open('admin/logins/login',$attributes); ?>
			  <input type="hidden" name="redirect_to" value="<?php if(isset($_GET['redirect_to'])) echo $_GET['redirect_to']; ?>">
			  <div class="form-group has-feedback">
				<input type="text" class="form-control" placeholder="Username" size="25" name="username" required>
				<span class="fa fa-envelope form-control-feedback"></span>
			  </div>
			  
			  <div class="form-group has-feedback">
				<input type="password" class="form-control" placeholder="Password" size="25" name="userpass" required>
				<span class="fa fa-lock form-control-feedback"></span>
			  </div>
			  
			  <div class="row">
				<div class="col-xs-8">
				  <a class="btn btn-default btn-flat" href="<?php echo base_url(array('logins','forgot_password')); ?>">
				  <?php echo mlx_get_lang('Forgot Password'); ?></a>
				</div>
				<div class="col-xs-4">
				  <button type="submit" name="submit" class="btn btn-primary btn-block btn-flat"><?php echo mlx_get_lang('Sign In'); ?></button>
				</div>
			  </div>
			  
			  <div class="social-auth-links text-center">
				  <p>- <?php echo mlx_get_lang('OR'); ?> -</p>
				  
				  <?php if($front_end_register == 'Y'){ ?>
				  <a href="<?php echo str_replace('admin/','',base_url('register')); ?>" class="btn btn-block btn-default btn-flat">
				   <?php echo mlx_get_lang('Register'); ?></a>
				  <?php } ?> 
				  
				  <a href="<?php echo str_replace('admin/','',base_url()); ?>" class="btn btn-block btn-default btn-flat">
				  <i class="fa fa-share"></i> &nbsp;<?php echo mlx_get_lang('Back to Website'); ?></a>
			  </div>
			  
			</form>
		
      </div>
    </div>
	
	
<?php 
echo script_tag("application/views/$theme/assets/plugins/jQuery/jQuery-2.1.4.min.js");
echo script_tag("application/views/$theme/assets/bootstrap/js/bootstrap.min.js");
?>
</body>
</html>
