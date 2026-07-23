<?php 

	$enable_property_distances_for_all  = get_option("enable_property_distances_for_all");
	$enable_property_distances_for_admin  = get_option("enable_property_distances_for_admin");
	
	$CI = &get_instance();
	
	
	if($enable_property_distances_for_all == 'Y' || ( $CI->user_type == 'admin' && $enable_property_distances_for_admin == 'Y') ){
		
		
		if (isset($query) && $query->num_rows() > 0) {

			$property = $row = $query->row();

			foreach ($row as $k => $v) {
				${$k} = $v;
			}
			
			if (!empty($distance_list))
				$saved_distance_list = json_decode($distance_list, true);
		}	
?>


<?php if (isset($distances_list) && !empty($distances_list)) { ?>
<?php

$any_direction_list = array(
	'East' => 'East',
	'West' => 'West',
	'North' => 'North',
	'South' => 'South',
	'North-East' => 'North-East',
	'South-East' => 'South-East',
	'South-West' => 'South-West',
	'North-West' => 'North-West',
);
?>
<style>
	.distance_block .direction-block {
		border: 1px solid #f4f4f4;
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
		padding-left: 15px;
		padding-right: 15px;
	}

	.distance_block .direction-block:nth-child(even) {
		border-left: 0px;
		border-right: 0px;
	}

	.distance_block .row:nth-child(even) .direction-block {
		border-top: 0px;
		border-bottom: 0px;
	}

	.distance_block .row.no-gutters {
		display: flex;
		align-items: stretch;
		flex-direction: row;
	}

	.distance_block .direction-block .btn {
		margin-bottom: 10px;
	}

	.distance_block .direction-block .direction-listing {
		width: 100%;
	}

	.distance_block .direction-block .direction-listing .list-group {
		margin-bottom: 10px;
		text-align: left;
	}

	.distance_block .direction-block .direction-listing .list-group span.badge {
		background-color: #dd4b39;
		cursor: pointer;
	}

	.distance_block .row:nth-child(odd) .direction-block:nth-child(even) {
		background-color: #f8f9fa;
	}

	.distance_block .row:nth-child(even) .direction-block:nth-child(odd) {
		background-color: #f8f9fa;
	}
	.distance_block .direction-block.center-block{
		position:relative;
	}
	.arrow {
		border: solid black;
		border-width: 0 3px 3px 0;
		display: inline-block;
		padding: 3px;
		position:absolute;
	}

	.top-left {
		transform: rotate(-180deg);
		-webkit-transform: rotate(-180deg);
		top:30px;
		left:30px;
	}
	.top-center {
		transform: rotate(-135deg);
		-webkit-transform: rotate(-135deg);
		top:30px;
	}
	.top-right {
		transform: rotate(-90deg);
		-webkit-transform: rotate(-90deg);
		top:30px;
		right:30px;
	}

	.center-right {
		transform: rotate(-45deg);
		-webkit-transform: rotate(-45deg);
		right:30px;
	}

	.center-left {
		transform: rotate(135deg);
		-webkit-transform: rotate(135deg);
		left:30px;
	}

	
	.bottom-left {
		transform: rotate(90deg);
		-webkit-transform: rotate(90deg);
		bottom:30px;
		left:30px;
	}
	.bottom-center {
		transform: rotate(45deg);
		-webkit-transform: rotate(45deg);
		bottom:30px;
	}
	.bottom-right {
		transform: rotate(0deg);
		-webkit-transform: rotate(0deg);
		bottom:30px;
		right:30px;
	}
</style>
<div class="box box-<?php echo get_skin_class(); ?>">
	<div class="box-header with-border">
		<h3 class="box-title"><?php echo mlx_get_lang('Distances'); ?></h3>
		<div class="box-tools pull-right">


			<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>
	<div class="box-body distance_block">

		<?php
		// echo "<pre>";
		// print_r($saved_distance_list);
		/* foreach ($distances_list as $k => $v) { */
		?>


		<div class="direction_code">

			<div class="row no-gutters">
				<div class="col-md-4 text-center direction-block North-West-block">
					<h4><?php echo mlx_get_lang('North-West'); ?></h4>
					<div class="direction-listing">
						<ul class="list-group">
							<?php if (isset($saved_distance_list) && isset($saved_distance_list['North-West']) && !empty($saved_distance_list['North-West'])) {
								foreach ($saved_distance_list['North-West'] as $dk => $dv) {
							?>
									<li class="list-group-item">
										<span class="badge badge-danger">X</span>
										<?php if (isset($dv['title']) && !empty($dv['title']))
											echo ucfirst($dv['title']) . '<br />';
										?>
										<?php if (isset($dv['entity']) && !empty($dv['entity']))
											echo '<strong>' . ucfirst($dv['entity']) . '</strong> - ';
										?>
										<?php echo $dv['measurement'] . ' ' . ucfirst($dv['measurement_type']);
										?>
										<input type="hidden" name="distance[North-West][title][]" value="<?php echo $dv['title'] ?>">
										<input type="hidden" name="distance[North-West][entity][]" value="<?php echo $dv['entity'] ?>">
										<input type="hidden" name="distance[North-West][measurement][]" value="<?php echo $dv['measurement'] ?>">
										<input type="hidden" name="distance[North-West][measurement_type][]" value="<?php echo $dv['measurement_type'] ?>">
									</li>
							<?php }
							} ?>
						</ul>
					</div>
					<button type="button" class="btn btn-<?php echo get_skin_class(); ?> btn-sm " title="Add Distance" data-toggle="modal" data-target=".direction-modal" data-direction="North-West"><i class="fa fa-plus"></i></button>
				</div>
				<div class="col-md-4 text-center direction-block North-block">
					<h4><?php echo mlx_get_lang('North'); ?></h4>
					<div class="direction-listing">
						<ul class="list-group">
							<?php if (isset($saved_distance_list) && isset($saved_distance_list['North']) && !empty($saved_distance_list['North'])) {
								foreach ($saved_distance_list['North'] as $dk => $dv) {
							?>
									<li class="list-group-item">
										<span class="badge badge-danger">X</span>
										<?php if (isset($dv['title']) && !empty($dv['title']))
											echo ucfirst($dv['title']) . '<br />';
										?>
										<?php if (isset($dv['entity']) && !empty($dv['entity']))
											echo '<strong>' . ucfirst($dv['entity']) . '</strong> - ';
										?>
										<?php echo $dv['measurement'] . ' ' . ucfirst($dv['measurement_type']);
										?>
										<input type="hidden" name="distance[North][title][]" value="<?php echo $dv['title'] ?>">
										<input type="hidden" name="distance[North][entity][]" value="<?php echo $dv['entity'] ?>">
										<input type="hidden" name="distance[North][measurement][]" value="<?php echo $dv['measurement'] ?>">
										<input type="hidden" name="distance[North][measurement_type][]" value="<?php echo $dv['measurement_type'] ?>">
									</li>
							<?php }
							} ?>
						</ul>
					</div>
					<button type="button" class="btn btn-<?php echo get_skin_class(); ?> btn-sm " title="Add Distance" data-toggle="modal" data-target=".direction-modal" data-direction="North"><i class="fa fa-plus"></i></button>
				</div>
				<div class="col-md-4 text-center direction-block North-East-block">
					<h4><?php echo mlx_get_lang('North-East'); ?></h4>
					<div class="direction-listing">
						<ul class="list-group">
							<?php if (isset($saved_distance_list) && isset($saved_distance_list['North-East']) && !empty($saved_distance_list['North-East'])) {
								foreach ($saved_distance_list['North-East'] as $dk => $dv) {
							?>
									<li class="list-group-item">
										<span class="badge badge-danger">X</span>
										<?php if (isset($dv['title']) && !empty($dv['title']))
											echo ucfirst($dv['title']) . '<br />';
										?>
										<?php if (isset($dv['entity']) && !empty($dv['entity']))
											echo '<strong>' . ucfirst($dv['entity']) . '</strong> - ';
										?>
										<?php echo $dv['measurement'] . ' ' . ucfirst($dv['measurement_type']);
										?>
										<input type="hidden" name="distance[North-East][title][]" value="<?php echo $dv['title'] ?>">
										<input type="hidden" name="distance[North-East][entity][]" value="<?php echo $dv['entity'] ?>">
										<input type="hidden" name="distance[North-East][measurement][]" value="<?php echo $dv['measurement'] ?>">
										<input type="hidden" name="distance[North-East][measurement_type][]" value="<?php echo $dv['measurement_type'] ?>">
									</li>
							<?php }
							} ?>
						</ul>
					</div>
					<button type="button" class="btn btn-<?php echo get_skin_class(); ?> btn-sm " title="Add Distance" data-toggle="modal" data-target=".direction-modal" data-direction="North-East"><i class="fa fa-plus"></i></button>
				</div>
			</div>


			<div class="row no-gutters">
				<div class="col-md-4 text-center direction-block West-block">
					<h4><?php echo mlx_get_lang('West'); ?></h4>
					<div class="direction-listing">
						<ul class="list-group">
							<?php if (isset($saved_distance_list) && isset($saved_distance_list['West']) && !empty($saved_distance_list['West'])) {
								foreach ($saved_distance_list['West'] as $dk => $dv) {
							?>
									<li class="list-group-item">
										<span class="badge badge-danger">X</span>
										<?php if (isset($dv['title']) && !empty($dv['title']))
											echo ucfirst($dv['title']) . '<br />';
										?>
										<?php if (isset($dv['entity']) && !empty($dv['entity']))
											echo '<strong>' . ucfirst($dv['entity']) . '</strong> - ';
										?>
										<?php echo $dv['measurement'] . ' ' . ucfirst($dv['measurement_type']);
										?>
										<input type="hidden" name="distance[West][title][]" value="<?php echo $dv['title'] ?>">
										<input type="hidden" name="distance[West][entity][]" value="<?php echo $dv['entity'] ?>">
										<input type="hidden" name="distance[West][measurement][]" value="<?php echo $dv['measurement'] ?>">
										<input type="hidden" name="distance[West][measurement_type][]" value="<?php echo $dv['measurement_type'] ?>">
									</li>
							<?php }
							} ?>
						</ul>
					</div>
					<button type="button" class="btn btn-<?php echo get_skin_class(); ?> btn-sm " title="Add Distance" data-toggle="modal" data-target=".direction-modal" data-direction="West"><i class="fa fa-plus"></i></button>
				</div>
				<div class="col-md-4 text-center direction-block center-block">
					<i class="fa fa-building fa-3x"></i>
					<i class="arrow top-left"></i>
					<i class="arrow top-center"></i>
					<i class="arrow top-right"></i>
					
					<i class="arrow center-left"></i>
					<i class="arrow center-right"></i>

					<i class="arrow bottom-left"></i>
					<i class="arrow bottom-center"></i>
					<i class="arrow bottom-right"></i>
				</div>
				<div class="col-md-4 text-center direction-block East-block">
					<h4><?php echo mlx_get_lang('East'); ?></h4>
					<div class="direction-listing">
						<ul class="list-group">
							<?php if (isset($saved_distance_list) && isset($saved_distance_list['East']) && !empty($saved_distance_list['East'])) {
								foreach ($saved_distance_list['East'] as $dk => $dv) {
							?>
									<li class="list-group-item">
										<span class="badge badge-danger">X</span>
										<?php if (isset($dv['title']) && !empty($dv['title']))
											echo ucfirst($dv['title']) . '<br />';
										?>
										<?php if (isset($dv['entity']) && !empty($dv['entity']))
											echo '<strong>' . ucfirst($dv['entity']) . '</strong> - ';
										?>
										<?php echo $dv['measurement'] . ' ' . ucfirst($dv['measurement_type']);
										?>
										<input type="hidden" name="distance[East][title][]" value="<?php echo $dv['title'] ?>">
										<input type="hidden" name="distance[East][entity][]" value="<?php echo $dv['entity'] ?>">
										<input type="hidden" name="distance[East][measurement][]" value="<?php echo $dv['measurement'] ?>">
										<input type="hidden" name="distance[East][measurement_type][]" value="<?php echo $dv['measurement_type'] ?>">
									</li>
							<?php }
							} ?>
						</ul>
					</div>
					<button type="button" class="btn btn-<?php echo get_skin_class(); ?> btn-sm " title="Add Distance" data-toggle="modal" data-target=".direction-modal" data-direction="East"><i class="fa fa-plus"></i></button>
				</div>
			</div>

			<div class="row no-gutters">
				<div class="col-md-4 text-center direction-block South-West-block">
					<h4><?php echo mlx_get_lang('South-West'); ?></h4>

					<div class="direction-listing">
						<ul class="list-group">
							<?php if (isset($saved_distance_list) && isset($saved_distance_list['South-West']) && !empty($saved_distance_list['South-West'])) {
								foreach ($saved_distance_list['South-West'] as $dk => $dv) {
							?>
									<li class="list-group-item">
										<span class="badge badge-danger">X</span>
										<?php if (isset($dv['title']) && !empty($dv['title']))
											echo ucfirst($dv['title']) . '<br />';
										?>
										<?php if (isset($dv['entity']) && !empty($dv['entity']))
											echo '<strong>' . ucfirst($dv['entity']) . '</strong> - ';
										?>
										<?php echo $dv['measurement'] . ' ' . ucfirst($dv['measurement_type']);
										?>
										<input type="hidden" name="distance[South-West][title][]" value="<?php echo $dv['title'] ?>">
										<input type="hidden" name="distance[South-West][entity][]" value="<?php echo $dv['entity'] ?>">
										<input type="hidden" name="distance[South-West][measurement][]" value="<?php echo $dv['measurement'] ?>">
										<input type="hidden" name="distance[South-West][measurement_type][]" value="<?php echo $dv['measurement_type'] ?>">
									</li>
							<?php }
							} ?>
						</ul>
					</div>

					<button type="button" class="btn btn-<?php echo get_skin_class(); ?> btn-sm " title="Add Distance" data-toggle="modal" data-target=".direction-modal" data-direction="South-West"><i class="fa fa-plus"></i></button>
				</div>

				<div class="col-md-4 text-center direction-block South-block">
					<h4><?php echo mlx_get_lang('South'); ?></h4>
					<div class="direction-listing">
						<ul class="list-group">
							<?php if (isset($saved_distance_list) && isset($saved_distance_list['South']) && !empty($saved_distance_list['South'])) {
								foreach ($saved_distance_list['South'] as $dk => $dv) {
							?>
									<li class="list-group-item">
										<span class="badge badge-danger">X</span>
										<?php if (isset($dv['title']) && !empty($dv['title']))
											echo ucfirst($dv['title']) . '<br />';
										?>
										<?php if (isset($dv['entity']) && !empty($dv['entity']))
											echo '<strong>' . ucfirst($dv['entity']) . '</strong> - ';
										?>
										<?php echo $dv['measurement'] . ' ' . ucfirst($dv['measurement_type']);
										?>
										<input type="hidden" name="distance[South][title][]" value="<?php echo $dv['title'] ?>">
										<input type="hidden" name="distance[South][entity][]" value="<?php echo $dv['entity'] ?>">
										<input type="hidden" name="distance[South][measurement][]" value="<?php echo $dv['measurement'] ?>">
										<input type="hidden" name="distance[South][measurement_type][]" value="<?php echo $dv['measurement_type'] ?>">
									</li>
							<?php }
							} ?>
						</ul>
					</div>
					<button type="button" class="btn btn-<?php echo get_skin_class(); ?> btn-sm " title="Add Distance" data-toggle="modal" data-target=".direction-modal" data-direction="South"><i class="fa fa-plus"></i></button>
				</div>
				<div class="col-md-4 text-center direction-block South-East-block">
					<h4><?php echo mlx_get_lang('South-East'); ?></h4>
					<div class="direction-listing">
						<ul class="list-group">
							<?php if (isset($saved_distance_list) && isset($saved_distance_list['South-East']) && !empty($saved_distance_list['South-East'])) {
								foreach ($saved_distance_list['South-East'] as $dk => $dv) {
							?>
									<li class="list-group-item">
										<span class="badge badge-danger">X</span>
										<?php if (isset($dv['title']) && !empty($dv['title']))
											echo ucfirst($dv['title']) . '<br />';
										?>
										<?php if (isset($dv['entity']) && !empty($dv['entity']))
											echo '<strong>' . ucfirst($dv['entity']) . '</strong> - ';
										?>
										<?php echo $dv['measurement'] . ' ' . ucfirst($dv['measurement_type']);
										?>
										<input type="hidden" name="distance[South-East][title][]" value="<?php echo $dv['title'] ?>">
										<input type="hidden" name="distance[South-East][entity][]" value="<?php echo $dv['entity'] ?>">
										<input type="hidden" name="distance[South-East][measurement][]" value="<?php echo $dv['measurement'] ?>">
										<input type="hidden" name="distance[South-East][measurement_type][]" value="<?php echo $dv['measurement_type'] ?>">
									</li>
							<?php }
							} ?>
						</ul>
					</div>
					<button type="button" class="btn btn-<?php echo get_skin_class(); ?> btn-sm " title="Add Distance" data-toggle="modal" data-target=".direction-modal" data-direction="South-East"><i class="fa fa-plus"></i></button>
				</div>
			</div>

		</div>

		<script>
			$(document).ready(function() {
				$('.direction-modal').on('show.bs.modal', function(event) {
					var button = $(event.relatedTarget);
					var modal = $(this);
					var direction = button.attr('data-direction');

					var callback = 'manage_direction';
					$.ajax({
						url: base_url + 'admin_ajax',
						type: 'POST',
						success: function(res) {
							modal.find('.modal-content').append(res.modal_content);
							modal.find('.modal-overlay').hide();
						},
						data: {
							direction: direction,
							callback: callback
						},
						cache: false
					});

				});

				$('.direction-modal').on('hidden.bs.modal', function(event) {
					var modal = $(this);
					modal.find('.modal-content').html('');
					modal.find('.modal-overlay').show();
				});

				$(document).delegate('.direction-modal-form', 'submit', function() {

					var thiss = $(this);
					var callback = 'add_direction';
					var direction = thiss.find('input[type="hidden"][name="direction"]').val();
					$.ajax({
						url: base_url + 'admin_ajax',
						type: 'POST',
						success: function(res) {
							$('.' + direction + '-block').find('.list-group').append(res.output);
							$('.direction-modal').modal('hide');
						},
						data: thiss.serialize() + '&callback=' + callback,
						cache: false
					});
					return false;
				});

				$('.direction-listing .list-group').sortable();

				$(document).delegate('.direction-listing .list-group-item span.badge', 'click', function() {
					if (confirm('Do you really want to delete?')) {
						var thiss = $(this);
						thiss.parents('.list-group-item').remove();
					}
				});
			});
		</script>


	<?php //} ?>
	</div>
	
</div>
<?php } 	?>


<?php 	}	?>					
