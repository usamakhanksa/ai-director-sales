<?php 
	if(isset($options_list) && $options_list->num_rows()>0)
	{
		
		foreach($options_list->result() as $row)
		{
			${$row->option_key} = $row->option_value;
			
			if($row->option_key == 'site_plugins')
			{
				${$row->option_key} = json_decode($row->option_value,true);
			}
			
		}
	}
	
?>
<script>
$(window).on('load', function () {
	var hash = window.location.hash;
	if (hash && hash != '') {
		$('#myList a[href="'+hash+'"').trigger('click');
	}
});

$(document).ready(function() {
	$('#myList a').on('click', function (e) {
	  e.preventDefault()
	  $('#myList a').removeClass('active');
	  $(this).addClass('active');
	  $(this).tab('show');
	});
});
</script>
      <div class="content-wrapper">
       <section class="content-header">
          <h1 class="page-title"><i class="fa fa-cog"></i> <?php echo mlx_get_lang('General Settings'); ?> </h1>
          <?php echo validation_errors(); 
			if(isset($_SESSION['msg']) && !empty($_SESSION['msg']))
			{
				echo $_SESSION['msg'];
				unset($_SESSION['msg']);
			}
			?>
        </section>

        <section class="content">
		   <?php 
			$attributes = array('name' => 'add_form_post','class' => 'setting-form');		 			
			echo form_open_multipart('admin/settings/general_settings',$attributes); 
			
			?>
			<input type="hidden" name="user_id" class="user_id" value="<?php echo $this->session->userdata('user_id'); ?>">	
			
			<div class="row">
				<div class="col-md-4">
					<div class="list-group sticky_sidebar" id="myList" role="tablist">
		<?php 
			if(isset($general_settings_sections)){
				$current = " active ";
				foreach($general_settings_sections as $key => $sections){
					?>
				<a class="list-group-item list-group-item-action <?php echo $current;?>" data-toggle="list" href="#<?php echo $key?>" role="tab">
				<?php echo mlx_get_lang($sections['title']); ?></a>

				<?php	$current = "";
				}
			}

		?>			
					</div>
				</div>
			
			
				<div class="col-md-8">   
					<div class="tab-content">
			   
				<?php 
					if(isset($general_settings_sections)){
						$current = " active ";
						foreach($general_settings_sections as $key => $sections){
				?>
						<div class="tab-pane <?php echo $current;?>" id="<?php echo $key;?>" role="tabpanel">
							<div class="box box-<?php echo get_skin_class(); ?> ">
							<div class="box-header with-border">
							  <h3 class="box-title"><?php echo mlx_get_lang($sections['title']); ?></h3>
							  
							</div>
							<div class="box-body">
								<?php 	
								/*echo $CI->theme."/settings/". $sections['template'].".php";
								echo base_url('views/'.$CI->theme."/settings/". $sections['template'].".php");*/
								//if(file_exists($CI->theme."/settings/". $sections['template'].".php"))
								/*if (file_exists(base_url('views/'.$CI->theme."/settings/". $sections['template'].".php")))
									$this->load->view($CI->theme."/settings/". $sections['template']);
								else
									echo "no ";*/
								if(isset($sections['template'])  && !empty($sections['template']))	
									$this->load->view($CI->theme."/settings/". $sections['template']);
								else	if(isset($sections['module_template'])  && !empty($sections['module_template']))	
									$this->load->view($sections['module_template']);	
								?>
							</div>
								
							<div class="box-footer">
								<button name="submit" type="submit" class="btn btn-<?php echo get_skin_class(); ?> pull-right" id="save_publish"><?php echo mlx_get_lang('Save Changes'); ?></button>
							  </div>	
							</div>
						</div>

				<?php	$current = "";
						}
					}
				?>		
			   
					</div>	<!-- tab-content -->
				</div> <!-- col-md-8 -->
		  </div>
			</form>
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
<?php

	echo link_tag("application/views/$theme/assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css");
	echo script_tag("application/views/$theme/assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js");

?>
<script>
	$(document).ready(function(){
		
		$('.wysihtml_editor_elem').each(function() {
			$(this).wysihtml5();
		});
		
		var validator = $('.setting-form').validate({
			errorClass: "invalid",
			ignore: false,
			invalidHandler: function(e,validator) {
				for (var i=0;i<validator.errorList.length;i++){
					if(!$(validator.errorList[i].element).parents('.tab-pane[role="tabpanel"]').hasClass('active'))
					{
						
						var cur_id = $(validator.errorList[i].element).parents('.tab-pane[role="tabpanel"]').attr('id');
						$('.list-group a.list-group-item[href="#'+cur_id+'"]').addClass('errorParentClass')
					}
				}
			},
			submitHandler: function(form) {
				if ($(form).valid()) 
					return true;
			},
			highlight: function(element) {
				$(element).closest('.form-group').addClass('has-error');
			},
			unhighlight: function(element) {
				$(element).closest('.form-group').removeClass('has-error');
				var cur_id = $(element).parents('.tab-pane[role="tabpanel"]').attr('id');
				$('.list-group a.list-group-item[href="#'+cur_id+'"]').removeClass('errorParentClass')
			},
			errorPlacement: function(error, element) {
				if(element.hasClass('select2_elem') && element.next('.select2-container').length) {
					error.insertAfter(element.next('.select2-container'));
				}
				else if(element.hasClass('minimal') && element.parents('.form-group').length)
				{
					error.appendTo(element.parents('.form-group')).css('display','block');
				}
				else if (element.parent('.custom-file-upload').length && element.parents('.form-group').length) {
					error.appendTo(element.parents('.form-group')).css('display','block');
				}
				else if(element.hasClass('wysihtml_editor_elem') && element.parents('.form-group').length)
				{
					error.appendTo(element.parents('.form-group')).css('display','block');
				}
				else if (element.parent('.input-group').length) {
					error.insertAfter(element.parent());
				}
				else if (element.prop('type') === 'radio' && element.parent('.radio-inline').length) {
					error.insertAfter(element.parent().parent());
				}
				else if (element.prop('type') === 'checkbox' || element.prop('type') === 'radio') {
					error.appendTo(element.parent().parent());
				}
				else {
					error.insertAfter(element);
				}
			}
		});

	});
</script>