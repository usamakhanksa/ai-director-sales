<?php 
global $widget , $widget_key , $current_widget;
//echo $widget_key;
//print_r($widget);
if(is_array($current_widget)){
	
	$sidebar_for =   $current_widget['sidebar_for'];
	$sidebar_widget =   $current_widget['sidebar_widget'];
	$sidebar_options =   $current_widget['sidebar_options'];
}else{
	/*$sidebar_for =   $current_widget['sidebar_for'];
	$sidebar_widget =   $current_widget['sidebar_widget'];
	$sidebar_options = array();*/
	
}

	if(!isset($widget_key)) $widget_key = 1; else $widget_key += 1;
?>					
<div class="box box-default collapsed-box search-widget widget" id="search-widget<?php if($widget_key != '')echo "-".$widget_key; ?>">
<div class="box-header with-border">
	<h3 class="box-title">Search</h3>
	<div class="box-tools pull-right">
		<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
		<!--<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>-->
		
	</div>
</div><!-- /.box-header -->
<div class="box-body" style="display: none;">
<form action="" method="post" name="search" class="search sidebar-widget" >
<input type="hidden" name="sidebar_for" value="<?php if(isset($sidebar_for)) echo $sidebar_for; ?>" class="sidebar_for" /> 
<input type="hidden" name="sidebar_widget" value="search-widget" class="" /> 
<input type="hidden" name="widget_id" value="<?php if($widget_key != '')echo $widget_key; ?>" class="" /> 


<div class="row">
	<div class="col-md-12">
		<div class="checkbox">
		  <label>
			<input type="checkbox" name="enable_advance_search" checked value="yes"> Enable Advance Search
		  </label>
		</div>

	</div><!-- /.col -->
	
	<div class="col-md-12">
		
	</div><!-- /.col -->
	
</div><!-- /.row -->

</form>
</div><!-- /.box-body -->
<div class="box-footer" style="display: none;">
	
	<div class="box-tools">
		<div class="pull-left">
			<button type="button" class="button-link button-link-delete sidebar-widget-remove">Delete</button>
		</div>
		<div class="pull-right">
			<input type="submit" name="save" class="btn btn-primary" value="Save" >
		</div>
	</div>
	
	<!--<div class="pull-left"> For Calculate the Mortgage calculations for a Property. </div> -->
</div>
</div>	
