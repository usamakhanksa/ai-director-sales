
<style type="text/css">

	.sidebars .widget {
		border-left: solid 1px #f4f4f4;
		border-right: solid 1px #f4f4f4;
	}

	/*.widget-collections .widget .box-header .box-tools { display:none; }*/
	.widget-collections .widget .box-footer{ display:block!important;}

	.widgets-chooser ul.widgets-chooser-sidebars {
		margin: 0;
		list-style-type: none;
		max-height: 300px;
		overflow: auto;
	}

	.widgets-chooser li {
		padding: 10px 15px 10px 35px;
		border-bottom: 1px solid #ccc;
		background: #fff;
		margin: 0;
		cursor: pointer;
		outline: 0;
		position: relative;
		transition: background .2s ease-in-out;
	}

	.widgets-chooser li.widgets-chooser-selected {
		background: #00a0d2;
		color: #fff;
	}

	.handle {
		display: inline-block;
		cursor: move;
		margin: 0 5px;
		font-size:14px;
	}
	.ui-sortable-handle .box-header .box-title {
    	display: inline-block;
    	font-size: 16px;
	}
</style>

<?php //do_action('property_after_sidebar_widgets'); ?>

<div class="content-wrapper">
	<section class="content-header">
	  <h1 class="page-title"><?php echo mlx_get_lang('Property Layouts'); ?></h1>
	  <?php if(isset($_SESSION['msg']) && !empty($_SESSION['msg']))
			{
				echo $_SESSION['msg'];
				unset($_SESSION['msg']);
			}
	?> 
	</section>

	<section class="content">  
		<?php 
		$attributes = array('name' => 'add_form_post','class' => 'homepage_section_form');		 			
		echo form_open_multipart('',$attributes); ?>
		<div class="row ">
			<section class="connectedSortable">

				
				<?php 

				

				if(isset($property_sidebar_widgets_meta) && !empty($property_sidebar_widgets_meta))
				{
					$site_widgets['property_sidebar'] = sortArrayByArray($site_widgets['property_sidebar'],$property_sidebar_widgets_meta);
				}

				if(isset($property_content_widgets_meta) && !empty($property_content_widgets_meta))
				{
					$site_widgets['property_contents'] = sortArrayByArray($site_widgets['property_contents'],$property_content_widgets_meta);
				}
				?>

				<div class="col-md-6 sidebars">	
					<div class="box box-<?php echo get_skin_class(); ?> Sidebar  content-property-details" id="content-property-details">
						<div class="box-header with-border">
						<h3 class="box-title"><?php echo mlx_get_lang('Property Contents');?></h3>
						</div>
					
						<div class="box-body ui-sortable" id="content-property-details-widgets">
						<?php 
							if(isset($site_widgets) && isset($site_widgets['property_contents']) && !empty($site_widgets['property_contents']))
							{
								foreach($site_widgets['property_contents'] as $k=>$v)
								{
									$widget_key = $v['widget_key'];
									$widget_status = 'Y';
									$box_class = 'success';
									if(isset($property_content_widgets_meta) && !empty($property_content_widgets_meta) && array_key_exists($widget_key,$property_content_widgets_meta) && isset($property_content_widgets_meta[$widget_key]['status']) && $property_content_widgets_meta[$widget_key]['status'] == 'N') 
									{
										$widget_status = 'N';
										$box_class = 'danger';
									}

								?>
									<div class="box box-<?php echo $box_class; ?> collapsed-box widget" id="widget_<?php echo $widget_key; ?>">
										<div class="box-header with-border">
											<h4 class="box-title">
												<span class="handle ui-sortable-handle">
													<i class="fa fa-ellipsis-v"></i>
													<i class="fa fa-ellipsis-v"></i>
												</span>
												&nbsp;
											<?php echo ucfirst($v['widget_title']); ?></h4>
											<div class="box-tools pull-right">
												<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
											</div>
										</div>
										<div class="box-body" style="display: none;">

											

											<div class="radio_toggle_wrapper ">
												<input type="radio" 
												<?php if($widget_status == 'Y') 
													echo 'checked="checked"';
												?>
												 
												id="widget_<?php echo $widget_key; ?>_enable" value="Y" name="property_content[<?php echo $widget_key; ?>][status]" class="toggle-radio-button">
												<label for="widget_<?php echo $widget_key; ?>_enable"><?php echo mlx_get_lang('Enable'); ?></label>

												<input type="radio" id="widget_<?php echo $widget_key; ?>_disable" value="N" name="property_content[<?php echo $widget_key; ?>][status]" class="toggle-radio-button" 
												<?php if($widget_status == 'N')
													echo 'checked="checked"';
												?>>
												<label for="widget_<?php echo $widget_key; ?>_disable"><?php echo mlx_get_lang('Disable'); ?></label>
											</div>
										</div>
									</div>
								<?php
								}
								?>
								<input type="submit" name="submit" value="Submit" class="btn btn-<?php echo get_skin_class(); ?>">
								<?php
								
							}else{
							?>
								<h4 class="text-center"><?php echo mlx_get_lang('No Widget Available'); ?></h4>
							<?php } ?>
						</div>    
					</div>	
				</div>

				<div class="col-md-6 sidebars">	
					
					
					<div class="box box-<?php echo get_skin_class(); ?> Sidebar  sidebar-property-details" id="sidebar-property-details">
						<div class="box-header with-border">
						<h3 class="box-title"><?php echo mlx_get_lang('Property Sidebar');?></h3>
						</div>
						
						<div class="box-body ui-sortable" id="sidebar-property-details-widgets">
							<?php 
							if(isset($site_widgets) && isset($site_widgets['property_sidebar']) && !empty($site_widgets['property_sidebar']))
							{
								foreach($site_widgets['property_sidebar'] as $k=>$v)
								{
									$widget_key = $v['widget_key'];
									$widget_status = 'Y';
									$box_class = 'success';
									if(isset($property_sidebar_widgets_meta) && !empty($property_sidebar_widgets_meta) && array_key_exists($widget_key,$property_sidebar_widgets_meta) && isset($property_sidebar_widgets_meta[$widget_key]['status']) && $property_sidebar_widgets_meta[$widget_key]['status'] == 'N') 
									{
										$widget_status = 'N';
										$box_class = 'danger';
									}

								?>
									<div class="box box-<?php echo $box_class; ?> collapsed-box widget" id="widget_<?php echo $widget_key; ?>">
										<div class="box-header with-border">
											<h4 class="box-title">
												<span class="handle ui-sortable-handle">
													<i class="fa fa-ellipsis-v"></i>
													<i class="fa fa-ellipsis-v"></i>
												</span>
												&nbsp;
											<?php echo ucfirst($v['widget_title']); ?></h4>
											<div class="box-tools pull-right">
												<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
											</div>
										</div>
										<div class="box-body" style="display: none;">

											

											<div class="radio_toggle_wrapper ">
												<input type="radio" 
												<?php if($widget_status == 'Y') 
													echo 'checked="checked"';
												?>
												 
												id="widget_<?php echo $widget_key; ?>_enable" value="Y" name="property_sidebar[<?php echo $widget_key; ?>][status]" class="toggle-radio-button">
												<label for="widget_<?php echo $widget_key; ?>_enable"><?php echo mlx_get_lang('Enable'); ?></label>

												<input type="radio" id="widget_<?php echo $widget_key; ?>_disable" value="N" name="property_sidebar[<?php echo $widget_key; ?>][status]" class="toggle-radio-button" 
												<?php if($widget_status == 'N')
													echo 'checked="checked"';
												?>>
												<label for="widget_<?php echo $widget_key; ?>_disable"><?php echo mlx_get_lang('Disable'); ?></label>
											</div>
										</div>
									</div>
								<?php
								}
								?>
								<input type="submit" name="submit" value="Submit" class="btn btn-<?php echo get_skin_class(); ?>">
								<?php
								
							}else{
							?>
								<h4 class="text-center"><?php echo mlx_get_lang('No Widget Available'); ?></h4>
							<?php } ?>
						</div>    
					</div>	
				</div>

			<div class="col-md-12 text-right hide">

				<input type="submit" name="submit" value="Submit" class="btn btn-<?php echo get_skin_class(); ?>">
			</div>
			
			</section>
			
