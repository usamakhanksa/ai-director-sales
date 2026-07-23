  <?php $this->load->view("default/header-top");?>
  
  <?php $this->load->view("default/sidebar-left");?>
  
<div class="content-wrapper">
	<section class="content-header">
	  <h1 class="page-title"><i class="fa fa-server"></i> <?php echo mlx_get_lang('Manage Badge'); ?> </h1>
	  <?php if(isset($_SESSION['msg']) && !empty($_SESSION['msg']))
			{
				echo $_SESSION['msg'];
				unset($_SESSION['msg']);
			}
		?>
	</section>

	<section class="content">

	  <div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?>">
		
		
		
		<div class="box-body content-box">
			
			  <table id="example2" class="table table-bordered table-hover datatable-element-scrollx">
				<thead>
				  <tr>
					
					<th width="75px" class="pad-right-5" ><?php echo mlx_get_lang('S No.'); ?></th>
					<th class="pad-right-5"><?php echo mlx_get_lang('Titles'); ?></th>
					<th class="pad-right-5"><?php echo mlx_get_lang('Badges'); ?></th>
					<th class="pad-right-5"><?php echo mlx_get_lang('Descriptions'); ?></th>
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
					<td><?php echo  $row->title; ?></td>
					
					<td>
						<?php if(!empty($row->image) && file_exists('../uploads/badge/'.$row->image)) { ?>
						<img width="200px" src="<?php echo base_url().'../uploads/badge/'.$row->image; ?>">
						<?php } ?>
					</td>
					<td><?php echo  $row->short_description; ?></td>
					<td> 
						<?php if($row->status == 'Y') echo '<span class="label label-success">'.mlx_get_lang("Active").'</span>'; 
						   else if($row->status == 'N') echo '<span class="label label-danger">'.mlx_get_lang("In-Active").'</span>';
						   else echo '-';
						?>
					 </td>
					<td class="action_block">
						
						<a href="<?php $segments = array('badge','edit',$myHelpers->global_lib->EncryptClientId($row->badge_id)); 
						echo site_url($segments);?>" title="<?php echo mlx_get_lang('Edit'); ?>" data-toggle="tooltip" class="btn btn-warning btn-xs"><i class="fa fa-edit "></i></a>
						
						<a href="<?php $segments = array('badge','delete',$myHelpers->global_lib->EncryptClientId($row->badge_id)); 
						echo site_url($segments);?>" title="<?php echo mlx_get_lang('Delete'); ?>" data-toggle="tooltip" class="btn btn-danger btn-xs"><i class="fa fa-trash "></i></a>
						
					</td>
				  </tr>
<?php 	}
}	?>                      
				  
				 
				 
				 
				</tbody>
				
			  </table>
			</div>
	  </div>
	</section>
  </div>
   