<?php $default_language = get_option('default_language'); ?>
<div class="content-wrapper">
<section class="content-header">
  <h1 class="page-title"><i class="fa fa-cog"></i> <?php echo mlx_get_lang('Admin Keyword Settings'); ?> 
  <a href="<?php echo base_url("admin/settings/manage_admin_keywords");?>" class="btn btn-<?php echo get_skin_class(); ?> pull-right content-header-right-link"><?php echo mlx_get_lang('Manage Keywords'); ?></a>
  
  <a style="margin-right:10px; margin-left:5px;" href="<?php echo base_url("admin/settings/import_admin_keywords");?>" 
		class="btn btn-<?php echo 	get_skin_class(); ?> pull-right content-header-right-link"><?php echo mlx_get_lang('Import Keywords'); ?></a>
  
  <a style="margin-right:5px; margin-left:5px;" href="<?php echo base_url("admin/settings/export_admin_keywords");?>" 
		class="btn btn-<?php echo 	get_skin_class(); ?> pull-right content-header-right-link"><?php echo mlx_get_lang('Export Keywords'); ?></a>
  </h1>
  <?php echo validation_errors(); 
	if(isset($_SESSION['msg']) && !empty($_SESSION['msg']))
	{
		echo $_SESSION['msg'];
		unset($_SESSION['msg']);
	}
	?>
</section>


<style type="text/css">

.fixed-tab {
    position: fixed;
    top: 60px;
    /*left: 0;*/
    width: 100%;
	z-index:1000;
	background-color:#fff;	
}
.form-horizontal .control-label{
	overflow: hidden;
	white-space: nowrap;
	text-overflow: ellipsis;
}

</style>		
		
		
<section class="content">
	
	 
	<input type="hidden" name="user_id" class="user_id" value="<?php echo $this->session->userdata('user_id'); ?>">	
	<div class="row">
	<div class="col-md-12">   
	 
	  <?php if(isset($site_language) && !empty($site_language)) { 
		$site_language_array = json_decode($site_language,true);
		if(!empty($site_language_array)) { 
			
			foreach($site_language_array as $aak=>$aav)
			{
				if($aav['language'] == $default_language)
				{
					$new_value = $site_language_array[$aak];
					unset($site_language_array[$aak]);
					array_unshift($site_language_array, $new_value);
					break;
				}
			}
							
	  ?>
	 
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
			  <?php 
				$n=0;
				foreach($site_language_array as $k=>$v) { $n++; 
				$lang_exp = explode('~',$v['language']);
				$lang_code = $lang_exp[1];
				$lang_title = $lang_exp[0];
				?>
				<li <?php if($n == 1) echo 'class="active"'; ?>>
					<a href="#<?php echo $lang_code; ?>" data-toggle="tab"><?php echo ucfirst($lang_title); ?></a>
				</li>
			  <?php } ?>
			</ul>
			<div class="tab-content">
			  <?php 
				$n=0;
				foreach($site_language_array as $k=>$v) { $n++; 
					$lang_exp = explode('~',$v['language']);
					$lang_code = $lang_exp[1];
					$lang_title = $lang_exp[0];
					
					$lang_slug = $myHelpers->global_lib->get_slug($lang_title,'_');
				?>
					  <div class="<?php if($n == 1) echo 'active'; ?> tab-pane" id="<?php echo $lang_code; ?>">
						 <?php 
							$attributes = array('name' => 'add_form_post','class' => 'form-horizontal admin_keyword_settings_form');		 			
							echo form_open_multipart('',$attributes); 
						?>
						 <input type="hidden" name="lang_slug" value="<?php echo $lang_slug; ?>">
						 <input type="hidden" name="lang_code" class="lang_code" value="<?php echo $lang_code; ?>">
						 <div class="form-group">
							<div class="col-sm-12">
							  <button type="submit" name="update_lang_file" style="margin-left:5px;" class="btn btn-<?php echo get_skin_class(); ?> pull-right"><?php echo mlx_get_lang('Update Language File'); ?></button> 
							  <button type="submit" name="update_english_keywords" style="margin-left:5px;" class="btn btn-<?php echo get_skin_class(); ?> pull-right"><?php echo mlx_get_lang('Overright English Keywords'); ?></button>
							</div>
						  </div>
						
						  <?php 
						  $keyword_result = $myHelpers->Common_model->commonQuery("select keyword,lang_id,$lang_slug from languages where lang_for = 'back'
							order by lang_id DESC");
						  if($keyword_result->num_rows() > 0) 
						  { 
							foreach($keyword_result->result() as $row)
							{
						  ?>
							  <div class="form-group">
								<label for="<?php echo $row->keyword; ?>" class="col-sm-3 control-label"><?php echo ucfirst($row->keyword); ?></label>
								<div class="col-sm-9">
								  
									<input type="text" value="<?php if($lang_slug == 'english' && $row->$lang_slug == '') echo $row->keyword; else echo $row->$lang_slug; ?>" 
									class="form-control keywords" name="lang_ids[<?php echo $row->lang_id; ?>]" id="<?php echo $row->keyword; ?>" 
									data-lang_id="<?php echo $myHelpers->EncryptClientId($row->lang_id); ?>" data-lang_slug="<?php echo $lang_slug; ?>"
									>
									<i class="fa fa-spinner fa-spin" style="display:none;"></i>
								 
								</div>
							  </div>
						  <?php 
							}
						  } 
						  ?>
						  <div class="form-group">
							<div class="col-sm-offset-3 col-sm-9">
							  <button type="submit" name="lang_update" class="btn btn-<?php echo get_skin_class(); ?>"><?php echo mlx_get_lang('Submit'); ?></button>
							</div>
						  </div>
						</form>
					  </div>
				<?php } ?>
			</div>
		  </div>
		 
	  <?php }} ?>
  </div>
  
  </div>
  
	
</section>


</div>
      
	  
<script type="text/javascript">
jQuery(document).ready(function($){
	$(window).scroll(function(){
		if ($(window).scrollTop() >= 100) 
		{
			$('.nav.nav-tabs').addClass('fixed-tab');
		}
		else 
		{
			$('.nav.nav-tabs').removeClass('fixed-tab');
		}
	});
});
</script> 