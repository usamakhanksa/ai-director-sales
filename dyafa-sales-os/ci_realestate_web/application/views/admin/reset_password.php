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
<title><?php echo mlx_get_lang('Reset Password'); ?> | <?php echo mlx_get_lang('Real Estate'); ?></title>
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
			echo form_open('logins/reset_password',$attributes); ?>
			
			  <input type="hidden" name="user_id" value="<?php if(isset($user_id)) echo $user_id;  ?>">
			  
			  <div class="form-group has-feedback">
				<input type="password" class="form-control" placeholder="<?php echo mlx_get_lang('Password'); ?>" id="password" name="password" required pattern="^\S{5,}$" onchange="this.setCustomValidity(this.validity.patternMismatch ? '<?php echo mlx_get_lang('Must have at least 5 characters'); ?>' : ''); if(this.checkValidity()) form.repeat_password.pattern = this.value;">
				<span class="fa fa-lock form-control-feedback"></span>
			  </div>
			  
			   <div class="form-group has-feedback">
				<input type="password" class="form-control" placeholder="<?php echo mlx_get_lang('Repeat Password'); ?>" id="repeat_password" name="repeat_password" pattern="^\S{5,}$" required onchange="this.setCustomValidity(this.validity.patternMismatch ? '<?php echo mlx_get_lang('Please enter the same Password as above'); ?>' : '');">
				<span class="fa fa-lock form-control-feedback"></span>
			  </div>
			  
			  <div class="row">
				<div class="col-xs-8">
				  <a class="btn btn-default btn-flat" href="<?php echo base_url(array('logins','login')); ?>"><?php echo mlx_get_lang('Back to Login'); ?></a>
				</div>
				<div class="col-xs-4">
				  <button type="submit" name="submit" class="btn btn-primary btn-block btn-flat"><?php echo mlx_get_lang('Submit'); ?></button>
				</div>
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
