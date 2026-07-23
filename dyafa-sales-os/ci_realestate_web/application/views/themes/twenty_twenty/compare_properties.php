
<div class="site-section site-section-sm bg-light compare_listing_block">
  <div class="container">
  <h2 class="text-center">Compare Listing</h2>
  <br>
  <table class="table table-bordered">
 
  <?php 
  
	if(count($property_list) > 1){
			$fields = array();
			$fields[] = array('title'=>'','prop-field'=>'title');
			$fields[] = array('title'=>'Address','prop-field'=>'address');
			$fields[] = array('title'=>'Country','prop-field'=>'country');
			$fields[] = array('title'=>'Image','prop-field'=>'property_images','field-type' => 'img');
			$fields[] = array('title'=>'Purpose','prop-field'=>'property_for');
			$fields[] = array('title'=>'City','prop-field'=>'city');
			$fields[] = array('title'=>'Area','prop-field'=>'size');
			$fields[] = array('title'=>'Bathroom','prop-field'=>'bathroom');
			$fields[] = array('title'=>'Bedroom','prop-field'=>'bedroom');
			$fields[] = array('title'=>'Price','prop-field'=>'price');
			$fields[] = array('title'=>'Indoor Amenities','prop-field'=>'indoor_amenities', 'field-type' => 'json');
			$fields[] = array('title'=>'Outdoor Amenities','prop-field'=>'outdoor_amenities','field-type' => 'json');
			$fields[] = array('title'=>'Distance List','prop-field'=>'distance_list','field-type' => 'json');
			$p =array();
			
			foreach($property_list as $i => $property){
				
				$p['property'.($i+1)] = $property->row();
			}
			
	?>
  <?php foreach($fields as $field){ 
		
	if(isset($field['field-type']) && $field['field-type'] == 'json'){ 
		if($field['prop-field'] == 'indoor_amenities' || $field['prop-field'] == 'outdoor_amenities')
		{
			$property_amenities = $this->global_lib->get_option('property_amenities');
			if(isset($property_amenities) && !empty($property_amenities))
			{
				echo '<tr class="option_heading">
						<th>'.$field['title'].'</th>';
						echo '<td colspan="'.count($p).'">&nbsp;</td>';
				echo '</tr>';
				$property_amenities_list = json_decode($property_amenities,true);
				if(count($property_amenities_list) > 0 && isset($property_amenities_list[$field['prop-field']]) )
				{
					$per_prop_ame_list = $property_amenities_list[$field['prop-field']];
					
					foreach($per_prop_ame_list as $palk=>$palv)
					{
						echo '<tr>';
							echo '<th class="pl30">'.ucfirst($palv).'</th>';
							foreach($p as $property)
							{
								$field_val = '<i class="fa fa-remove text-red"></i>';
								$field_name = $field['prop-field'];
								echo '<td>';
								if(isset($property->{$field_name}) && !empty($property->{$field_name}))
								{
									$field_array = json_decode($property->{$field_name},true);
									if(!empty($field_array) && in_array($palv,$field_array))
									{
										echo '<i class="fa fa-check text-green"></i>';
									}
									else
									{
										echo $field_val;
									}
								}else
								{
									echo $field_val;
								}
								echo '</td>';
							}
						echo '</tr>';
					}
					
				}
				
			}
		}
		else if($field['prop-field'] == 'distance_list')
		{
			$property_distances = $this->global_lib->get_option('property_distances');
			if(isset($property_distances) && !empty($property_distances))
			{
				echo '<tr class="option_heading">
						<th>'.$field['title'].'</th>';
						echo '<td colspan="'.count($p).'">&nbsp;</td>';
				echo '</tr>';
				$property_amenities_list = json_decode($property_distances,true);
				if(count($property_amenities_list) > 0 )
				{
					foreach($property_amenities_list as $palk=>$palv)
					{
						echo '<tr>';
							echo '<th class="pl30">'.ucfirst($palv).'</th>';
							foreach($p as $property)
							{
								$field_val = '<i class="fa fa-remove text-red"></i>';
								$field_name = $field['prop-field'];
								echo '<td>';
								if(isset($property->{$field_name}) && !empty($property->{$field_name}))
								{
									$field_array = json_decode($property->{$field_name},true);
									if(!empty($field_array) && array_key_exists($palv,$field_array))
									{
										echo $field_array[$palv]['direction'].', '.$field_array[$palv]['distance'].' '.$field_array[$palv]['distance_text'];
									}
									else
									{
										echo $field_val;
									}
								}else
								{
									echo $field_val;
								}
								echo '</td>';
							}
						echo '</tr>';
					}
					
				}
				
			}
		}
	?>
		
	<?php }else{ 
		$field_name = $field['prop-field'];
		$hading_cls = '';
		if($field_name == 'title')
		{
			$hading_cls = 'prop_heading';
		}
	?>
		<tr>
		<th><?php echo $field['title']?></th>
		<?php foreach($p as $property){?>
		<td class="<?php echo $hading_cls; ?>">
		<?php 
		
		$field_val = 'N/A';
			if(isset($property->{$field_name}) && !empty($property->{$field_name})){
				
				if(isset($field['field-type']) && $field['field-type'] == 'img'){
					
					if(!empty($property->{$field_name})){
						/*$img = $myHelpers->global_lib->get_property_image($property->p_id,'thumbnail');
						print_r($img);
						if(isset($img[0]))
							echo '<img src="'.base_url().$img[0]['thumbnail'].'" class="img-fluid" />';
							*/
							
						$p_images = $myHelpers->global_lib->get_property_gallery($property->p_id,'thumbnail');	
						/*print_r($p_images);*/
						if(count($p_images) > 0){
							/*reset($p_images); 
							$key = key($p_images); */
							foreach( $p_images as $key=>$value)
							{   
							   
								echo '<img src="'.base_url().$p_images[$key]['thumbnail'].'" class="img-fluid" />';
								break;
							}/**/
						}	
					}		
				}else
				{
					
					if($hading_cls == '')
					{
						if($field_name == 'price')
						{
							echo $currency_symbol = $myHelpers->global_lib->get_currency_symbol_by_property($property->p_id);
							echo ' '.$myHelpers->global_lib->moneyFormatDollar($property->{$field_name});
						}
						else
						{
							echo ucfirst($property->{$field_name});
						}
					}
					else
					{
						
						$property_url = $myHelpers->global_lib->get_property_url($property->p_id,$property);
						echo '<a  href="'.$property_url.'">'.ucfirst(stripslashes($property->{$field_name})).'</a>';
					}
				}
				
			}else{
				echo $field_val;
			}?>
		</td>
		<?php }?>
	</tr>
	<?php } }?>
<?php 	
	}else{?>
	<div class="alert alert-info">You Need to atleast 2 Propertices to be Compare </div>
<?php }?>
  </table>
  </div>
</div>    
    
