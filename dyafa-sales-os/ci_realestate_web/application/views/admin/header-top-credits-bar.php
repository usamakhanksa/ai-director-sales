<?php 
	$admin_url =  site_url();
	$site_url = str_replace("/admin","",$admin_url);	

	$user_id = $this->session->userdata('user_id');
	$user_type = $this->session->userdata('user_type');
?>


	<?php if($this->session->userdata('user_type') != 'admin'){?>
	<li class="dropdown tasks-menu">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown">
		  <i class="fa fa-flag-o"></i>
		  <span class="label label-danger"> <?php 
		   ?>
		 </span>
		</a>
		<ul class="dropdown-menu">
			  <?php
			  $query="select * from user_meta where user_id=$user_id 
			  and ( meta_key like '%_credit') ";
			  $user_info = $myHelpers->Common_model->commonQuery($query);
			  

			  $slimdiv_height = "0";
			  if($user_info->num_rows() == 0 ){?>
			   
				<li class="header"><?php echo mlx_get_lang('You have 0 Credits'); ?></li>
				<?php
				}else{
					
					if($user_info->num_rows() <= 4 )
						$slimdiv_height = $user_info->num_rows() * 56;		
					
					?>
				<li class="header"><?php echo mlx_get_lang('You have Below Credits'); ?></li>
				  <li id="user_credits">
					<!-- inner menu: contains the actual data -->
					<ul class="menu">
					<?php
					  foreach($user_info->result() as $user_data)
					  { 

						?>
						<li>
						  <a href="<?php 
						  $link='';
						  if($user_data->meta_key =='subscription_credit'){
							  $link= 'subscription';
						  }else{
							$link = 'topup';
						  }

						echo base_url().'packages/choose_package/'.$link; ?>">
						<h3 >
						  <?php 

						  echo ucwords(str_replace('_',' ',$user_data->meta_key)).''; 
						  ?>
						  <small class="pull-right"><?php 
						  if($user_data->meta_key == 'subscription_credit'){
							echo "Expires on ". date('m/d/Y',$user_data->meta_value);
							
						  }else{
							  echo $user_data->meta_value;
						  }
						 
						  ?></small>
						</h3>
							
							<div class="progress xs">
							  <?php  
							  if($user_data->meta_key == 'subscription_credit'){ 
							  
							  $subscription_credited =  $myHelpers->global_lib->get_user_meta($user_id , 'subscription_credited');
							  $subscription_credit_expires = $user_data->meta_value;
							  
							  $diff = $subscription_credit_expires - $subscription_credited; 
							  $credit_purchase_diff =  abs(round($diff / 86400));
							  
							  //echo date("d/m/Y",$subscription_credited);
							  
								$diff = $subscription_credit_expires - time(); 
								$current_day_diff = $credit_expire =  abs(round($diff / 86400));
								//var_dump($credit_expire);
								
								$total_diff = $credit_purchase_diff - $current_day_diff;
								$progress = round( ($current_day_diff  * 100)/$credit_purchase_diff );
								
								if($progress <= 10){
								   $progressBar = 'progress-bar-danger';
								}elseif($progress <= 20){
								   $progressBar = 'progress-bar-warning';
								}
								elseif($progress <= 50){
								   $progressBar = 'progress-bar-info';
								}elseif($progress <= 70){
								   $progressBar = 'progress-bar-success';
								}else{
								  $progressBar = 'progress-bar-success';
								}
								?>
							  <div class="progress-bar <?php echo $progressBar; ?>" 
									style="width: <?php if(isset($progress)) { echo $progress; }else{ echo 0 ; } ?>%" 
									role="progressbar" aria-valuenow="<?php echo $progress; ?>" aria-valuemin="0" aria-valuemax="100">
							  <?php 
							  }
							  else
							  { 
								$credit_type = str_replace("_credit","", $user_data->meta_key);
								
								$credit_type_credited =  $myHelpers->global_lib->get_user_meta($user_id , $credit_type.'_credited');
								if(!$credit_type_credited)
									$credit_type_credited = $user_data->meta_value;	
								
								/*$balance = (int) $user_data->meta_value*1000/100; 
								$balance = (int) $user_data->meta_value*1000/100;*/
								
								//$total_diff = $credit_type_credited - $credit_type;
								$balance = round( (($user_data->meta_value ) * 100)/ $credit_type_credited );
								
								//$balance = 50;
								if($balance >  60){
								  $progressBar = 'progress-bar-succsess';
								}
								elseif($balance > 30){
								  $progressBar = 'progress-bar-warning';
								}else{
								  $progressBar = 'progress-bar-danger';
								}
								?>
								<div class="progress-bar <?php echo $progressBar; ?>" style="width: <?php if(isset($balance)){ 
								  echo $balance; }else{
									echo 0;
								  } ?>%" role="progressbar" aria-valuenow="<?php if(isset($balance)){ echo $balance;} else{ echo 0;} ?>" aria-valuemin="0" aria-valuemax="100">
							  <?php }?>
							   
							  </div>
							</div>
						  </a>
						</li>
						
						<?php  } ?>
					  </ul>
					  <li class="footer">
						<a href="<?php echo base_url().'packages/choose_package'; ?>"><?php echo mlx_get_lang('View All Packages'); ?></a>
					  </li>
				<?php  } ?>
				  </li>
		</ul>
	</li>
	<?php } ?>
	 <!-- credit section end -->
	 <style>
		.iteams_height{
			height:56px !important;
		}
		
		li#user_credits > div {
			max-height: <?php echo $slimdiv_height; ?>px !important;
			}
		
	 </style>
