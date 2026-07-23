<?php $user_type = $this->session->userdata('user_type'); ?>

<div class="content-wrapper">

	<section class="content-header">
		
	  <h1 class="page-title"><i class="fa fa-google"></i> <?php echo mlx_get_lang('Google Analystic Dashboard'); ?></h1>
	</section>
	
	<section class="content">
		
		<div class="row">
			<div class="col-md-8">
			<?php $this->load->view("google_analytics/widgets/hits_by_users");?>			
			</div>
				
			<?php $this->load->view("google_analytics/widgets/visitors_map");?>
		</div>		
		<?php $this->load->view("google_analytics/widgets/general_stats");?>		
		
		<div class="row">	
			<div class="col-md-8">
			<?php 
				$this->load->view("google_analytics/widgets/hits_browser_os");
			?>				
			</div>
				
			<div class="col-md-4">
				<?php $this->load->view("google_analytics/widgets/browser_sessions");?>
			</div>
		</div>
		
			<?php //} ?>
	</section>

  </div>
<?php 

echo script_tag("application/views/admin/assets/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js");
echo script_tag("application/views/admin/assets/plugins/jvectormap/jquery-jvectormap-world-mill-en.js");
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<?php echo script_tag("application/views/admin/assets/plugins/morris/morris.min.js");?>
  
  
     