<?php $admin_url =  site_url();
	  $site_url = str_replace("/admin","",$admin_url);	

$user_id = $this->session->userdata('user_id');
$user_type = $this->session->userdata('user_type');


$website_logo_text = get_option('website_logo_text');
$website_logo = 	get_option('website_logo');

?>

<header class="main-header">
        
		<a href="<?php echo $admin_url."admin/";?>" class="logo">
          <?php if(!empty($website_logo) || !empty($website_logo_text)){ ?>
				
				<?php if ( !empty($website_logo) && file_exists('uploads/media/' . $website_logo)) { ?>
					<img class="logo-img" src="<?php echo site_url() . 'uploads/media/' . $website_logo; ?>" 
						alt="<?php echo $website_logo_text; ?>">
				<?php } else if (isset($website_logo_text)) {
					echo '<strong">' . $website_logo_text . '</strong>';
				}
				?>
				
		 <?php }else {?>
			  <span class="logo-mini"><?php echo mlx_get_lang('R E'); ?></span>
			  <span class="logo-lg"><?php echo mlx_get_lang('Real Estate'); ?></span>
		  <?php } ?>
        </a>
		
		
        <nav class="navbar navbar-static-top" role="navigation">
          <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only"><?php echo mlx_get_lang('Toggle Navigation'); ?></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
            
			  <li class=" user user-menu">
                <a href="<?php echo $site_url; ?>" 
					id="view_front_site"
					class="btn btn-flat" target="_blank"><?php echo mlx_get_lang('View Site'); ?></a>
			  </li>
        <?php 
		
		/*print_r($CI);*/
		if( $CI->site_payments == 'Y' && $this->session->userdata('user_type') != 'admin' && 0){?>
        <!-- credit section start -->
        <?php $this->load->view("$theme/header-top-credits-bar" );?>
        <?php } ?>
         <!-- credit section end -->
			  <?php 
				$query = "select * from notifications 
				where notif_status = 'U' and notif_for = '$user_id'
				order by notif_id DESC
				limit 12";
				$notif_result = $myHelpers->Common_model->commonQuery($query);
			  ?>
			  
			  <li class="dropdown notifications-menu sd_menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <i class="fa fa-bell-o"></i>
                  <span class="label label-warning bg-chilli-red ft-size-12 notif-count-top"><?php if(isset($notif_result) && $notif_result->num_rows() > 0 ) { echo $notif_result->num_rows(); } ?></span>
                </a>
                <ul class="dropdown-menu"  >
                  <li class="header ft-size-16"><?php echo mlx_get_lang('You have'); ?> <span class="notif-count-bottom"><?php if(isset($notif_result)) { echo $notif_result->num_rows(); } else echo '0'; ?></span> <?php echo mlx_get_lang('notifications'); ?></li>
                  <?php if(isset($notif_result) && $notif_result->num_rows() > 0 ) {  ?>
				  
				  <li>
                    <ul class="menu" id="notifications" style="max-height:300px !important; overflow-x:hidden;overflow-y:auto;">
					  <?php foreach($notif_result->result() as $notif_row){ 
						  $url_text = '';
						  if($notif_row->prop_action != 'decline')
						  {
							  $url_text = 'href="'.site_url(array('property/view/'.$myHelpers->global_lib->EncryptClientId($notif_row->p_id))).'"';
						  }
					  ?>
						  <li data-notif_id="<?php echo $myHelpers->global_lib->EncryptClientId($notif_row->notif_id); ?>" style="cursor:pointer;" class="ft-size-16">
							<a <?php echo $url_text; ?>>
							  <i class="fa <?php echo $notif_row->notif_icon; ?> "></i> <span><?php echo $notif_row->notif_text ; ?></span>
							</a>
							
						  </li>
					  <?php } ?>
                    </ul>
                    
                  </li>
                  <?php } ?>
				  <li class="footer"><a style="cursor:pointer;" data-toggle="control-sidebar"><?php echo mlx_get_lang('View All'); ?></a></li>
                </ul>
              </li>
			  
			  <li class=" user user-menu">
                <a style="background:rgba(0,0,0,0.1);"><?php echo mlx_get_lang('Welcome'); ?> <strong><?php echo ucfirst($this->session->userdata('first_name')); ?></strong></a>
    		  </li>
              <li class=" user user-menu">
                <a href="<?php $segments = array('admin','logins','logout'); echo base_url($segments);?>" class="btn btn-flat "><?php echo mlx_get_lang('Sign Out'); ?></a>
			  </li>
            </ul>

          </div>
        </nav>
      </header>