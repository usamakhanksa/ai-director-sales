<?php $this->load->view("default/header-top");?>
<?php $this->load->view("default/sidebar-left");?>


<style type="text/css">

.sidebars .widget {
    border: solid 1px #d2d6de;
}

.widget-collections .widget .box-header .box-tools { display:none; }
.widget-collections .widget .box-footer{ display:block!important;}

</style>

<?php

	$sidebars = array();
	$sidebars [] = array("title" => "Property Sidebar" , "class" => "sidebar-property-details");
	$sidebars [] = array("title" => "Page Sidebar" , "class" => "sidebar-page-details");
	$sidebars [] = array("title" => "Footer Sidebar" , "class" => "sidebar-footer");
?>

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
			
			$this->load->view("default/appearance/mortgage-calculator-widget");
			$this->load->view("default/appearance/related-properties-widget");
			$this->load->view("default/appearance/recent-properties-widget");
			
			?>
					
			</div>
			
			
			
			<div class="col-md-6 sidebars">
			
				<div class="box box-info Sidebar sidebar-property-details" id="sidebar-property-details">
				<div class="box-header with-border">
				  <h3 class="box-title">Property Sidebar</h3>
				</div><!-- /.box-header -->
				<!-- form start -->
				<div class="box-body" id="widgets-property-details">
					
					<?php $this->load->view("default/appearance/mortgage-calculator-widget"); ?>
					<?php $this->load->view("default/appearance/related-properties-widget"); ?>
					<?php $this->load->view("default/appearance/mortgage-calculator-widget"); ?>
					
					<!--<ul id="items2" class="items">
					<li class="list g">Item 2-1</li>
    <li class="list g">Item 2-2</li>
    <li class="list g">Item 2-3</li>
    <li class="list g">Item 2-4</li>
    <li class="list g">Item 2-5</li>
    <li class="list g">Item 2-6</li>    
					</ul>-->
					
				</div>    
				</div>
					
					
				<div class="box box-info Sidebar  sidebar-page-details">
				<div class="box-header with-border">
				  <h3 class="box-title">Page Sidebar</h3>
				</div><!-- /.box-header -->
				<!-- form start -->
				<div class="box-body" id="widgets-page-details">
					
					<?php $this->load->view("default/appearance/mortgage-calculator-widget"); ?>
					
					
				</div>    
				</div>	
				
				<div class="box box-info Sidebar  sidebar-footer">
				<div class="box-header with-border">
				  <h3 class="box-title">Footer Sidebar</h3>
				</div><!-- /.box-header -->
				<!-- form start -->
				<div class="box-body" id="widgets-footer">
					
					<?php $this->load->view("default/appearance/mortgage-calculator-widget"); ?>
					
					
				</div>    
				</div>
					
					
					
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
		
		
		

       /* $("#items1,#items2,#items3").sortable({
                connectWith: "#items1,#items2,#items3",
                start: function (event, ui) {
                        ui.item.toggleClass("highlight");
                },
                stop: function (event, ui) {
                        ui.item.toggleClass("highlight");
                }
        });
        $("#items1,#items2,#items3").disableSelection();*/
});
</script>			

<!--<ul id="items1" class="items">
    <li class="list">
	<?php //$this->load->view("default/appearance/mortgage-calculator-widget"); ?>
	</li>
    
</ul>
<ul id="items2" class="items">
    <li class="list g"><?php //$this->load->view("default/appearance/mortgage-calculator-widget"); ?></li>
    
</ul>
<ul id="items3" class="items">
    <li class="list o"><?php //$this->load->view("default/appearance/mortgage-calculator-widget"); ?></li>
    
</ul>-->
<div class="widgets-chooser" id="widgets-chooser" style="display:none;">
	<ul class="widgets-chooser-sidebars">
		<li tabindex="0" class="">Blog Sidebar</li>
		<li tabindex="0" class="widgets-chooser-selected">Footer 1</li>
		<li tabindex="0">Footer 2</li>
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




