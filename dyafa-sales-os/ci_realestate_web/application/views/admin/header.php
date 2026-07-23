<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="author" content="">
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
    <title><?php ?></title>
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    
<?php

$fevicon_icon = get_option('fevicon_icon');
if(isset($fevicon_icon) && !empty($fevicon_icon) && file_exists('uploads/media/'.$fevicon_icon) )
	echo '<link rel="shortcut icon" href="'.base_url().'uploads/media/'.$fevicon_icon.'">';
else
	echo '<link rel="shortcut icon" href="'.base_url().'application/views/'.$theme.'/assets/images/fav.png">';



echo link_tag("application/views/$theme/assets/bootstrap/css/bootstrap.min.css");
echo link_tag("application/views/$theme/assets/css/font-awesome.min.css");
echo link_tag("application/views/$theme/assets/css/flag-icon.min.css");
echo link_tag("application/views/$theme/assets/plugins/daterangepicker/daterangepicker-bs3.css");
echo link_tag("application/views/$theme/assets/plugins/datepicker/datepicker3-min.css");
echo link_tag("application/views/$theme/assets/plugins/iCheck/all.css");
echo link_tag("application/views/$theme/assets/plugins/select2/select2.min.css");
echo link_tag("application/views/$theme/assets/css/magnific-popup.min.css");
echo link_tag("application/views/$theme/assets/css/site.css");
echo link_tag("application/views/$theme/assets/plugins/datatables/dataTables.bootstrap.css");
echo link_tag("application/views/$theme/assets/plugins/ckeditor/contents.css");
echo link_tag("application/views/$theme/assets/plugins/nestable/style.css");
echo link_tag("application/views/$theme/assets/plugins/jvectormap/jquery-jvectormap-1.2.2.css");
echo link_tag("application/views/$theme/assets/plugins/morris/morris.css");

$user_id = $this->session->userdata('user_id');
$direction = get_user_meta($user_id,'direction');
if(empty($direction))
{
	$direction = $CI->site_direction;
	/*	$direction = $this->global_lib->get_option('direction');*/
}
if(!empty($direction) && $direction == 'rtl')
{
	echo link_tag("application/views/$theme/assets/css/bootstrap-rtl.min.css");
	echo link_tag("application/views/$theme/assets/css/AdminLTE.min-rtl.css");
}
else 
{
	echo link_tag("application/views/$theme/assets/css/AdminLTE.min.css");
}

echo link_tag("application/views/$theme/assets/css/skins/_all-skins.min.css");
echo link_tag("application/views/$theme/assets/custom-styles.css");

echo script_tag("application/views/$theme/assets/plugins/jQuery/jQuery-2.1.4.min.js");
echo script_tag("application/views/$theme/assets/plugins/jQuery/jquery-ui.min.js");
echo script_tag("application/views/$theme/assets/js/jquery.magnific-popup.js");
echo script_tag("application/views/$theme/assets/plugins/ckeditor/ckeditor.js");
echo script_tag("application/views/$theme/assets/plugins/dompurify/0.8.4/purify.min.js");
echo script_tag("application/views/$theme/assets/plugins/datatables/jquery.dataTables.min.js");
echo script_tag("application/views/$theme/assets/plugins/datatables/dataTables.bootstrap.min.js");
echo script_tag("application/views/$theme/assets/plugins/lazy-load/jquery.lazy.min.js");

echo script_tag("application/views/$theme/assets/custom-scripts.js");


global $def_skin;
$def_skin = 'skin-blue';
$skin = get_option('skin');
if(!empty($skin))
{
	$def_skin = $skin;
}
?>
	<!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
<script type="text/javascript">
    var base_url = "<?php echo base_url(); ?>";
    function site_url(url){
        var bu = "<?php echo base_url(); ?>admin/";
        url = (url)?url:"";
        return bu + "index.php/" + url;
    }
</script>


<?php	do_action("cms_admin_header");	?>

<style >


.main-header .logo img.logo-img {
	max-width: 100%;
    padding: 5px 10px;
	height: 100%;
}

</style>

  </head>
  <body class="<?php echo $def_skin; ?> fixed sidebar-mini">
	<div class="wrapper">
	<?php $this->load->view("$theme/header-top");?>
	<?php $this->load->view("$theme/sidebar-left");?>
	<?php $this->load->view($content);?>
	<?php $this->load->view("$theme/footer");?>
	<div class="model_wrapper"></div>
   </div>	

<?php	do_action("cms_admin_footer");	?>

  </body>

</html>