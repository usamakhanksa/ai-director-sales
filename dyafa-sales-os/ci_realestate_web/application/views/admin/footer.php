<?php 
echo script_tag("application/views/$theme/assets/bootstrap/js/bootstrap.min.js");
echo script_tag("application/views/$theme/assets/plugins/select2/select2.full.min.js");
echo script_tag("application/views/$theme/assets/plugins/moment/moment.js");
echo script_tag("application/views/$theme/assets/plugins/daterangepicker/daterangepicker.js");
echo script_tag("application/views/$theme/assets/plugins/datepicker/bootstrap-datepicker.js");
echo script_tag("application/views/$theme/assets/plugins/iCheck/icheck.min.js");
echo script_tag("application/views/$theme/assets/plugins/slimScroll/jquery.slimscroll.min.js");
echo script_tag("application/views/$theme/assets/plugins/jscolor/jscolor.js");

echo script_tag("application/views/$theme/assets/plugins/stickySidebar/stickySidebar.js");	
echo script_tag("application/views/$theme/assets/js/app.min.js");
echo script_tag("application/views/$theme/assets/js/jquery.magnific-popup.min.js");
echo script_tag("application/views/$theme/assets/plugins/nestable/jquery.nestable.js");

echo script_tag("application/views/$theme/assets/plugins/validation/jquery.validate.min.js");
echo script_tag("application/views/$theme/assets/plugins/validation/additional-methods.min.js");
/*echo script_tag("application/views/$theme/assets/plugins/validation/jquery-validate.bootstrap-tooltip.min.js");*/
echo script_tag("application/views/$theme/assets/validation-scripts.js");



echo script_tag("application/views/$theme/assets/plugins/plupload/plupload.full.min.js");
echo script_tag("application/views/$theme/assets/plugins/plupload/gallery-uploader.js");
echo script_tag("application/views/$theme/assets/plugins/plupload/image-uploader.js");
echo script_tag("application/views/$theme/assets/plugins/plupload/multi-image-uploader.js");
echo script_tag("application/views/$theme/assets/plugins/plupload/zip-uploader.js");
echo script_tag("application/views/$theme/assets/plugins/plupload/document-uploader.js");
echo script_tag("application/views/$theme/assets/plugins/plupload/property-image-uploader.js");
echo script_tag("application/views/$theme/assets/plugins/plupload/property-document-uploader.js");

echo script_tag("application/views/$theme/assets/plugins/nestable/menu.js");

?>


<a href="#" id="back-to-top" class="no-print" title="Back to top"><i class="fa fa-chevron-up"></i></a>

<footer class="main-footer no-print">
<div class="text-right hidden-xs">
  <strong><?php echo mlx_get_lang('Version'); ?></strong> <?php
  echo  $this->cms_version;
  ?>
</div>
</footer>


<div class="full_sreeen_overlay">
	<span ><?php echo mlx_get_lang('Please Wait'); ?> ...</span>
</div>

<aside class="control-sidebar control-sidebar-dark hide ">
<ul class="nav nav-tabs nav-justified control-sidebar-tabs">
  <li class="active"><a href="#control-sidebar-home-tab" data-toggle="tab" aria-expanded="true"><i class="fa fa-bell"></i></a></li>
  <li><a class="hide_right_sidebar" style="cursor:pointer;"><i class="fa fa-remove text-red" ></i></a></li>
</ul>
<div class="tab-content">
  <div class="tab-pane active" id="control-sidebar-home-tab">
	
		<h3 class="control-sidebar-heading" style="margin-top:0px;"><?php echo mlx_get_lang('All Notifications'); ?></h3>
		<?php 
			$user_id = $this->session->userdata('user_id');
			$query = "select * from notifications 
			where notif_for = $user_id
			order by notif_id DESC";
			$notif_result = $myHelpers->Common_model->commonQuery($query);
		 if($notif_result->num_rows() > 0) {
		 ?>
		<div class="scrollable_tab">
		<ul class="control-sidebar-menu">
		  <?php foreach($notif_result->result() as $notif_row){ 
			  $url_text = '';
			  if($notif_row->prop_action != 'reject')
			  {
				  $url_text = 'href="'.site_url(array('property/view/'.$myHelpers->global_lib->EncryptClientId($notif_row->p_id))).'"';
			  }
			  $notif_icon_color = 'bg-yellow';
			  if($notif_row->prop_action == 'complete')
				  $notif_icon_color = 'bg-yellow';
			  else if($notif_row->prop_action == 'approve')
				  $notif_icon_color = 'bg-green';
			  else if($notif_row->prop_action == 'reject')
				  $notif_icon_color = 'bg-red ';
		  ?>
			  <li data-notif_id="<?php echo $myHelpers->global_lib->EncryptClientId($notif_row->notif_id); ?>" style="cursor:pointer;" class="ft-size-16">
				<a <?php echo $url_text; ?>>
				  <i class="menu-icon fa <?php echo $notif_row->notif_icon; ?> <?php echo $notif_icon_color; ?>" style="color:#fff !important;"></i> 
					<div class="menu-info">
						<h4 class="control-sidebar-subheading"><?php echo $notif_row->notif_text ; ?></h4>
						<p><?php echo $myHelpers->global_lib->relativeTime($notif_row->notif_on); ?></p>
					  </div>
			      <i data-notif_id="<?php echo $myHelpers->global_lib->EncryptClientId($notif_row->notif_id); ?>" class="fa fa-trash remove_property_notif text-red"></i>
				</a>
				
			  </li>
		  <?php } ?>
		</ul>
		</div>
		<?php } ?>
	
  </div>
  
  
</div>
</aside>
<div class="control-sidebar-bg"></div>