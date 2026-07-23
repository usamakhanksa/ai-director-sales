
<div class="content-wrapper">
<section class="content-header">
  <h1 class="page-title"><i class="fa fa-file"></i> <?php echo mlx_get_lang('Manage Pages'); ?> </h1>
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
				
				<th width="30px"><?php echo mlx_get_lang('S.No.'); ?></th>
				<th><?php echo mlx_get_lang('Page Title'); ?></th>
				<th><?php echo mlx_get_lang('Created On'); ?></th>
				<th><?php echo mlx_get_lang('Updated On'); ?></th>
				<th><?php echo mlx_get_lang('Status'); ?></th>
				<th><?php echo mlx_get_lang('Action'); ?></th>
			  </tr>
			</thead>
			<tbody>
<?php  if ($query->num_rows() > 0)
{				
	$i=0;   
foreach ($query->result() as $row)
{ 
	$i++;
	
?>						
			  <tr>
			   
				<td><?php echo  $i; ?></td>
				<td> <?php echo ucfirst($row->page_title); ?></td>
				<td>
					<?php 
						echo date('M d, Y h:i A',$row->created_on); 
					?>
				</td>
				<td>
					<?php 
						echo date('M d, Y h:i A',$row->updated_on); 
					?>
				</td>
				<td> <?php if($row->page_status == 'Y') echo '<span class="label label-success">Active</span>'; 
					   else if($row->page_status == 'N') echo '<span class="label label-danger">In-Active</span>';
					   else echo '-';
				 ?>
				 </td>
				<td class="action_block">
					
					<a href="<?php $segments = array($row->page_slug); 
					echo str_replace('admin/','',base_url($segments)); ?>" title="View" target="_blank" class="btn btn-info btn-xs"><i class="fa fa-eye"></i></a>
					
					<a href="<?php $segments = array('admin','page','edit',$myHelpers->EncryptClientId($row->page_id)); 
					echo site_url($segments);?>" title="<?php echo mlx_get_lang('Edit'); ?>" class="btn btn-warning btn-xs"><i class="fa fa-edit"></i></a>
					
					<a href="<?php $segments = array('admin','page','delete',$myHelpers->EncryptClientId($row->page_id)); 
					echo site_url($segments);?>" title="<?php echo mlx_get_lang('Delete'); ?>" class="btn btn-danger btn-xs"><i class="fa fa-remove"></i></a>
					
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