<?php echo link_tag("themes/$theme/plugins/availability_calendar/dateTimePicker.css"); ?>
<?php echo script_tag("themes/$theme/plugins/availability_calendar/dateTimePicker.min.js"); ?>
<?php echo script_tag("themes/$theme/plugins/availability_calendar/booking.js"); ?>
<h2 class="h4 mb-3 text-black no-gutters mt-5  text-left"><?php echo mlx_get_lang('Property Availability Calendar'); ?>
	<small class="outer_blck">
		<small class="btn btn-success btn-sm inner_blck" style="background:#d2d6de;border-color:#d2d6de;">&nbsp;</small>
		<span class="text">Available</span>
		&nbsp;&nbsp;
		<small class="btn btn-danger btn-sm inner_blck" style="background:#f87217;border-color:#f87217;">&nbsp;</small>
		<span class="text">Booked</span>
	</small>
</h2>
  <div class="row ">
	<div class="col-md-12">
		<div id="show-next-month" data-toggle="calendar"></div>
	</div>
</div>			
