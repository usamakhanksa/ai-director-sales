

<div class="content-wrapper">
<section class="content-header">
  <h1 class="page-title"><i class="fa fa-credit-card"></i> <?php echo mlx_get_lang('Manage Packages'); ?>  
  <a href="<?php echo base_url('admin/packages/add_new');?>" class="btn btn-<?php echo $myHelpers->global_lib->get_skin_class(); ?> pull-right content-header-right-link"><?php echo mlx_get_lang('Add New'); ?></a></h1>
  

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
				<th><?php echo mlx_get_lang('Package Name'); ?></th>
				<th><?php echo mlx_get_lang('Price'); ?></th>
				<th><?php echo mlx_get_lang('Package Life'); ?></th>
				<th><?php echo mlx_get_lang('Applicable For'); ?></th>
				<th><?php echo mlx_get_lang('Order'); ?></th>
				<!-- <th><?php echo mlx_get_lang('Is Default?'); ?></th> -->
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
				<td>
					<?php
						echo $row->package_name;
					?>
				</td>
				<td> <?php 
					$args = array("currency_symbol"=>$myHelpers->global_lib->get_currency_symbol($row->package_currency));
					echo $myHelpers->global_lib->moneyFormatDollar($row->package_price,$args); 
				?></td>
				<td> <?php 
				if($row->package_life == '0 days' || $row->package_life == '0 weeks' || $row->package_life == '0 months' || $row->package_life == '0 years'){
					echo 'Unlimited';
				}else{
				echo ucfirst($row->package_life);
				}
				?></td>
				<td> <?php echo ucwords(str_replace(',',', ',$row->applicable_for)); ?></td>
				<td> <?php echo ucfirst($row->package_order); ?></td>
				<!-- <td> <?php if($row->is_default == 'Y') echo '<span class="label label-success">Yes</span>'; 
					   else if($row->is_default == 'N') echo '<span class="label label-danger">No</span>';
					   else echo '-';
				 ?>
				 </td> -->
				<td class="action_block">
					
					<a href="<?php $segments = array('admin','packages','edit',$myHelpers->EncryptClientId($row->package_id)); 
					echo site_url($segments);?>" title="<?php echo mlx_get_lang('Edit'); ?>" class="btn btn-warning btn-xs"><i class="fa fa-edit"></i></a>
					
					<a href="<?php $segments = array('admin','packages','delete',$myHelpers->EncryptClientId($row->package_id)); 
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