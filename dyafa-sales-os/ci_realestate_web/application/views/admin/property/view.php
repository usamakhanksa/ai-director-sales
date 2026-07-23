<?php 
$user_type = $this->session->userdata('user_type');
$user_id = $this->session->userdata('user_id');
?>

<script>
$(document).ready(function() {
	
	$('button[name="reject"]').click(function() {
		if($(this).hasClass('not-submitted'))
		{
			$('#rejectModal').modal('show');
			return false;
		}
	});
	
	$('.reject_form_submit').submit(function() {
		var reject_message = $('#rejectModal').find('.reject_message').val();
		$('<input>').attr({
			type: 'hidden',
			name: 'reject_message',
		}).val(reject_message).appendTo('form.form');
		$('button[name="reject"]').removeClass('not-submitted');
		$('button[name="reject"]').attr('type','submit');
		$('button[name="reject"]').trigger('click');
		return false;
	});
});
</script>
<style>
a > img{
	outline:0 none;
}
.product-document-container .document_images_inner a{
	display: inline-block;
	width: 100%;
	height: 100%;
}
</style>
      

      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1> <?php echo mlx_get_lang('View Property'); ?></h1>
          
        </section>

		<section class="content">
			<div class="invoice">
          <?php 
			$attributes = array('name' => 'add_form_post','class' => 'form');		 			
			echo form_open_multipart('',$attributes); ?>
			<input type="hidden" name="id" class="pid" value="<?php echo EncryptClientID($single_prop->id); ?>">
			
          <div class="row">
            <div class="col-xs-12">
              <h2 class="page-header">
                <i class="fa fa-building"></i> <?php echo ucfirst($single_prop->title); ?>
                <small class="pull-right"><?php echo mlx_get_lang('Date'); ?>: <?php echo date('m/d/Y',time()); ?></small>
              </h2>
            </div><!-- /.col -->
          </div>
          
          <div class="row">
            <div class="col-xs-12">
              <p class="lead no-margin" style="margin-bottom:10px !important;"><?php echo mlx_get_lang('Property Details'); ?></p>
              <div class="row">
				<div class="col-xs-3 text-right">
					<strong><?php echo mlx_get_lang('Title'); ?></strong>
				</div>
				<div class="col-xs-8 col-xs-offset-1">
					<?php echo ucfirst($single_prop->title); ?>
				</div>
			  </div>
			  
			   <div class="row">
				<div class="col-xs-3 text-right">
					<strong><?php echo mlx_get_lang('Short Description'); ?></strong>
				</div>
				<div class="col-xs-8 col-xs-offset-1">
					<?php echo $single_prop->short_description; ?>
				</div>
			  </div>
			  
			  <div class="row">
				<div class="col-xs-3 text-right">
					<strong><?php echo mlx_get_lang('Description'); ?></strong>
				</div>
				<div class="col-xs-8 col-xs-offset-1">
					<?php echo $single_prop->description; ?>
				</div>
			  </div>
			  
			  <div class="row">
				<div class="col-xs-3 text-right">
					<strong><?php echo mlx_get_lang('Property Type'); ?></strong>
				</div>
				<div class="col-xs-8 col-xs-offset-1">
					<?php echo ucfirst($single_prop->prop_type_title); ?>
				</div>
			  </div>
			  
			  <div class="row">
				<div class="col-xs-3 text-right">
					<strong><?php echo mlx_get_lang('Property For'); ?></strong>
				</div>
				<div class="col-xs-8 col-xs-offset-1">
					<?php echo ucfirst($single_prop->property_for); ?>
				</div>
			  </div>
			  
			  <div class="row">
				<div class="col-xs-3 text-right">
					<strong><?php echo mlx_get_lang('Property Price'); ?></strong>
				</div>
				<div class="col-xs-8 col-xs-offset-1">
					<?php echo $currency_symbol; ?><?php echo $myHelpers->global_lib->moneyFormatDollar($single_prop->price); ?><?php if($single_prop->property_for == 'Rent'){ echo '/'.mlx_get_lang('Month'); } ?>
				</div>
			  </div>
			  
			  <div class="row">
				<div class="col-xs-3 text-right">
					<strong><?php echo mlx_get_lang('Property Status'); ?></strong>
				</div>
				<div class="col-xs-8 col-xs-offset-1">
					<?php 
						
						if($single_prop->status == 'publish') echo '<span class="label label-success">'.mlx_get_lang('Publish').'</span>'; 
					    else if($single_prop->status == 'draft') echo '<span class="label label-info">'.mlx_get_lang('Draft').'</span>';
					    else if($single_prop->status == 'pending') echo '<span class="label label-warning">'.mlx_get_lang('Pending').'</span>';
					    else if($single_prop->status == 'reject') echo '<span class="label label-danger">'.mlx_get_lang('Reject').'</span>';
					    else echo '-';
					?>
				</div>
			  </div>
			  
            </div>
			
            <div class="col-xs-12">
              <p class="lead"><?php echo mlx_get_lang('User Details'); ?></p>
			
			  <?php 
			  $created_by = $single_prop->created_by;
			  $first_name = get_user_meta($created_by,'first_name');
			  $last_name = get_user_meta($created_by,'last_name');
			  $mobile_no = get_user_meta($created_by,'mobile_no');
			  $address = get_user_meta($created_by,'address');
			  $photo_url = get_user_meta($created_by,'photo_url');
			  ?>
			  <div class="row">
				<div class="col-xs-3 text-right">
					<?php 
					if(isset($photo_url) && !empty($photo_url))
					{
						if(file_exists('uploads/user/'.$photo_url))
						{
							echo '<img src="'.base_url().'uploads/user/'.$photo_url.'" class="agent_img">';
						}
						else
						{
							echo '<img src="'.base_url().'themes/default/images/no-user-image.png'.'" class="agent_img">';
						}
					}
					else
					{
						echo '<img src="'.base_url().'themes/default/images/no-user-image.png'.'" class="agent_img">';
					}
					?>
				</div>
				<div class="col-xs-8 col-xs-offset-1">
					
					<h4 style="margin-top:0px;"><?php echo ucfirst($first_name).' '.ucfirst($last_name); ?></h4>
					<p class="text-muted"><?php echo mlx_get_lang(ucfirst($single_prop->user_type)); ?></p>
					<?php if(!empty($mobile_no)){ ?>
						<p>
						  <i class="fa fa-phone"></i> <?php echo $mobile_no; ?>
						</p>
					<?php } ?>
					
					<?php if(!empty($single_property->user_email)){ ?>
						<p>
						  <i class="fa fa-envelope"></i> <?php echo $single_property->user_email; ?>
						</p>
					<?php } ?>
					
					<?php if(!empty($address)){ ?>
						<p>
						  <i class="fa fa-map-marker"></i> <?php echo $address; ?>
						</p>
					<?php } ?>
				</div>
			  </div>
            </div>
			
			<div class="col-xs-12">
              <p class="lead"><?php echo mlx_get_lang('Features'); ?></p>
              <div class="row">
				<div class="col-xs-3 text-right">
					<strong><?php echo mlx_get_lang('Size'); ?></strong>
				</div>
				<div class="col-xs-8 col-xs-offset-1">
					<td> <?php 
						$size_field = (!empty($single_prop->size))?$single_prop->size : "";
						$size_val = explode("~",$size_field);
						if(isset($size_val[0])) echo $size_val[0];
						if(isset($size_val[1])) echo " ".$size_val[1];
						?></td>
				</div>
			  </div>
			  
			  <div class="row">
				<div class="col-xs-3 text-right">
					<strong><?php echo mlx_get_lang('Bedroom'); ?></strong>
				</div>
				<div class="col-xs-8 col-xs-offset-1">
					<?php echo $single_prop->bedroom; ?>
				</div>
			  </div>
			  
			  <div class="row">
				<div class="col-xs-3 text-right">
					<strong><?php echo mlx_get_lang('Bathroom'); ?></strong>
				</div>
				<div class="col-xs-8 col-xs-offset-1">
					<?php echo $single_prop->bathroom; ?>
				</div>
			  </div>
			  
			  <div class="row">
				<div class="col-xs-3 text-right">
					<strong><?php echo mlx_get_lang('Garages'); ?> </strong>
				</div>
				<div class="col-xs-8 col-xs-offset-1">
					<?php echo $single_prop->garage; ?>
				</div>
			  </div>
			  
            </div>
			
			<div class="col-xs-12">
              <p class="lead"><?php echo mlx_get_lang('Address Details'); ?></p>
              <div class="row">
				<div class="col-xs-3 text-right">
					<strong><?php echo mlx_get_lang('Latitude'); ?></strong>
				</div>
				<div class="col-xs-8 col-xs-offset-1">
					<?php echo $single_prop->lat; ?>
				</div>
			  </div>
			  
			  <div class="row">
				<div class="col-xs-3 text-right">
					<strong><?php echo mlx_get_lang('Longitude'); ?></strong>
				</div>
				<div class="col-xs-8 col-xs-offset-1">
					<?php echo $single_prop->long; ?>
				</div>
			  </div>
			  
			  <div class="row">
				<div class="col-xs-3 text-right">
					<strong><?php echo mlx_get_lang('Address'); ?></strong>
				</div>
				<div class="col-xs-8 col-xs-offset-1">
					<?php echo ucfirst($single_prop->address); ?>
				</div>
			  </div>
			 
			  <div class="row">
				<div class="col-xs-3 text-right">
					<strong><?php echo mlx_get_lang('City'); ?></strong>
				</div>
				<div class="col-xs-8 col-xs-offset-1">
					<?php echo ucfirst($single_prop->city); ?>
				</div>
			  </div>
			  
			  <div class="row">
				<div class="col-xs-3 text-right">
					<strong><?php echo mlx_get_lang('Zip Code'); ?></strong>
				</div>
				<div class="col-xs-8 col-xs-offset-1">
					<?php echo $single_prop->zip_code; ?>
				</div>
			  </div>
            </div>
			
			<?php if(isset($single_prop->indoor_amenities) && !empty($single_prop->indoor_amenities)) { ?>
				
			<?php $indoor_amenities = json_decode($single_prop->indoor_amenities,true); 
				if(!empty($indoor_amenities)){
			   ?>
			    <div class="col-xs-12">
					<p class="lead"><?php echo mlx_get_lang('Indoor Amenities'); ?></p>
					<div class="row">
						<?php foreach($indoor_amenities as $k=>$v){ ?>	
							<div class="col-xs-3"><i class="fa fa-check"></i> <?php echo ucfirst($v); ?></div>
						<?php } ?>
					</div>
				</div>
			   <?php } ?> 
				
			   <?php } ?>
			
			<?php if(isset($single_prop->outdoor_amenities) && !empty($single_prop->outdoor_amenities)) { ?>
				
			<?php $outdoor_amenities = json_decode($single_prop->outdoor_amenities,true);
				if(!empty($outdoor_amenities)){
			   ?>
			    <div class="col-xs-12">
					<p class="lead"><?php echo mlx_get_lang('Outdoor Amenities'); ?></p>
					<div class="row">
						<?php foreach($outdoor_amenities as $k=>$v){ ?>	
							<div class="col-xs-3"><i class="fa fa-check"></i> <?php echo ucfirst($v); ?></div>
						<?php } ?>
					</div>
				</div>
			   <?php } ?> 
				
			   <?php } ?>
			 
			 <?php if(isset($single_prop->distance_list) && !empty($single_prop->distance_list)) { ?>
				
			 <?php $distance_list = json_decode($single_prop->distance_list,true);
				if(!empty($distance_list)){
			   ?>
			    <div class="col-xs-12">
					<p class="lead"><?php echo mlx_get_lang('Distances'); ?></p>
					<div class="row">
						<?php foreach($distance_list as $k=>$v){ ?>	
							<div class="col-xs-3 col-sm-4 col-md-4"><i class="fa fa-arrows"></i> <?php echo ucfirst($k); ?> <small>(<?php echo ucfirst($v['direction']); ?>)</small> : <strong><?php echo ucfirst($v['distance']); ?><?php echo ucfirst($v['distance_text']); ?></strong></div>
						<?php } ?>
					</div>
				</div>
			   <?php } ?> 
				 
			   <?php } ?>
			 
			  <?php 
			  if(!empty($single_prop->property_images))
			  {
					$p_images = $myHelpers->global_lib->get_property_gallery($single_prop->p_id , 'thumbnail');
					
					if(!empty($p_images))
					{
						
				?>
				
				<div class="col-xs-12 gallery_images">
					<p class="lead"><?php echo mlx_get_lang('Gallery'); ?></p>
					<div class="row">
					<?php 
					foreach($p_images as $k=>$v)
					{
						
						if(isset($v['original']))
							$large_image_url = base_url().'../'.$v['original'];
						else if(isset($v['medium']))
							$large_image_url = base_url().'../'.$v['medium'];
						else
							$large_image_url = base_url().'../'.$v['thumbnail'];
						
						$thumb_image_url = base_url().'../'.$v['thumbnail'];
					?>
						<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
							<a href="<?php echo $large_image_url; ?>" class="image-popup gal-item no-print">
								<img src="<?php echo $thumb_image_url; ?>" alt="Image" class="img-fluid">
							</a>
							<img src="<?php echo $thumb_image_url; ?>" alt="Image" class="img-fluid show-on-print">
						</div>
					<?php } ?>
					</div>
              </div>
			  <?php }
				}
				?>
				
				<?php 
				$isDocAct = $myHelpers->isPluginActive('document');
				if($isDocAct == true)
				{		
						
						if(isset($document_types) && $document_types->num_rows() > 0)
						{
							
							foreach($document_types->result() as $doc_type_row)
							{
								
								if(isset($property_meta[$doc_type_row->slug.'-ids']) && !empty($property_meta[$doc_type_row->slug.'-ids']))
								{
									
						?>
									<div class="col-xs-12 document_container">
										<p class="lead"><?php echo ucfirst($doc_type_row->title).' - '.mlx_get_lang('Document Type'); ?></p>
										<div class="row product-document-container">
											<?php 
												
											
												$p_g_i = explode(',',$property_meta[$doc_type_row->slug.'-ids']);
												
												if(count($p_g_i) > 0)
												{
													foreach($p_g_i as $key=>$val)
													{
														$img_id = $val;
														
														$query = "SELECT att.* FROM `attachments` as att
														WHERE att.att_type = 'document' and att_id = $img_id";
														$result = $myHelpers->Common_model->commonQuery($query);
														if($result->num_rows() > 0)
														{
															$img_row = $result->row();
															
															$explode = explode('.',$img_row->att_name);
															$extension = $explode[count($explode)-1];
															$actual_name = substr($img_row->att_name, 0, strrpos($img_row->att_name, "."));

															if($img_row->file_type == 'image')
															{
																$thumb_image_url = base_url().'../'.$img_row->att_path.$actual_name.'-thumbnail.'.$extension;
																if(file_exists('../'.$img_row->att_path.$actual_name.'-thumbnail.'.$extension))
																{
																	$thumb_image_url = base_url().'../'.$img_row->att_path.$actual_name.'-thumbnail.'.$extension;
																}
																else
																{
																	$thumb_image_url = base_url().'../'.$img_row->att_path.$img_row->att_name;
																}
																$origional_dowload_image_url = $origional_image_url = base_url().'../'.$img_row->att_path.$img_row->att_name;
																
															}
															else if($extension == 'doc' || $extension == 'docx' || $extension == 'xls' || $extension == 'xlsx')
															{
																if(file_exists('../themes/default/images/file_icons/'.$extension.'_file.png'))
																{
																	$thumb_image_url = base_url().'../themes/default/images/file_icons/'.$extension.'_file.png';
																}
																else
																{
																	$thumb_image_url = base_url().'../themes/default/images/file_icons/default_file.jpg';
																}
																$url_final = base_url().'../'.$img_row->att_path.$img_row->att_name;
																$origional_image_url = $url_final;
																$origional_dowload_image_url = base_url().'../'.$img_row->att_path.$img_row->att_name;
															}
															else
															{
																if(file_exists('../themes/default/images/file_icons/'.$extension.'_file.png'))
																{
																	$thumb_image_url = base_url().'../themes/default/images/file_icons/'.$extension.'_file.png';
																}
																else
																{
																	$thumb_image_url = base_url().'../themes/default/images/file_icons/default_file.jpg';
																}
																$origional_dowload_image_url = $origional_image_url = base_url().'../'.$img_row->att_path.$img_row->att_name;
															}
															
															
															echo "<div class='col-md-2 document_images '><div class='document_images_inner lazy-load-processing' data-toggle='tooltip' 
																data-original-title='".$img_row->att_name."'>
																	<a href='$origional_dowload_image_url' download='$img_row->att_name'>
																	<img class='lazy-img-elem' data-img_id='".$myHelpers->global_lib->EncryptClientId($img_id)."' 
																	data-src='".$thumb_image_url."'>
																	</a>
																	</div></div>";
															
															
														}
													}
												}
											
											?>
										</div>
									</div>
						
				<?php }}}} ?> 
				
				
				<?php 
				/** && $single_prop->created_by == $user_id **/
				if($single_prop->status == 'reject' ) { 
					
					$reject_message = $myHelpers->global_lib->get_property_meta($single_prop->p_id,'reject_message');
					if(!empty($reject_message))
					{
				?>
				<div class="col-xs-12">
					<p class="lead"><?php echo mlx_get_lang('Rejection Reason'); ?></p>
					<div class="row">
						<div class="col-md-12">
							<p class="well well-sm no-shadow"><?php echo $reject_message; ?></p>
						</div>
					</div>
				</div>
				
				<?php }} ?>
				
          </div>

          <div class="row no-print">
            <div class="col-xs-12">
              <a href="#" onclick="window.print();" target="_blank" class="btn btn-default"><i class="fa fa-print"></i> <?php echo mlx_get_lang('Print'); ?></a>
				
				<?php if($user_type == 'admin' && $single_prop->status == 'pending'){ ?>
					<button type="submit" name="approve" class="btn btn-success pull-right"><i class="fa fa-check"></i> <?php echo mlx_get_lang('Approve'); ?></button>
					<button name="reject" class="btn btn-danger pull-right not-submitted" style="margin-right: 5px;"><i class="fa fa-remove"></i> <?php echo mlx_get_lang('Reject'); ?></button>
				<?php }else{ ?>
					<a href="<?php echo base_url('property/manage'); ?>" class="btn btn-<?php echo $myHelpers->global_lib->get_skin_class(); ?> pull-right" style="margin-right: 5px;"><i class="fa fa-share"></i> <?php echo mlx_get_lang('Back'); ?></a>
				<?php } ?>
			</div>
          </div>
		  </form>
		  </div>
        </section>
      </div>
	  
<div id="rejectModal" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
		<form method="post" class="reject_form_submit">
		  <div class="modal-header">
			<h5 class="modal-title"><?php echo mlx_get_lang('Property Reject Form'); ?>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
			</h5>
			
		  </div>
		  <div class="modal-body">
			<label><?php echo mlx_get_lang('Comment'); ?></label>
			<textarea class="form-control reject_message" required></textarea>
		  </div>
		  <div class="modal-footer">
			<button type="submit" class="btn btn-primary"><?php echo mlx_get_lang('Submit'); ?></button>
			<button type="button" class="btn btn-secondary pull-left" data-dismiss="modal"><?php echo mlx_get_lang('Close'); ?></button>
		  </div>
		</form>
    </div>
  </div>
</div> 	  