<script>
$(function () {

		$(".sidebars .Sidebar .box-body").each(function(k){
			var id = $(this).attr('id');
			
			$(this).sortable({
                connectWith: "#"+id,
                start: function (event, ui) {
                        ui.item.toggleClass("highlight");
						console.log('started');
                },
                stop: function (event, ui) {
                        ui.item.toggleClass("highlight");
						console.log('stopped');
                }
        	});	
			$("#"+id).disableSelection();
				
		});
		
		
		
		
		$(".widget-collections .widget").each(function(){
			
			var thiss = $(this);//.parent();
			$(this).find('.box-header').on("click",function(){
			//alert('');
				if(thiss.hasClass('hasWidgets'))
				{
					$('.widget-chooser-on').remove();
					thiss.removeClass('hasWidgets');
				}else{
				
					var haswidgets = $('.hasWidgets');
					 haswidgets.removeClass('hasWidgets');
					 $('.widget-chooser-on').remove();
					
					var widgets = $("#widgets-chooser").clone();
					widgets.addClass('widget-chooser-on').show();
					thiss.after(widgets);
					thiss.addClass('hasWidgets');
				}
			});
		});
		
		
		$(document).delegate(".sidebar-widget-remove","click",function(){
			
			//var widget_html = $(this).parent().parent().parent().parent();
			var widget_html = $(this).parents(".box").first();
			var widget_id = widget_html.attr('id');
			//widget_html.slideUp(1000);//.remove();
			
			//onsole.log(widget_html.html());
			//console.log(widget_id);
			var formData = widget_html.find("form")
					.serialize();
			//console.log(formData);		
			
			$.ajax({
				url: base_url+'ajax_widgets/remove_widget_from_sidebar_callback_func',
				type: 'POST',
				success: function (res) {
					console.log(res);
				},
				error:function(args1,args2,args3){
					console.log( "args1 " + args1 + "args2 " + args2 + "args3 " + args3 );
				},
				data: formData,
				cache: false,
				dataType: 'json',
			});
				
				
		});
		
		$(document).delegate(".widgets-chooser-add","click",function(){
			
			var widget_chooser = $(this).parent().parent();
			var sidebar = widget_chooser.find('.widgets-chooser-selected').attr('id');
			
			
			var widget_selected = $('.widget.hasWidgets').clone();
			
			if(sidebar != '')
			{
				widget_selected.find(".sidebar_for").val(sidebar);
				$("#"+sidebar + " > .box-body").append(widget_selected);
				
				var formData = widget_selected.find("form")
					.serialize();
				
				var callback = "save_widget_to_sidebar";
				
				$.ajax({
					/*url: base_url+'widget_lib/save_widget_to_sidebar_callback_func',*/
					url: base_url+'admin_ajax',
					type: 'POST',
					success: function (res) {
						console.log(res);
					},
					error:function(args1,args2,args3){
						console.log( "args1 " + args1 + "args2 " + args2 + "args3 " + args3 );
					},
					data: formData + "&callback=" + callback,
					cache: false,
					dataType: 'json',
				});
				
				
			}
			var haswidgets = $('.hasWidgets');
			 haswidgets.removeClass('hasWidgets');
			 $('.widget-chooser-on').remove();
			
		});
		
		$(document).delegate(".widgets-chooser-cancel","click",function(){
			
			var haswidgets = $('.hasWidgets');
			 haswidgets.removeClass('hasWidgets');
			 $('.widget-chooser-on').remove();
			
		});
		
		
		
		
		
		$(document).delegate(".widgets-chooser-sidebars li","click",function(){
			
			
			var current = $(this); 
			$(this).parent().find('li').removeClass('widgets-chooser-selected');
			current.addClass('widgets-chooser-selected');
		
		});
	
});
</script>			

<div class="widgets-chooser" id="widgets-chooser" style="display:none;">
	<ul class="widgets-chooser-sidebars">
		<?php 
			$first = 1;
			foreach($sidebars as $sidebar) {
			if($first == 1)
				$class = "widgets-chooser-selected";
			else
				$class = ""; 	
			$first++;	
		?>
		<li tabindex="0" id="<?php echo $sidebar['class'];?>" class="<?php echo $class;?>"><?php echo $sidebar['title'];?></li>
		<?php	
			}
		?>
	</ul>
 
<div class="widgets-chooser-actions">
	<button class="button widgets-chooser-cancel">Cancel</button>
	<button class="button button-primary widgets-chooser-add">Add Widget</button>
</div>
	
</div>

		
		</div>
		<!--</form>-->
	</section>
</div>




