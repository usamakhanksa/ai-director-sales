<?php

$def_indoor_amenities = $def_outdoor_amenities = array();

if ($this->global_lib->get_option('property_amenities')) {
	$amenities_list = json_decode($this->global_lib->get_option('property_amenities'), true);
	$def_indoor_amenities = (isset($amenities_list['indoor_amenities']) ? $amenities_list['indoor_amenities'] : array());
	$def_outdoor_amenities = (isset($amenities_list['outdoor_amenities']) ? $amenities_list['outdoor_amenities'] : array());
}



?>

<?php if (isset($single_property->indoor_amenities) && !empty($single_property->indoor_amenities) && !empty($def_indoor_amenities)) {
	$indoor_amenities = json_decode($single_property->indoor_amenities, true);
	foreach ($indoor_amenities as $k => $v) {
		if (!in_array($v, $def_indoor_amenities)) unset($indoor_amenities[$k]);
	}
	if (!empty($indoor_amenities)) {


?>
		<h2 class="h4 text-black no-gutters mt-5  text-left"><?php echo mlx_get_lang('Indoor Amenities'); ?></h2>
		<div class="row">
			<?php foreach ($indoor_amenities as $k => $v) { ?>
				<div class="col-md-4 text-left"><i class="fa fa-check"></i> <?php echo mlx_get_lang(ucfirst($v)); ?></div>
			<?php } ?>
		</div>
<?php }
} ?>

<?php if (isset($single_property->outdoor_amenities) && !empty($single_property->outdoor_amenities) && !empty($def_outdoor_amenities)) {
	$outdoor_amenities = json_decode($single_property->outdoor_amenities, true);
	foreach ($outdoor_amenities as $k => $v) {
		if (!in_array($v, $def_outdoor_amenities)) unset($outdoor_amenities[$k]);
	}
	if (!empty($outdoor_amenities)) {
?>
		<h2 class="h4 text-black no-gutters mt-5  text-left"><?php echo mlx_get_lang('Outdoor Amenities'); ?></h2>
		<div class="row">
			<?php foreach ($outdoor_amenities as $k => $v) { ?>
				<div class="col-md-4 text-left"><i class="fa fa-check"></i> <?php echo mlx_get_lang(ucfirst($v)); ?></div>
			<?php } ?>
		</div>
<?php }
} ?>