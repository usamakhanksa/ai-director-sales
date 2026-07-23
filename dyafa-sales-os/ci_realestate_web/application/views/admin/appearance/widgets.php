
<style type="text/css">

.sidebars .widget {
    border: solid 1px #d2d6de;
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

</style>

<?php

	$sidebars = array();
	$sidebars [] = array("title" => "Property Sidebar" , "class" => "sidebar-property-details", 
							"widget_id" => "sidebar-property-details-widgets");
	$sidebars [] = array("title" => "Page Sidebar" , "class" => "sidebar-page-details", 
							"widget_id" => "sidebar-page-details-widgets");
	$sidebars [] = array("title" => "Footer Sidebar" , "class" => "sidebar-footer" , 
							"widget_id" => "sidebar-footer-widgets" );
							
	
	
?>
<?php do_action('property_after_sidebar_widgets'); ?>

<div class="content-wrapper">
	<section class="content-header">
	  <h1><?php echo mlx_get_lang('Widgets'); ?></h1>
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
		//echo form_open_multipart('main/home_page',$attributes); ?>
		<div class="row ">
			<section class="connectedSortable">
			
			
			<div class="col-md-6  widget-collections">
			
			<?php 
			global  $widget_key;
			$widget_key = 1;
			
			$this->load->view("admin/appearance/mortgage-calculator-widget");
			$this->load->view("admin/appearance/related-properties-widget");
			$this->load->view("admin/appearance/recent-properties-widget");
			$this->load->view("admin/appearance/search-widget");
			?>
					
			</div>
			
			
			
			<div class="col-md-6 sidebars">
			
				<!--<div class="box box-info Sidebar sidebar-property-details" id="sidebar-property-details">
				<div class="box-header with-border">
				  <h3 class="box-title">Property Sidebar</h3>
				</div>--><!-- /.box-header -->
				<!-- form start -->
				<!--<div class="box-body" id="widgets-property-details">
					
					<?php //$this->load->view("default/appearance/mortgage-calculator-widget"); ?>
					<?php //$this->load->view("default/appearance/related-properties-widget"); ?>
					<?php //$this->load->view("default/appearance/mortgage-calculator-widget"); ?>
					
				</div>    
				</div>-->
					
				<?php 
				global $sidebar_widgets;
				
				foreach($sidebars as $sidebar) { 
				//print_r($site_widgets);
				
				$sidebar_id =  $sidebar['class'];
				//echo $sidebar['widget_id'];
				$sidebar_widgets = array();
				if(isset($site_widgets[$sidebar_id])){
					$sidebar_widgets = $site_widgets[$sidebar_id];
					//echo "<pre>";print_r($sidebar_widgets);echo "</pre>";
				}
				?>	
					
					
				<div class="box box-info Sidebar  <?php echo $sidebar['class'];?>" id="<?php echo $sidebar['class'];?>">
				<div class="box-header with-border">
				  <h3 class="box-title"><?php echo $sidebar['title'];?></h3>
				</div><!-- /.box-header -->
				<!-- form start -->
				<div class="box-body" id="<?php echo $sidebar['widget_id'];?>">
					
					<?php //$this->load->view("default/appearance/mortgage-calculator-widget"); 
						global $widget , $widget_key , $current_widget;
						foreach($sidebar_widgets as $widget_key => $current_widget){
							
							$widget = $current_widget;
							$widget_id = $current_widget['sidebar_widget'];
							$widget_key = $widget_key +1;
							
							$this->load->view("admin/appearance/".$widget_id); 
							
						}
					
					?>
					
					
				</div>    
				</div>	
				
				<?php } ?>
				
				
				
					
					
			</div><!-- sidebars -->
		
		
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




