<?php $user_type = $this->session->userdata('user_type');

?>
<div class="content-wrapper">
	<section class="content-header">
	  <h1 class="page-title"><i class="fa fa-users"></i> 
	  <?php if (isset($page_heading)) 
					echo mlx_get_lang($page_heading);
			  else 
					echo mlx_get_lang('Manage Users'); ?>
	  
	  
	  <?php
			if($myHelpers->has_menu_access("users||add_new_user" , $user_type))
			{  ?>
   	  <a href="<?php echo base_url(array('admin','user', 'add_new')); ?>" 
				class="btn btn-<?php echo get_skin_class(); ?> pull-right content-header-right-link">Add New User</a>
				
			<?php  } ?>	
	  
	  
	  
	  </h1>
	  
	  <?php if(isset($_SESSION['msg']) && !empty($_SESSION['msg']))
			{
				echo $_SESSION['msg'];
				unset($_SESSION['msg']);
			}
		?>
		
		
	</section>

	<section class="content">

	  <div class="box box-<?php echo get_skin_class(); ?>">
		
		<div class="box-body content-box">
			
			  <table id="example2" class="table table-bordered table-hover datatable-element-scrollx">
				<thead>
				  <tr>
					
					<th width="75px" class="pad-right-5" ><?php echo mlx_get_lang('S No.'); ?></th>
					<th class="pad-right-5"><?php echo mlx_get_lang('Full Name'); ?></th>
					<th class="pad-right-5"><?php echo mlx_get_lang('Username'); ?></th>
					<th class="pad-right-5"><?php echo mlx_get_lang('User Type'); ?></th>
					<th class="pad-right-5" ><?php echo mlx_get_lang('Mobile No.'); ?></th>
					<th class="pad-right-5" ><?php echo mlx_get_lang('Email'); ?></th>
					<?php do_action("cms_manage_users_table_header_after");?>
					<th><?php echo mlx_get_lang('Status'); ?></th>
					<th width="150px" class="pad-right-5" ><?php echo mlx_get_lang('Action'); ?></th>
				  </tr>
				</thead>
				<tbody>
<?php  if ($query->num_rows() > 0)
   {		
	$n=1;
	$site_users = $myHelpers->config->item("site_users");
	foreach ($query->result() as $row)
	{ 
	
		
?>						
				  <tr>
				   <td><?php echo  $n++; ?></td>
					<td><?php echo  get_user_meta($row->user_id,'first_name').' '.
									get_user_meta($row->user_id,'last_name'); ?></td>
					<td><?php echo  $row->user_name; ?></td>
					<td> <?php 
					
						
					
						if(array_key_exists($row->user_type,$site_users))	
						{	$user = $site_users[$row->user_type];
							echo $user['title'];
						}else 
						{
							echo ucfirst($row->user_type); 
						}	
							?></td>
					<td><?php echo  get_user_meta($row->user_id,'mobile_no'); ?></td>
					<td><?php echo  $row->user_email; ?></td>
					<?php do_action("cms_manage_users_table_data_after" , $row);?>
					<td> 
						<?php 
						   if($row->user_verified == 'N') echo '<span class="label label-info">'.mlx_get_lang("UnVerified").'</span>';
						   else if($row->user_status == 'Y') echo '<span class="label label-success">'.mlx_get_lang("Active").'</span>'; 
						   else if($row->user_status == 'N') echo '<span class="label label-danger">'.mlx_get_lang("InActive").'</span>';
						   else echo '-';
						?>
					 </td>
					<td class="action_block">
						
						<?php 
						$data['user'] = $row; 
						$data['myHelpers'] = $myHelpers;
						do_action("cms_manage_users_action_before" ,$data);?>
					<?php 
					
					
					
					
						if($myHelpers->has_menu_access("user||edit",$user_type))
						{
					?>
						<a href="<?php $segments = array('admin','user','edit',EncryptClientID($row->user_id)); 
						echo site_url($segments);?>" title="<?php echo mlx_get_lang('Edit'); ?>" data-toggle="tooltip" class="btn btn-warning btn-xs"><i class="fa fa-edit fa-2x"></i></a>
						
						
					<?php 
					
						
						}
					?>
					
					
					
					<?php 
					
						if($myHelpers->has_menu_access("user||delete",$user_type))
						{
					?>
						
						<a href="<?php $segments = array('admin','user','delete',EncryptClientID($row->user_id)); 
						echo site_url($segments);?>" title="<?php echo mlx_get_lang('Delete'); ?>" data-toggle="tooltip" class="btn btn-danger btn-xs"><i class="fa fa-trash fa-2x"></i></a>
					
					
					
					<?php 
					
						
						}
					?>
					
					
						<?php do_action("cms_manage_users_action_after" , $data);?>
						
					
					</td>
				  </tr>
<?php 	}
}	?>                      
				  
				 
				 
				 
				</tbody>
				
			  </table>
			</div>
	  </div><!-- /.box -->

	  <!-- /.row -->

	</section>
  </div>
  
<?php 
	
	$this->load->view("/admin/user/user_modal_popup");	
?>

<?php do_action("admin_footer_scripts", "cms_manage_users"); ?>