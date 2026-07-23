<?php 
$advance_search_price_range = get_option('advance_search_price_range');
$advance_search_bath = 	get_option('advance_search_bath');
$advance_search_bed = 	get_option('advance_search_bed');

$col_class = 'col-md-4';
$col_class_count = 0;
if($advance_search_price_range == 'Y')
{
	$col_class_count++;
}
if($advance_search_bath == 'Y')
{
	$col_class_count++;
}
if($advance_search_bed == 'Y')
{
	$col_class_count++;
}

if($col_class_count == 1)
	$col_class = 'col-md-12';
else if($col_class_count == 2)
	$col_class = 'col-md-6';
else if($col_class_count == 3)
	$col_class = 'col-md-4';


$advance_search_indoor_amenities = get_option('advance_search_indoor_amenities');
$advance_search_outdoor_amenities = get_option('advance_search_outdoor_amenities');


$feature_type = "slider"; /**  slider/input_boxes **/

?>
<link rel="stylesheet" href="<?php echo base_url().'application/views/'.$theme.'/assets/css/icheck.min_all.css'?>"></link>
<link rel="stylesheet" href="<?php echo base_url().'application/views/'.$theme.'/assets/css/price-range.css'?>"></link>

	<?php
		$adv_search_hidden = 0;
		if(isset($_GET['adv_search']) && $_GET['adv_search'] == 1)
			$adv_search_hidden = 1;
		
		$list_types_bed_enable = $list_types_bath_enable = array();
		if(isset($property_type_list)){
			
			/*print_r($property_type_list);*/
			if($property_type_list->num_rows() > 0)
			{
				foreach ($property_type_list->result() as $row)
				{	 
					if($row->slug == '') continue;
					$meta_options = $row->meta_options;
					/*print_r($meta_options); */
					if(!empty($meta_options))
					{
						$meta_options = json_decode($meta_options,true);
						if(isset($meta_options['adv_search_options'])){
							
							$adv_search_options = $meta_options['adv_search_options'];
							
							if(isset($adv_search_options['enable_min_bed']) && $adv_search_options['enable_min_bed'] == 'Y')
								$list_types_bed_enable [] = $row->slug;	
							
							if(isset($adv_search_options['enable_min_bath']) && $adv_search_options['enable_min_bath'] == 'Y')
								$list_types_bath_enable [] = $row->slug;	
							
						}
					}	
				}
			}	
		}
		
		
	?>

	<div class="row adv-serach-header-row">
		<div class="col-md-12 ">
			<a href="#" id="adv_search" class="pull-right adv-search-header"><?php echo mlx_get_lang('Advanced Search'); ?></a>
			<input type="hidden" name="adv_search" class="adv_search_hidden" value="<?php echo $adv_search_hidden;?>"  />
		</div>
	</div>	

