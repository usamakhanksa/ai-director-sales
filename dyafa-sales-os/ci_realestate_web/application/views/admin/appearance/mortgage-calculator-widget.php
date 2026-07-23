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
					
<div class="box box-default collapsed-box mortgage-calc-widget widget" id="mortgage-calc-widget">
<div class="box-header with-border">
	<h3 class="box-title">Mortgage Calculator</h3>
	<div class="box-tools pull-right">
		<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
		<!--<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>-->
	</div>
</div><!-- /.box-header -->
<div class="box-body" style="display: none;">
<form action="" method="post" name="mortgage-calc" class="mortgage-calc sidebar-widget" >
<input type="hidden" name="sidebar_for" value="" class="sidebar_for" /> 
<div class="row">
	<div class="col-md-12">
		<div class="form-group">
		<label>Minimal</label>
		<select class="form-control select2 select2-hidden-accessible" style="width: 100%;" tabindex="-1" aria-hidden="true">
		  <option selected="selected">Alabama</option>
		  <option>Alaska</option>
		  <option>California</option>
		  <option>Delaware</option>
		  <option>Tennessee</option>
		  <option>Texas</option>
		  <option>Washington</option>
		</select><span class="select2 select2-container select2-container--default" dir="ltr" style="width: 100%;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-autocomplete="list" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-labelledby="select2-t2nq-container"><span class="select2-selection__rendered" id="select2-t2nq-container" title="Alabama">Alabama</span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
		</div><!-- /.form-group -->

		<div class="form-group">
		<label>Disabled</label>
		<select class="form-control select2 select2-hidden-accessible" disabled="" style="width: 100%;" tabindex="-1" aria-hidden="true">
		  <option selected="selected">Alabama</option>
		  <option>Alaska</option>
		  <option>California</option>
		  <option>Delaware</option>
		  <option>Tennessee</option>
		  <option>Texas</option>
		  <option>Washington</option>
		</select><span class="select2 select2-container select2-container--default select2-container--disabled" dir="ltr" style="width: 100%;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-autocomplete="list" aria-haspopup="true" aria-expanded="false" tabindex="-1" aria-labelledby="select2-g97c-container"><span class="select2-selection__rendered" id="select2-g97c-container" title="Alabama">Alabama</span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
		</div><!-- /.form-group -->
	</div><!-- /.col -->
	 
</div><!-- /.row -->

</form>
</div><!-- /.box-body -->
<div class="box-footer" style="display: none;">
For Calculate the Mortgage calculations for a Property.
</div>
</div>	
