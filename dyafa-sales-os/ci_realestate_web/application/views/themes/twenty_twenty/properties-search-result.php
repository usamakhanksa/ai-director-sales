<?php 
	
if(isset($search_properties) && $search_properties->num_rows() > 0){ ?>
<div class="site-section site-section-sm ">
  <div class="container">
	
	
	<?php
		$view = 'grid';
		if(isset($_GET['view']) && $_GET['view'] == 'list')
			$view = 'list';	
		else if(isset($_GET['view']) && $_GET['view'] == 'map')
			$view = 'map';	

		if($view == 'map')
		{
		?>
			<div class="row mb-4">
				<div class="col-md-12">
				<?php
					$has_lat_long_available = false;
					global $prop_row;	
					foreach($search_properties->result() as $prop_row){
						
						if(!empty($prop_row->lat) && !empty($prop_row->long))
						{
							$has_lat_long_available = true;
						}
					}
					include('property/template-part/single-property-map.php'); 
					
				?>
				</div>
			</div>
		<?php 
		}
		else
		{
			global $prop_row;
				if($view == 'grid')
				{
				?>
				<div class="row mb-5 justify-content-center">
				<?php } ?>
			
			<?php 
			
			foreach($search_properties->result() as $prop_row){ ?>
				
				<?php
					if(isset($_GET['view']) && $_GET['view'] == 'list')
					{
					?>
					<div class="row mb-4">
					<div class="col-md-12">
				<?php
						include('property/template-part/single-property-list.php'); 
					?>
					</div>
					</div>
					<?php 
					}else {
				?>
					<div class="col-md-6 col-lg-4 mb-4">
				<?php	
						include('property/template-part/single-property-grid.php'); 
				?>
				</div>
				
			<?php }
				} 
				if($view == 'grid')
					{
					?>
					</div >
					<?php } ?>		
		

				<?php if(isset($pagination_links)) { ?>
				<div class="row">
					<div class="col-md-12 text-center">
						<?php echo $pagination_links; ?>
					</div>
				</div>
				<?php } ?>
		<?php
		}
		?>
  </div>
</div>
<?php }else{ ?>
<div class="site-section site-section-sm bg-light">
  <div class="container">
		<div class="row justify-content-center">
		  <div class="col-md-12 text-center">
			  <h2><?php echo mlx_get_lang('No Property Found Related Your Search Criteria.'); ?></h2>
		  </div>
		</div>
  </div>
</div>
<?php } ?>