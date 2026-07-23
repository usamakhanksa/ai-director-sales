 <div class="box box-<?php echo get_skin_class(); ?>">
	<div class="box-header with-border">
	  <h3 class="box-title"><?php echo mlx_get_lang('Social Media Details'); ?></h3>
	  <div class="box-tools pull-right">
		<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>
	  <div class="box-body">
		 
		<?php 

			$social_media = get_user_meta($user_ID,'social_media');
			if(!empty($social_media))
			{
				$social_media_array = json_decode($social_media,true);
			}
			
			
			foreach($social_medias as $key => $details){
			?>
			<div class="col-md-6">
				<div class="form-group">
				  <label for="<?php echo $key; ?>"><?php echo $details['title']; ?></label>
				  <div class="input-group">
				  <span class="input-group-addon">
						<input type="hidden" class="form-control "
						name="options[<?php echo $key; ?>][icon]"  
						value="<?php echo $details['fa-icon']; ?>" 
						>
					  <i class="fa <?php echo $details['fa-icon']; ?>"></i>
					</span> 
				  <input type="url" class="form-control "
				  name="user_meta[social_media][<?php echo $key; ?>][url]" id="<?php echo $key; ?>" 
				  value="<?php 
				  if(isset($social_media_array) && isset($social_media_array[$key]))
				  {
					  echo $social_media_array[$key]['url']; 
				  }
				  ?>"
				  >
				  <input type="hidden" name="user_meta[social_media][<?php echo $key; ?>][title]" 
				  value="<?php echo $details['title']; ?>">
				  <input type="hidden" name="user_meta[social_media][<?php echo $key; ?>][icon]" 
				  value="<?php echo $details['fa-icon']; ?>">
				  
				  </div>
				  
				</div>
			</div>
			<?php
			}
			?>
				
				
	</div>
</div>