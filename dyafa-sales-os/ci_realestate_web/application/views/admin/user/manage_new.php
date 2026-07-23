  
<div class="content-wrapper">
	<section class="content-header">
	  <h1 class="page-title"><i class="fa fa-users"></i> <?php echo mlx_get_lang('Manage Users'); ?> </h1>
	</section>

	<section class="content">

	  <div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?>">
		
		<div class="box-body content-box">
			<?php if(isset($_SESSION['msg']) && !empty($_SESSION['msg']))
				{
					echo $_SESSION['msg'];
					unset($_SESSION['msg']);
				}
			?>
			  <!-- <table id="example2" class="table table-bordered table-hover datatable-element-scrollx">
				<thead>
				  <tr>
					
					<th width="75px" class="pad-right-5" ><?php echo mlx_get_lang('S No.'); ?></th>
					<th class="pad-right-5"><?php echo mlx_get_lang('Full Name'); ?></th>
					<th class="pad-right-5"><?php echo mlx_get_lang('Username'); ?></th>
					<th class="pad-right-5"><?php echo mlx_get_lang('User Type'); ?></th>
					<th class="pad-right-5" ><?php echo mlx_get_lang('Mobile No.'); ?></th>
					<th class="pad-right-5" ><?php echo mlx_get_lang('Email'); ?></th>
					<th><?php echo mlx_get_lang('Status'); ?></th>
					<th width="150px" class="pad-right-5" ><?php echo mlx_get_lang('Action'); ?></th>
				  </tr>
				</thead>
				<tbody>-->
				<div class="row">
<?php  if ($query->num_rows() > 0)
   {		
	$n=1;
	$site_users = $myHelpers->config->item("site_users");
	foreach ($query->result() as $row)
	{ 
	
		
?>				


					
					
				  <!--<tr>
				   <td><?php echo  $n++; ?></td>
					<td><?php echo  $myHelpers->global_lib->get_user_meta($row->user_id,'first_name').' '.
									$myHelpers->global_lib->get_user_meta($row->user_id,'last_name'); ?></td>
					<td><?php echo  $row->user_name; ?></td>
					<td> <?php 
						if(array_key_exists($row->user_type,$site_users))	
						{	$user = $site_users[$row->user_type];
							echo $user['title'];
						}else
							echo ucfirst($row->user_type); ?></td>
					<td><?php echo  $myHelpers->global_lib->get_user_meta($row->user_id,'mobile_no'); ?></td>
					<td><?php echo  $row->user_email; ?></td>
					<td> 
						<?php 
						   if($row->user_verified == 'N') echo '<span class="label label-info">'.mlx_get_lang("UnVerified").'</span>';
						   else if($row->user_status == 'Y') echo '<span class="label label-success">'.mlx_get_lang("Active").'</span>'; 
						   else if($row->user_status == 'N') echo '<span class="label label-danger">'.mlx_get_lang("InActive").'</span>';
						   else echo '-';
						?>
					 </td>
					<td class="action_block">
						
						<a href="<?php $segments = array('user','edit',$myHelpers->global_lib->EncryptClientId($row->user_id)); 
						echo site_url($segments);?>" title="<?php echo mlx_get_lang('Edit'); ?>" data-toggle="tooltip" class="btn btn-warning btn-xs"><i class="fa fa-edit fa-2x"></i></a>
						<a href="<?php $segments = array('user','delete',$myHelpers->global_lib->EncryptClientId($row->user_id)); 
						echo site_url($segments);?>" title="<?php echo mlx_get_lang('Delete'); ?>" data-toggle="tooltip" class="btn btn-danger btn-xs"><i class="fa fa-trash fa-2x"></i></a>
					</td>
				  </tr>-->
				  <div class="col-md-3">
				  <!-- Widget: user widget style 1 -->
				  <div class="box box-widget widget-user-2">
					<!-- Add the bg color to the header using any of the bg-* classes -->
					<div class="widget-user-header bg-yellow">
					  <div class="widget-user-image">
						<img class="img-circle" src="#" alt="User Avatar">
					  </div><!-- /.widget-user-image -->
					  <h3 class="widget-user-username"><?php echo  $myHelpers->global_lib->get_user_meta($row->user_id,'first_name').' '.
									$myHelpers->global_lib->get_user_meta($row->user_id,'last_name'); ?></h3>
					  <h5 class="widget-user-desc">
							<?php 
						if(array_key_exists($row->user_type,$site_users))	
						{	$user = $site_users[$row->user_type];
							echo $user['title'];
						}else
							echo ucfirst($row->user_type); ?>
					  </h5>
					</div>
					<div class="box-footer no-padding">
					  <ul class="nav nav-stacked">
						<li><a href="#">Joins on <span class="pull-right badge bg-blue">
							<?php echo  date('m-d-Y',$myHelpers->global_lib->get_user_meta($row->user_id,'user_registered_date'));?>
						</span></a></li>
						
						<li><a href="#">Subsription <span class="pull-right badge bg-blue"><?php $subscriptions =  $myHelpers->global_lib->get_user_meta($row->user_id,'subscription_credit');
							if(!empty($subscriptions)){ 
							echo date('m-d-Y',$subscriptions);
							}else{ echo 'N/A';
							}
						?></span></a></li>
						<li><a href="#">Property Posting <span class="pull-right badge bg-aqua">5 Remains</span></a></li>
						<li><a href="#">Featured Properties <span class="pull-right badge bg-green">2 Remains</span></a></li>
						<li><a href="#">Blog Posting <span class="pull-right badge bg-red">10 Remains</span></a></li>
						
						
					  </ul>
						<div class="social-blocks pads-20">
						<a class="btn btn-block btn-social btn-facebook" href="https://facebook.com/" target="_blank">
						<i class="fa fa-facebook"></i>  Facebook
						</a>
						
						<a class="btn btn-block btn-social btn-instagram">
						<i class="fa fa-instagram"></i> Instagram
					  </a>
						<a class="btn btn-block btn-social btn-linkedin">
						<i class="fa fa-linkedin"></i> LinkedIn
					  </a>
						<a class="btn btn-block btn-social btn-twitter">
						<i class="fa fa-twitter"></i> Twitter
					  </a>
					   </div>
                </div>
             
			 </div><!-- /.widget-user -->
            </div>
				  
				  
				  
<?php 	}
}	?>                      
				  
				 
				 
				 </div>
				<!--</tbody>
				
			  </table>-->
			</div>
	  </div><!-- /.box -->

	  <!-- /.row -->

	</section>
  </div>