<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
<meta name="description"  content=""/>
<meta name="keywords" content=""/>
<meta name="robots" content="ALL,FOLLOW"/>
<meta name="Author" content="AIT"/>
<meta http-equiv="imagetoolbar" content="no"/>
<title><?php echo mlx_get_lang('Forgot Password'); ?> | <?php echo mlx_get_lang('Real Estate'); ?></title>
<?php
echo link_tag("themes/$theme/bootstrap/css/bootstrap.min.css");
echo link_tag("themes/$theme/css/AdminLTE.min.css");
echo link_tag("themes/$theme/css/font-awesome.min.css");
echo link_tag("themes/$theme/custom-styles.css");
?>
<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
	<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
<style>
	<?php 
	$login_page_bg_type = $myHelpers->global_lib->get_option('login_page_bg_type');
	$login_bg_image = $myHelpers->global_lib->get_option('login_bg_image');
	$login_bg_color = $myHelpers->global_lib->get_option('login_bg_color');
	if($login_page_bg_type == 'color' && !empty($login_bg_color))
	{
		echo '.login-page { background-color:#'.$login_bg_color.';
							background-image: unset;
						  }';
	}
	else if($login_page_bg_type == 'image' && !empty($login_bg_image) && file_exists('../uploads/media/'.$login_bg_image))
	{
		echo '.login-page { background-image: url("'.base_url().'../uploads/media/'.$login_bg_image.'");
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
							background-image: url("'.base_url().'themes/default/images/login-bg.jpg");
						  }';
	}
	?>
</style>
</head>

<body class="hold-transition login-page">
	<div class="login-box">
      <div class="login-logo">
        <a href="#"><?php echo mlx_get_lang('Real Estate'); ?></a>
      </div>
      <div class="login-box-body">
        
		<?php 
		if(isset($_SESSION['msg']) & !empty($_SESSION['msg'])) { 
			echo $_SESSION['msg'];
			unset($_SESSION['msg']);
		}
		
		?>
			<?php 	$attributes = array('name' => 'login_form');		 			
			echo form_open('logins/forgot_password',$attributes); ?>
			  <div class="form-group has-feedback">
				<input type="email" class="form-control" placeholder="Email" size="25" name="email" required>
				<span class="fa fa-envelope form-control-feedback"></span>
			  </div>
			  
			  
			  <div class="row">
				<div class="col-xs-8">
				  <a class="btn btn-default btn-flat" href="<?php echo base_url(array('logins','login')); ?>">Back to Login</a>
				</div>
				<div class="col-xs-4">
				  <button type="submit" name="submit" class="btn btn-primary btn-block btn-flat"><?php echo mlx_get_lang('Submit'); ?></button>
				</div>
			  </div>
			  
			  
			  <div class="social-auth-links text-center">
				  <p>- OR -</p>
				  <a href="<?php echo str_replace('admin/','',base_url()); ?>" class="btn btn-block btn-default btn-flat"><i class="fa fa-share"></i> &nbsp;Back to Website</a>
			  </div>
			</form>
		
      </div>
    </div>
	
	
<?php 
echo script_tag("themes/$theme/plugins/jQuery/jQuery-2.1.4.min.js");
echo script_tag("themes/$theme/bootstrap/js/bootstrap.min.js");
?>
</body>
</html>
