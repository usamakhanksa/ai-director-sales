<div class="col-md-9 col-sm-6 col-xs-12 
dashboard-widget_total_enquiries" data-callback="<?php if (isset($widget_callback)) echo $widget_callback; ?>">
	<div class="box box-warning">
		<div class="box-header with-border">
			<!-- <span class="info-box-icon"><i class="fa fa-question bg-green"></i></span> -->
			<h3 class="box-title"><?php if (isset($widget_title)) echo mlx_get_lang($widget_title); ?></h3>
		</div>
		<!-- /.box-header -->
		<div class="box-body">
			<div class="row">
				<div class="col-md-12">
					<table id="enquiries" class="table table-bordered table-hover ">
						<thead>
							<tr>
								<th class="pad-right-5" width="100px" data-orderable="false"><?php echo mlx_get_lang('S No.'); ?></th>
								<th class="pad-right-5"><?php echo mlx_get_lang('First Names'); ?></th>
								<th class="pad-right-5"><?php echo mlx_get_lang('Emails'); ?></th>
								<th class="pad-right-5" data-orderable="false"><?php echo mlx_get_lang('Subjects'); ?></th>
								<th class="pad-right-5" data-orderable="false"><?php echo mlx_get_lang('Messages'); ?></th>
								<th class="pad-right-5" width="100px" data-orderable="false"><?php echo mlx_get_lang('Created At'); ?></th>

							</tr>
						</thead>
						<tbody>
						</tbody>

					</table>
				</div>
				<!-- /.col -->
			</div>
			<!-- /.row -->
		</div>
		<!-- ./box-body -->
	</div>
</div>



<!-- <div class="row"></div> -->