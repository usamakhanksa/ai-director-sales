<?php if (isset($single_property->distance_list) && !empty($single_property->distance_list)) {
	$distance_list = json_decode($single_property->distance_list, true);

	if (!empty($distance_list)) {


?>
		<h2 class="h4 text-black no-gutters mt-5  text-left"><?php echo mlx_get_lang('Distances'); ?></h2>

		<div class="direction_code distance_block">

			<div class="row no-gutters">
				<div class="col-md-4 text-center direction-block North-West-block">
					<h4><?php echo mlx_get_lang('North-West'); ?></h4>
					<div class="direction-listing">
						<ul class="list-group">
							<?php if (isset($distance_list) && isset($distance_list['North-West']) && !empty($distance_list['North-West'])) {
								foreach ($distance_list['North-West'] as $dk => $dv) {
							?>
									<li class="list-group-item">
										<?php
										$description = '';
										if (isset($dv['title']) && !empty($dv['title']))
											$description .= ucfirst($dv['title']);
										
										?>
										<?php if (isset($dv['entity']) && !empty($dv['entity']))
											$description .= ' (<strong>' . ucfirst($dv['entity']) .'</strong>) - ';
										?>
										<?php $description .= $dv['measurement'] . ' ' . ucfirst($dv['measurement_type']);
										echo $description;
										?>
									</li>
							<?php }
							} ?>
						</ul>
					</div>

				</div>
				<div class="col-md-4 text-center direction-block North-block">
					<h4><?php echo mlx_get_lang('North'); ?></h4>
					<div class="direction-listing">
						<ul class="list-group">
							<?php if (isset($distance_list) && isset($distance_list['North']) && !empty($distance_list['North'])) {
								foreach ($distance_list['North'] as $dk => $dv) {
							?>
									<li class="list-group-item">
										<?php
										$description = '';
										if (isset($dv['title']) && !empty($dv['title']))
											$description .= ucfirst($dv['title']);
										
										?>
										<?php if (isset($dv['entity']) && !empty($dv['entity']))
											$description .= ' (<strong>'.ucfirst($dv['entity']) .'</strong>) - ';
										?>
										<?php $description .= $dv['measurement'] . ' ' . ucfirst($dv['measurement_type']);
										echo $description;
										?>
									</li>
							<?php }
							} ?>
						</ul>
					</div>

				</div>
				<div class="col-md-4 text-center direction-block North-East-block">
					<h4><?php echo mlx_get_lang('North-East'); ?></h4>
					<div class="direction-listing">
						<ul class="list-group">
							<?php if (isset($distance_list) && isset($distance_list['North-East']) && !empty($distance_list['North-East'])) {
								foreach ($distance_list['North-East'] as $dk => $dv) {
							?>
									<li class="list-group-item">
										<?php
										$description = '';
										if (isset($dv['title']) && !empty($dv['title']))
											$description .= ucfirst($dv['title']);
										
										?>
										<?php if (isset($dv['entity']) && !empty($dv['entity']))
											$description .= ' (<strong>'.ucfirst($dv['entity']) . '</strong>) - ';
										?>
										<?php $description .= $dv['measurement'] . ' ' . ucfirst($dv['measurement_type']);
										echo $description;
										?>
									</li>
							<?php }
							} ?>
						</ul>
					</div>

				</div>
			</div>


			<div class="row no-gutters">
				<div class="col-md-4 text-center direction-block West-block">
					<h4><?php echo mlx_get_lang('West'); ?></h4>
					<div class="direction-listing">
						<ul class="list-group">
							<?php if (isset($distance_list) && isset($distance_list['West']) && !empty($distance_list['West'])) {
								foreach ($distance_list['West'] as $dk => $dv) {
							?>
									<li class="list-group-item">
										<?php
										$description = '';
										if (isset($dv['title']) && !empty($dv['title']))
											$description .= ucfirst($dv['title']);
										
										?>
										<?php if (isset($dv['entity']) && !empty($dv['entity']))
											$description .= ' (<strong>'.ucfirst($dv['entity']) . '</strong>) - ';
										?>
										<?php $description .= $dv['measurement'] . ' ' . ucfirst($dv['measurement_type']);
										echo $description;
										?>

									</li>
							<?php }
							} ?>
						</ul>
					</div>

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
							<?php if (isset($distance_list) && isset($distance_list['East']) && !empty($distance_list['East'])) {
								foreach ($distance_list['East'] as $dk => $dv) {
							?>
									<li class="list-group-item">
										<?php
										$description = '';
										if (isset($dv['title']) && !empty($dv['title']))
											$description .= ucfirst($dv['title']);
										
										?>
										<?php if (isset($dv['entity']) && !empty($dv['entity']))
											$description .= ' (<strong>'.ucfirst($dv['entity']) . '</strong>) - ';
										?>
										<?php $description .= $dv['measurement'] . ' ' . ucfirst($dv['measurement_type']);
										echo $description;
										?>

									</li>
							<?php }
							} ?>
						</ul>
					</div>

				</div>
			</div>

			<div class="row no-gutters">
				<div class="col-md-4 text-center direction-block South-West-block">
					<h4><?php echo mlx_get_lang('South-West'); ?></h4>

					<div class="direction-listing">
						<ul class="list-group">
							<?php if (isset($distance_list) && isset($distance_list['South-West']) && !empty($distance_list['South-West'])) {
								foreach ($distance_list['South-West'] as $dk => $dv) {
							?>
									<li class="list-group-item">
										<?php
										$description = '';
										if (isset($dv['title']) && !empty($dv['title']))
											$description .= ucfirst($dv['title']);
										
										?>
										<?php if (isset($dv['entity']) && !empty($dv['entity']))
											$description .= ' (<strong>'.ucfirst($dv['entity']) . '</strong>) - ';
										?>
										<?php $description .= $dv['measurement'] . ' ' . ucfirst($dv['measurement_type']);
										echo $description;
										?>

									</li>
							<?php }
							} ?>
						</ul>
					</div>


				</div>

				<div class="col-md-4 text-center direction-block South-block">
					<h4><?php echo mlx_get_lang('South'); ?></h4>
					<div class="direction-listing">
						<ul class="list-group">
							<?php if (isset($distance_list) && isset($distance_list['South']) && !empty($distance_list['South'])) {
								foreach ($distance_list['South'] as $dk => $dv) {
							?>
									<li class="list-group-item">
										<?php
										$description = '';
										if (isset($dv['title']) && !empty($dv['title']))
											$description .= ucfirst($dv['title']);
										
										?>
										<?php if (isset($dv['entity']) && !empty($dv['entity']))
											$description .= ' (<strong>'.ucfirst($dv['entity']) . '</strong>) - ';
										?>
										<?php $description .= $dv['measurement'] . ' ' . ucfirst($dv['measurement_type']);
										echo $description;
										?>

									</li>
							<?php }
							} ?>
						</ul>
					</div>

				</div>
				<div class="col-md-4 text-center direction-block South-East-block">
					<h4><?php echo mlx_get_lang('South-East'); ?></h4>
					<div class="direction-listing">
						<ul class="list-group">
							<?php if (isset($distance_list) && isset($distance_list['South-East']) && !empty($distance_list['South-East'])) {
								foreach ($distance_list['South-East'] as $dk => $dv) {
							?>
									<li class="list-group-item">

										<?php
										$description = '';
										if (isset($dv['title']) && !empty($dv['title']))
											$description .= ucfirst($dv['title']);
										
										?>
										<?php if (isset($dv['entity']) && !empty($dv['entity']))
											$description .= ' (<strong>'.ucfirst($dv['entity']) . '</strong>) - ';
										?>
										<?php $description .= $dv['measurement'] . ' ' . ucfirst($dv['measurement_type']);
										echo $description;
										?>

									</li>
							<?php }
							} ?>
						</ul>
					</div>

				</div>
			</div>

		</div>

		<!-- <div class="row">
			<?php foreach ($distance_list as $k => $v) {


			?>
				<div class="col-md-6 text-left"><i class="fa fa-arrows"></i> <?php echo mlx_get_lang(ucfirst($k)); ?>

					<?php if ($v['direction'] != '') { ?>
						<small>(<?php echo mlx_get_lang(ucfirst($v['direction'])); ?>)</small>
					<?php } ?>

					: <strong><?php echo ucfirst($v['distance']); ?> <?php echo mlx_get_lang(ucfirst($v['distance_text'])); ?></strong>
				</div>
			<?php } ?>
		</div> -->
<?php }
} ?>



<style>
	.distance_block .direction-block {
		border: 1px solid #f4f4f4;
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
		padding: 15px;
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