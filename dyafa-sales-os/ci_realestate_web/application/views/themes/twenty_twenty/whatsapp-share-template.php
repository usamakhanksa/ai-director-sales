
<style>
.whatsapp-share-btn a i{
    display: inline-block;
    padding: 12px 12px 9px;
    text-align: center;
	color:#fff;
	font-size:14px;
	border-radius: 3px;
	cursor:pointer;
	background: #4FCE5D;
}
.share-whatsapp	{
    color : rgba(0, 0, 0, 0.6);
}
</style>
<div class="bg-white widget border rounded  text-left" id="whatsapp_link">
			  
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 mb-2">
			<h3 class="h4 text-black widget-title mb-3"><?php echo mlx_get_lang('Whatsapp Links'); ?></h3>
		</div>  
		<?php 
		if(isset($site_whatsapp_group_link) && !empty($site_whatsapp_group_link))
		{
		?>
		<div class="form-group whatsapp-share-btn">
			
			<a class="w-inline-block social-share-btn share-whatsapp" 
				title="Join Our Whatsapp Group" target="_blank" href="<?php echo $site_whatsapp_group_link; ?>">
				<i class="fa fa-whatsapp"></i> &nbsp;
				<?php echo mlx_get_lang('Join Site Whatsapp Group'); ?>
			</a>
		</div>
		<?php } ?>
		<?php 
		$whastapp_text = 'I want to get more detail about '.$property_title.' property. '.$property_url.'';
		?>
		
		<?php 
		if(isset($site_whatsapp_no) && !empty($site_whatsapp_no))
		{
		?>
		<div class="form-group whatsapp-share-btn">
			<a class="w-inline-block social-share-btn share-whatsapp" 
				title="Join Our Whatsapp Group" target="_blank" 
				href="https://api.whatsapp.com/send?phone=<?php echo $site_whatsapp_no; ?>&text=<?php echo urlencode($whastapp_text); ?>">
				<i class="fa fa-whatsapp"></i> &nbsp;
				<?php echo mlx_get_lang('Contact Site Owner'); ?>
			</a>
			
		</div>
		<?php } ?>
		
		<?php 
		if(isset($owner_whatsapp_no) && !empty($owner_whatsapp_no))
		{
		?>
		<div class="form-group whatsapp-share-btn">
			<a class="w-inline-block social-share-btn share-whatsapp" 
				title="Join Our Whatsapp Group" target="_blank" 
				href="https://api.whatsapp.com/send?phone=<?php echo $owner_whatsapp_no; ?>&text=<?php echo urlencode($whastapp_text); ?>">
				<i class="fa fa-whatsapp"></i> &nbsp;
				<?php echo mlx_get_lang('Contact Property Owner'); ?>
			</a>
			
		</div>
		<?php } ?>
		
		
		<div class="whatsapp-share-btn">
			<a class="w-inline-block social-share-btn share-whatsapp"
			href="https://api.whatsapp.com/send/?text=<?php echo urlencode($property_url); ?>" data-action="share/whatsapp/share">
			<i class="fa fa-whatsapp"></i> &nbsp;
			<?php echo mlx_get_lang('Share on Whatsapp'); ?></a>
		</div>
		
	</div>
</div>