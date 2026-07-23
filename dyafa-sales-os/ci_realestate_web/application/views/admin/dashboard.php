<?php $user_type = $this->session->userdata('user_type'); ?>

<style >

.row.sub-row {
    margin-top: 25px;
}

.info-box-content{
	margin-left: 0px;
}

.row.sub-row  .dashboard-widget {
    margin-bottom: 25px;
}

.info-box-text {
    text-transform: uppercase;
    font-size: 16px;
    
    padding: 15px 10px;
   /* margin-top: -30px;*/
}


.dashboard-widget.bg-green1 .info-box-text{
	background: #00a65a;
	color : #ffffff;
}

.dashboard-widget.bg-orange1 .info-box-text{
	background: #ff851b;
	color : #ffffff;
}

.dashboard-widget.bg-red1 .info-box-text{
	background: #dd4b39;
	color : #ffffff;
}

.dashboard-widget.bg-purple1 .info-box-text{
	background: #605ca8;
	color : #ffffff;
}

.dashboard-widget.bg-blue1 .info-box-text{
	background: #0073b7;
	color : #ffffff;
}

.dashboard-widget.bg-maroon1 .info-box-text{
	background: #d81b60;
	color : #ffffff;
}

.info-box-number{
	padding-left: 10px;
}

.welcome-columnS {
	border: none;
	-webkit-box-shadow: 0 0 35px 0 rgba(154,161,171,.15);
	box-shadow: 0 0 35px 0 rgba(154,161,171,.15);
	position: relative;
	background-color: #fff;
	border-radius: .25rem;
	padding: 2.5rem 1.5rem;
	font-size: 2rem;
	line-height: 1.1;
	color: #6c757d;
	position:relative;
	font-weight:600;
}


</style>
<div class="content-wrapper">

	<section class="content-header">
		<?php if (isset($inactive_agents) && $inactive_agents->num_rows() > 0) { ?>
			<div class="alert alert-warning alert-dismissable show_always">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
				<?php echo $inactive_agents->num_rows(); ?> <?php echo mlx_get_lang('Agent is in-active'); ?>. <a href="<?php echo base_url(array('user', 'manage')); ?>"><?php echo mlx_get_lang('Click Here'); ?></a> <?php echo mlx_get_lang('to see the list'); ?>.
			</div>
		<?php } ?>
		<h1 class="page-title"><i class="fa fa-dashboard"></i> <?php echo mlx_get_lang('Dashboard'); ?></h1>
		<?php 	do_action("cms_notifications");		?>
	</section>

	<section class="content dashboard-content">
			
		
		
		<?php 			do_action("cms_dashboard_heading_row");			?>
		
		
		<?php 			do_action("cms_dashboard_after_heading_row");			?>
		
		
		<div class="row pre-widget-row">
		
		</div>
		
		<?php 			do_action("cms_dashboard_pre_widget_row");			?>
		
		<div class="row sub-row">
		
		
			<div class="col-md-3 col-sm-6 col-xs-12 hide " >
			  <div class="info-box">
				<span class="info-box-icon"><i class="fa fa-building bg-blue"></i></span>
				<div class="info-box-content py-4 ">
				  
				  <span class="info-box-textSS mt-2">
				  <a href="<?=base_url("admin/property/add_new");?>"><?php echo mlx_get_lang('Add Property'); ?></a></span>
				  
				</div>
			  </div>
			</div>
		
		
			<?php if (isset($user_widgets)) {

				/*echo "<pre>"; print_r($user_widgets); echo "</pre>";*/

	
				if(isset($user_widgets['widget_sections'])){
					$widget_sections = $user_widgets['widget_sections'];
					unset($user_widgets['widget_sections']);
					
					/*echo "<pre>"; print_r($widget_sections); echo "</pre>";*/
				}

				foreach ($user_widgets as $w_key => $user_widget) {
					//print_r($user_widget);
					if (!isset($user_widget['widget_callback']))
						$user_widget['widget_callback'] = '';
					if (isset($user_widget['widget_path'])  && isset($user_widget['widget_callback'])) {


						if (cms_file_exists($theme . "/" . $user_widget['widget_path']))
							$this->load->view($theme . "/" . $user_widget['widget_path'], $user_widget);
						else {
							/*if(cms_file_exists($user_widget['widget_path']))*/
							$this->load->view($user_widget['widget_path'], $user_widget);
						}
					}
				}
			} ?>
		</div>
		<div class="row full-row">
		<?php if (isset($widget_sections)) {	
				
				foreach ($widget_sections as $w_key => $user_widget) {
					if (!isset($user_widget['widget_callback']))
						$user_widget['widget_callback'] = '';
					if (isset($user_widget['widget_path'])) {


						if (cms_file_exists($theme . "/" . $user_widget['widget_path']))
							$this->load->view($theme . "/" . $user_widget['widget_path'], $user_widget);
						else {
							/*if(cms_file_exists($user_widget['widget_path']))*/
							$this->load->view($user_widget['widget_path'], $user_widget);
						}
					}
				}
			
			}
		?>
		</div>
	</section>

</div>

<?php

//$this->load->view(."/dashboard_popup");

do_action("admin_footer_scripts", "dashboard_updates");

?>