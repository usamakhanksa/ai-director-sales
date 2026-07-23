<style>
.agent_block {
    text-align: center;
    background: #ffffff;
    padding: 35px 35px 30px 35px;
    box-shadow: 0px 3px 15px rgb(91 91 91 / 15%);
    
    -webkit-transition: all 0.3s;
    transition: all 0.3s;
	border:0px none;
}
.agent_block .agent-image-block {
    width: 151px;
    height: 151px;
    position: relative;
    margin: 0 auto;
    margin-bottom: 25px;
}
.agent_block .agent-image-block img {
    border-radius: 50%;
	width:100%;
}
.agent-detail-block h5{
	border-bottom: 1px solid #e1e1e1;
    padding-bottom: 15px;
    -webkit-transition: all 0.2s;
    transition: all 0.2s;
}
.agent-detail-block h5 a {
	
    color: #111111;
    text-transform: uppercase;
    font-weight: 700;
    -webkit-transition: all 0.2s;
    transition: all 0.2s;
	font-size: 16px;
}
.agent-detail-block ul {
    text-align: left;
    padding-top: 20px;
    margin-bottom: 16px;
	padding-left:0px;
}
.agent-detail-block ul li {
    list-style: none;
    font-size: 16px;
    color: #111111;
    font-weight: 500;
    line-height: 36px;
    overflow: hidden;
    -webkit-transition: all 22s;
    transition: all 0.2s;
}
.agent-detail-block ul li span {
    font-weight: 600;
    float: right;
}
.agent-detail-block .btn.custom-btn {
    background: #f2f2f2;
    color: #3a3a3a !important;
    display: block;
    padding: 14px 20px;
    text-align: center;
    -webkit-transition: all 0.2s;
    transition: all 0.2s;
    font-weight: bold;
}
</style>

<?php if (isset($banner_row) && isset($banner_row->b_image) && !empty($banner_row->b_image) && file_exists('uploads/banner/' . $banner_row->b_image)) { ?>
	<section class="page-top-section set-bg" data-setbg="<?php echo base_url(); ?>uploads/banner/<?php echo $banner_row->b_image; ?>" style="background-image: url(<?php echo base_url(); ?>uploads/banner/<?php echo $banner_row->b_image; ?>);">
		<div class="container text-white">
			<h1><?php
				if (isset($banner_title) && !empty($banner_title))
					echo mlx_get_lang($banner_title); ?>
			</h1>
		</div>
	</section>
<?php } ?>

<div class="site-section">
	<div class="container">

			<?php 
				if (isset($all_agents) && $all_agents->num_rows() > 0) {
			?>
					

					<div class="row mb-5">
						<?php

						foreach ($all_agents->result() as $a_row) {

							$p_images = $myHelpers->global_lib->get_user_meta($a_row->user_id, 'photo_url');
							if (!empty($p_images) && file_exists('uploads/user/' . $p_images)) {
								$user_image_url = base_url() . 'uploads/user/' . $p_images;
							} else {
								$user_image_url = $myHelpers->property_agent_image; //base_url() . 'application/views/' . $theme . '/assets/images/no-user-image.png';
							}
							$first_name = $myHelpers->global_lib->get_user_meta($a_row->user_id, 'first_name');
							$last_name = $myHelpers->global_lib->get_user_meta($a_row->user_id, 'last_name');
							$full_name = strtolower($first_name) . ' ' . strtolower($last_name);
							$agent_url = site_url(array('user', 'agent', $this->default_language, str_replace(' ', '-', strtolower($full_name)) . '~' . $this->global_lib->EncryptClientID($a_row->user_id)));
						?>
							<div class="col-md-4 col-lg-4 mb-4 text-center">
								<div class="agent_block">
									<div class="agent-image-block">
										
										<a href="<?php echo $agent_url; ?>">
											<img src="<?php echo $user_image_url; ?>" class="img-fluid img-thumbnail p-0">
										</a>
										
									</div>
									<div class="agent-detail-block px-2 py-2">
										<h5 class="mt-0"><a href="<?php echo $agent_url; ?>" class="text-dark"><?php echo ucfirst($first_name) . ' ' . ucfirst($last_name); ?></a></h5>

										<ul>
											<li>
												<?php echo mlx_get_lang('Active Properties');?> 
												<span><?php echo $a_row->total_property; ?></span>
											</li>
										</ul>

										<a href="<?php echo $agent_url; ?>" class="btn custom-btn btn-block rounded-0 text-white">View Details</a>
									</div>
								</div>
							</div>
						<?php } ?>
					</div>
			<?php 
			}
			?>
			
			<?php if (isset($pagination_links)) { ?>
				<div class="row">
					<div class="col-md-12 text-center">
						<?php echo $pagination_links; ?>
					</div>
				</div>
			<?php } ?>
			
	</div>
</div>