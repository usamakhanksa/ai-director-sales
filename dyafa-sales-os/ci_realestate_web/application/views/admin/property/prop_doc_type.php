<?php 
$user_type = $this->session->userdata('user_type');
?>

      <div class="content-wrapper">
        <section class="content-header">
          <h1 class="page-title"><i class="fa fa-sitemap"></i> <?php echo mlx_get_lang('Manage Property Docment Types'); ?> </h1>
		  <?php if(isset($_SESSION['msg']) && !empty($_SESSION['msg']))
				{
					echo $_SESSION['msg'];
					unset($_SESSION['msg']);
				}
			?>
        </section>

        <section class="content">
			<div class="row">
				<div class="col-md-4 col-md-push-8">
					<?php
					$attributes = array('name' => 'add_form_post','class' => 'form');		 			
					echo form_open_multipart('property/doc_type',$attributes); ?>
						<input type="hidden" name="doc_type_id" class="doc_type_id" value="<?php if(isset($doc_type_id) && !empty($doc_type_id)) echo $myHelpers->EncryptClientId($doc_type_id); ?>">
						<div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?>">
							<div class="box-header with-border">
								<?php if(isset($doc_type_id) && !empty($doc_type_id)){ ?>
									<h3 class="box-title"><?php echo mlx_get_lang('Edit Property Document Type'); ?></h3>
								<?php }else{ ?>
									<h3 class="box-title"><?php echo mlx_get_lang('Add Property Document Type'); ?></h3>
								<?php } ?>
							</div>
							  <div class="box-body">
								
								<div class="form-group">
								  <label for="title"><?php echo mlx_get_lang('Title'); ?> <span class="required">*</span></label>
								  <input type="text" class="form-control" required="required" name="title" id="title" 
								  value="<?php if(isset($title) && !empty($title)) echo $title; ?>">
								</div>
								
								<div class="form-group">
								  <label for="description"><?php echo mlx_get_lang('Description'); ?> </label>
								  <textarea class="form-control" name="description" id="description"><?php if(isset($description) && !empty($description)) echo $description; ?></textarea>
								</div>
								
								<div class="form-group">
									<label for="property_type_required"><?php echo mlx_get_lang('Is Required?'); ?></label>
									 <div class="radio_toggle_wrapper ">
										<input type="radio" checked="checked" id="required_y" value="Y" 
										name="is_required" class="toggle-radio-button" 
										<?php 
										  if((isset($is_required) && $is_required == 'Y'))
												echo ' checked="checked" ';
										  ?>>
										<label for="required_y"><?php echo mlx_get_lang('Yes'); ?></label>
										
										<input type="radio" id="required_n" value="N" name="is_required" 
										class="toggle-radio-button" 
										<?php 
										  if((isset($is_required) && $is_required == 'N')  || !isset($is_required))
												echo ' checked="checked" ';
										  ?>>
										<label for="required_n"><?php echo mlx_get_lang('No'); ?></label>
									</div>
								</div>
								
								<div class="form-group">
								  <label for="error_message"><?php echo mlx_get_lang('Error Message'); ?> </label>
								  <textarea class="form-control" name="error_message" id="error_message"><?php if(isset($error_message) && !empty($error_message)) echo $error_message; ?></textarea>
								</div>
								
								<div class="form-group">
								  <label for="pdt_order"><?php echo mlx_get_lang('Order'); ?> <span class="required">*</span></label>
								  <input type="number" min="0" step="1" class="form-control" required="required" name="pdt_order" id="pdt_order" 
								  value="<?php if(isset($pdt_order) && !empty($pdt_order)) echo $pdt_order; else echo '0'; ?>">
								</div>
								
								<div class="form-group">
									<label for="property_type_status"><?php echo mlx_get_lang('Status'); ?></label>
									 <div class="radio_toggle_wrapper ">
										<input type="radio" checked="checked" id="status_y" value="Y" 
										name="status" class="toggle-radio-button" 
										<?php 
										  if((isset($status) && $status == 'Y') || !isset($status))
												echo ' checked="checked" ';
										  ?>>
										<label for="status_y"><?php echo mlx_get_lang('Active'); ?></label>
										
										<input type="radio" id="status_n" value="N" name="status" 
										class="toggle-radio-button" 
										<?php 
										  if(isset($status) && $status == 'N')
												echo ' checked="checked" ';
										  ?>>
										<label for="status_n"><?php echo mlx_get_lang('In-Active'); ?></label>
									</div>
								</div>
								
							</div>
							<div class="box-footer">
								<button name="submit" type="submit" class="btn btn-<?php echo $myHelpers->global_lib->get_skin_class(); ?> pull-right" id="save_publish"><?php echo mlx_get_lang('Submit'); ?></button>
							  </div>
						  </div>
					</form>
				</div>
				<div class="col-md-8 col-md-pull-4">
					  <div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?>">
						
						<div class="box-body content-box">
							
							
							  <table id="example2" class="table table-bordered table-hover datatable-element-scrollx">
								<thead>
								  <tr>
									
									<th width="30px"><?php echo mlx_get_lang('S.No.'); ?></th>
									<th><?php echo mlx_get_lang('Title'); ?></th>
									<th><?php echo mlx_get_lang('Status'); ?></th>
									<th><?php echo mlx_get_lang('Order'); ?></th>
									<th><?php echo mlx_get_lang('Created On'); ?></th>
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
									<td> <?php echo ucfirst($row->title); ?></td>
									<td> <?php if($row->status == 'Y') echo '<span class="label label-success">Active</span>'; 
										   else if($row->status == 'N') echo '<span class="label label-danger">In-Active</span>';
										   else echo '-';
									 ?>
									</td>
									<td> <?php echo $row->pdt_order; ?></td>
									<td>
										<?php 
											echo date('M d, Y h:i A',$row->created_on); 
										?>
									</td>
									<td class="action_block">
										
										
										<a href="<?php $segments = array('property','doc_type',$myHelpers->EncryptClientId($row->pdt_id)); 
										echo site_url($segments);?>" title="Edit" data-toggle="tooltip" class="btn btn-warning btn-xs"><i class="fa fa-edit fa-2x"></i></a>
										
										<a href="<?php $segments = array('property','delete_doc_type',$myHelpers->EncryptClientId($row->pdt_id)); 
										echo site_url($segments);?>" title="Delete" data-toggle="tooltip" class="btn btn-danger  btn-xs delete-property"><i class="fa fa-trash fa-2x"></i></a>
										
									</td>
								  </tr>
			<?php 	}
				}	?>                      
								  
								 
								 
								 
								</tbody>
								
							  </table>
							
						</div>
					  </div><!-- /.box -->
				</div>
			</div>
          <!-- /.row -->

        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