<style type="text/css">
.slider.disabled .slider-handle {
  background-image: -webkit-linear-gradient(top, #dfdfdf 0%, #bebebe 100%);
  background-image: -o-linear-gradient(top, #dfdfdf 0%, #bebebe 100%);
  background-image: linear-gradient(to bottom, #dfdfdf 0%, #bebebe 100%);
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffdfdfdf', endColorstr='#ffbebebe', GradientType=0);
  background-repeat: repeat-x;
}
.slider.disabled .slider-track {
  background-image: -webkit-linear-gradient(top, #e5e5e5 0%, #e9e9e9 100%);
  background-image: -o-linear-gradient(top, #e5e5e5 0%, #e9e9e9 100%);
  background-image: linear-gradient(to bottom, #e5e5e5 0%, #e9e9e9 100%);
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffe5e5e5', endColorstr='#ffe9e9e9', GradientType=0);
  background-repeat: repeat-x;
  cursor: not-allowed;
}

.slider.disabled .slider-selection {
    background: none repeat scroll 0 0 #fff;
}
.adv-search-wrapper .tooltip.in {
	filter:alpha(opacity=90);
	opacity:.9
}	
.adv-search-wrapper .tooltip {
    font-size: 14px;
    font-weight: bold;
}
.adv-search-wrapper .tooltip-arrow{
    display: none;
    opacity: 0;
}
.adv-search-wrapper .tooltip-inner {
    background-color: #FAE6A4;
    border-radius: 4px;
    box-shadow: 0 1px 13px rgba(0, 0, 0, 0.14), 0 0 0 1px rgba(115, 71, 38, 0.23);
    color: #734726;
    max-width: 200px;
    padding: 6px 10px;
    text-align: center;
    text-decoration: none;
}
.adv-search-wrapper .tooltip-inner:after {
    content: "";
    display: inline-block;
    left: 100%;
    margin-left: -56%;
    position: absolute;
}
.adv-search-wrapper .tooltip-inner:before {
    content: "";
    display: inline-block;
    left: 100%;
    margin-left: -56%;
    position: absolute;
}
.adv-search-wrapper .tooltip.top {
    margin-top: -11px;
    padding: 0;
}
.adv-search-wrapper .tooltip.top .tooltip-inner:after {
    border-top: 11px solid #FAE6A4;
    border-left: 11px solid rgba(0, 0, 0, 0);
    border-right: 11px solid rgba(0, 0, 0, 0);
    bottom: -10px;
}
.adv-search-wrapper .tooltip.top .tooltip-inner:before {
    border-top: 11px solid rgba(0, 0, 0, 0.2);
    border-left: 11px solid rgba(0, 0, 0, 0);
    border-right: 11px solid rgba(0, 0, 0, 0);
    bottom: -11px;
}
.checkbox label{
	overflow: hidden;
	white-space: nowrap;
	text-overflow: ellipsis;
	display: inline-block;
	width: 100%;
	margin-bottom:0px;
}
</style>

<script type="text/javascript">

$(document).ready(function () {
	
	//console.log($("#list-types option:selected").val());
	function reset_adv_search_options(list_type){
		
		if(list_type == '') return false;
		var list_types_bed_enable = <?php echo json_encode($list_types_bed_enable); ?>;    //["flat","apartment","villa"];
		var list_types_bath_enable = <?php echo json_encode($list_types_bath_enable); ?>;   ///["flat","apartment","villa"];
		
		
		if($.inArray(list_type , list_types_bed_enable) > -1 )
		{
			$('#min-bed').slider('enable');
			
			$('#min-bed').attr('disabled',false);
			
		}else	
		{
			$('#min-bed').slider('disable');
			
			$('#min-bed').attr('disabled',true);
			
		}
		
		if($.inArray(list_type , list_types_bath_enable) > -1 )
		{
			$('#min-baths').slider('enable');
			
			$('#min-baths').attr('disabled',false);
			
			
		}else	
		{
			$('#min-baths').slider('disable');
			
			$('#min-baths').attr('disabled',true);
			
		}
		
		
	}
	var list_type = $("#list-types option:selected").val();
	
	reset_adv_search_options( list_type);
	
	$("#list-types").on('change',function(){
		
		//console.log('change');
		var list_type = $(this).val();
		//console.log('change' + list_type);
		reset_adv_search_options( list_type);	
		
	});
	
	
	$(".price_range").on('change',function(){
		
		var price_range_min = $('input[name=price_range_min]').val();
		var price_range_max = $('input[name=price_range_max]').val();
		$('input[name=price_ranges]').val( price_range_min +  ',' + price_range_max);
		
	});
	
	$(".bath_range").on('change',function(){
		
		var bath_range_min = $('input[name=bath_range_min]').val();
		var bath_range_max = $('input[name=bath_range_max]').val();
		$('input[name=bath_ranges]').val( bath_range_min +  ',' + bath_range_max);
		
	});
	
	$(".bed_range").on('change',function(){
		
		var bed_range_min = $('input[name=bed_range_min]').val();
		var bed_range_max = $('input[name=bed_range_max]').val();
		
		
		$('input[name=bed_ranges]').val( bed_range_min +  ',' + bed_range_max);
		
	});
});	

</script>
	
	<div class="row adv-serach-row">	
		<div class="col-md-12 adv-search-wrapper">
		
		<div class="row">
			<?php
			if($advance_search_price_range == 'Y'){	
				$currency_symbol = $myHelpers->global_lib->get_currency_symbol();
				
				$option_price_range_min = $myHelpers->global_lib->get_option("advance_search_min_price");
				$option_price_range_max = $myHelpers->global_lib->get_option("advance_search_max_price");
				
				if($option_price_range_min == '') $option_price_range_min = 1000;
				if($option_price_range_max == '') $option_price_range_max = 1000000000;
				
				
				
				$slider_value = $option_price_range_min.",".$option_price_range_max;
				if(isset($_GET['price_ranges']) && !empty($_GET['price_ranges']))
					$slider_value = $_GET['price_ranges'];
				
				$slider_range = explode(",",$slider_value);
				$price_range_min = ( isset($slider_range[0]) && !empty($slider_range[0]) ) ? $slider_range[0] : $option_price_range_min;
				$price_range_max = ( isset($slider_range[1])  && !empty($slider_range[1	]) )? $slider_range[1] : $option_price_range_max;
				
				
				
				if(isset($myHelpers->currency_pos)) $currency_pos =  $myHelpers->currency_pos ;  else $currency_pos = 'left'; 
				
				
				
			?>
			
			<?php if($feature_type == 'input_boxes') { ?>
			
			<div class="<?php echo $col_class; ?>">
				<label for="price-range"><?php echo mlx_get_lang('Price Range'); ?> (<?php echo $currency_symbol; ?>):</label>
				<input type="hidden" name="price_ranges" class="" value="<?php echo $slider_value; ?>"  >
				<div class="row"> 
				<div class="col-md-6" style="">
					<input type="number" name="price_range_min" class="price_range" 
					min="<?php echo $option_price_range_min; ?>" max="<?php echo $option_price_range_max; ?>" step="10"
					value="<?php echo $price_range_min; ?>"  style="width:100%;"  >
				</div>
				<div class="col-md-6">	
					<input type="number" name="price_range_max" class="price_range" 
					min="<?php echo $option_price_range_min; ?>" max="<?php echo $option_price_range_max; ?>" step="10"
					value="<?php echo $price_range_max; ?>"  style="width:100%;" >
				</div>
				</div>
			</div>
			<?php } else { ?>
			
			<div class="<?php echo $col_class; ?>">
				<label for="price-range"><?php echo mlx_get_lang('Price Range'); ?> (<?php echo $currency_symbol; ?>):</label>
				<input type="text" name="price_ranges" class="span2" value="<?php echo $slider_value; ?>" 
				data-slider-min="<?php echo $option_price_range_min; ?>" 
				data-slider-max="<?php echo $option_price_range_max; ?>" data-slider-step="500" data-slider-id="price-range" 
				data-slider-value="[<?php echo $slider_value; ?>]" id="price-range" ><br />
				<b class="pull-left color">
					<?php if($currency_pos == 'left') echo $currency_symbol; else if($currency_pos == 'left_space') echo $currency_symbol.' '; ?><span class="price-range-min"><?php echo $price_range_min; ?></span><?php if($currency_pos == 'right') echo $currency_symbol; else if($currency_pos == 'right_space') echo ' '.$currency_symbol; ?>
				</b> 
				<b class="pull-right color">
					<?php if($currency_pos == 'left') echo $currency_symbol; else if($currency_pos == 'left_space') echo $currency_symbol.' '; ?><span class="price-range-max"><?php echo $price_range_max; ?></span><?php if($currency_pos == 'right') echo $currency_symbol; else if($currency_pos == 'right_space') echo ' '.$currency_symbol; ?>
					</b>
			</div>
			<?php } ?>
			
			
			<?php } ?>
			
			<?php
			if($advance_search_bath == 'Y')
			{
				$slider_value = "1,10";
				if(isset($_GET['bath_ranges']) && !empty($_GET['bath_ranges']))
					$slider_value = $_GET['bath_ranges'];
				
				$slider_range = explode(",",$slider_value);
				$bath_range_min = isset($slider_range[0])? $slider_range[0] : 1;
				$bath_range_max = isset($slider_range[1])? $slider_range[1] : 10;
				$def_bath_range_min = 1;
				$def_bath_range_max = 10;
			?>
			
			<?php if($feature_type == 'input_boxes') { ?>
			
			<div class="<?php echo $col_class; ?>">
				<label for="bath-range"><?php echo mlx_get_lang('Baths'); ?> :</label>
				<input type="hidden" name="bath_ranges" class="" value="<?php echo $slider_value; ?>"  >
				<div class="row"> 
				<div class="col-md-6" style="">
					<input type="number" name="bath_range_min" class="bath_range" 
					min="<?php echo $def_bath_range_min; ?>" max="<?php echo $def_bath_range_max; ?>" step="1"
					value="<?php echo $bath_range_min; ?>"  style="width:100%;"  >
				</div>
				<div class="col-md-6">	
					<input type="number" name="bath_range_max" class="bath_range" 
					min="<?php echo $def_bath_range_min; ?>" max="<?php echo $def_bath_range_max; ?>" step="1"
					value="<?php echo $bath_range_max; ?>"  style="width:100%;" >
				</div>
				</div>
			</div>
			
			<?php } else { ?>
			
			
			<div class="<?php echo $col_class; ?>">
				<label for="bath-range"><?php echo mlx_get_lang('Baths'); ?> :</label>
				<input type="text" name="bath_ranges" class="span2" value="<?php echo $slider_value; ?>" data-slider-min="1" 
				data-slider-max="10" data-slider-step="1" data-slider-id="bath-range" data-slider-disabled="false"
				data-slider-value="[<?php echo $slider_value; ?>]" id="min-baths" ><br />
				<b class="pull-left color"><span class="bath-range-min"><?php echo $bath_range_min; ?></span></b> 
				<b class="pull-right color"><span class="bath-range-max"><?php echo $bath_range_max; ?></span></b>
			</div>
			<?php } ?>
			
			<?php } ?>
			
			<?php
			if($advance_search_bed == 'Y')
			{
				$slider_value = "1,10";
				if(isset($_GET['bed_ranges']) && !empty($_GET['bed_ranges']))
					$slider_value = $_GET['bed_ranges'];
				
				$slider_range = explode(",",$slider_value);
				$bed_range_min = isset($slider_range[0])? $slider_range[0] : 1;
				$bed_range_max = isset($slider_range[1])? $slider_range[1] : 10;
				$def_bed_range_min = 1;
				$def_bed_range_max = 10;
			?>
			
			
			
			<?php if($feature_type == 'input_boxes') { ?>
			
			<div class="<?php echo $col_class; ?>">
				<label for="bed-range"><?php echo mlx_get_lang('Beds'); ?> :</label>
				<input type="hidden" name="bed_ranges" class="" value="<?php echo $slider_value; ?>"  >
				<div class="row"> 
				<div class="col-md-6" style="">
					<input type="number" name="bed_range_min" class="bed_range" 
					min="<?php echo $def_bed_range_min; ?>" max="<?php echo $def_bed_range_max; ?>" step="1"
					value="<?php echo $bed_range_min; ?>"  style="width:100%;"  >
				</div>
				<div class="col-md-6">	
					<input type="number" name="bed_range_max" class="bed_range" 
					min="<?php echo $def_bed_range_min; ?>" max="<?php echo $def_bed_range_max; ?>" step="1"
					value="<?php echo $bed_range_max; ?>"  style="width:100%;" >
				</div>
				</div>
			</div>
			
			<?php } else { ?>
			
			<div class="<?php echo $col_class; ?>">
				<label for="bed-range"><?php echo mlx_get_lang('Beds'); ?> :</label>
				<input type="text" name="bed_ranges"  class="span2" value="<?php echo $slider_value; ?>" data-slider-min="1" 
				data-slider-max="10" data-slider-step="1" data-slider-id="bed-range" data-slider-disabled="false"
				data-slider-value="[<?php echo $slider_value; ?>]" id="min-bed" ><br />
				<b class="pull-left color"><span class="bed-range-min"><?php echo $bed_range_min; ?></span></b> 
				<b class="pull-right color"><span class="bed-range-max"><?php echo $bed_range_max; ?></span></b>
			</div>
			
			<?php } ?>
			<?php } ?>
		
		</div>
		
		
		<?php if((isset($amenities_list) && !empty($amenities_list)) && (
			 $advance_search_indoor_amenities == 'Y' ||  $advance_search_outdoor_amenities == 'Y'
		)) { ?>
		<?php if($col_class_count > 0) { ?>
		<hr>
		<?php } ?>
		<?php if(isset($amenities_list['indoor_amenities']) && !empty($amenities_list['indoor_amenities']) && $advance_search_indoor_amenities == 'Y') { 
			$selc_indoc_amen = array();
			if(isset($_GET['indoor_amenities']) && !empty($_GET['indoor_amenities']))
			{
				$selc_indoc_amen = explode(',',$_GET['indoor_amenities']);
			}
		?>
			<label> <?php echo mlx_get_lang('Indoor Amenities'); ?> :</label>
			<div class="row search-row">  
				
				<?php 
				$amenities = 	$amenities_list['indoor_amenities'];
				foreach($amenities as $amenity){ 
				
				
				?>
				<div class="col-sm-3">
					<div class="checkbox">
						<label class="default-label" title="<?php echo $amenity ; ?>">
							<input type="checkbox" name="indoor_amenities[<?php echo str_replace(' ','_',$amenity); ?>]" value="1" 
							<?php if(in_array(str_replace(' ','_',strtolower($amenity)),$selc_indoc_amen)) { 
								echo  ' checked="checked" ';
							} ?>
							>&nbsp;&nbsp;<?php echo $amenity ; ?>
						</label>
					</div>
				</div>
				<?php } ?>
			</div>   
		<?php } ?>
		
				
				
		<?php if(isset($amenities_list['outdoor_amenities']) && !empty($amenities_list['outdoor_amenities'])  && $advance_search_outdoor_amenities == 'Y') { 
			$selc_outdoc_amen = array();
			if(isset($_GET['outdoor_amenities']) && !empty($_GET['outdoor_amenities']))
			{
				$selc_outdoc_amen = explode(',',$_GET['outdoor_amenities']);
			}
		?>
		<?php if($col_class_count > 0 || $advance_search_indoor_amenities == 'Y') { ?>
		<hr>
		<?php } ?>
			<label> <?php echo mlx_get_lang('Outdoor Amenities'); ?> :</label>
			<div class="row search-row">  
				
				<?php 
				$amenities = 	$amenities_list['outdoor_amenities'];
				foreach($amenities as $amenity){ ?>
				<div class="col-sm-3">
					<div class="checkbox">
						<label class="default-label" title="<?php echo $amenity ; ?>">
							<input type="checkbox" name="outdoor_amenities[<?php echo str_replace(' ','_',$amenity); ?>]" value="1" 
							<?php if(in_array(str_replace(' ','_',strtolower($amenity)),$selc_outdoc_amen)) { 
								echo  ' checked="checked" ';
							} ?>
							>&nbsp;&nbsp;<?php echo $amenity ; ?>
						</label>
					</div>
				</div>
				<?php }  ?>
			</div>   
		<?php } ?>		
				
				
		<?php } ?>
		
		
		
		</div>
	</div>  
			