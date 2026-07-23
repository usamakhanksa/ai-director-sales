<?php 


/*$primary_menu = get_option('primary_menu');*/

$primary_menu = apply_filters("cms_get_details", '','primary_menu');


$sql = "select * from property_types where status = 'Y' order by title";
$property_type_list = $myHelpers->Common_model->commonQuery($sql );
?>
<div class="col-4 col-md-4 col-lg-8 menu-block" >
  <nav class="site-navigation text-right text-md-right" role="navigation">

	<div class="d-inline-block d-lg-none ml-md-0 mr-auto py-3"><a href="#" class="site-menu-toggle js-menu-toggle text-white"><span class="icon-menu h3"></span></a></div>

	<ul class="site-menu js-clone-nav d-none d-lg-block">
	  <?php 
	  if(isset($primary_menu) && !empty($primary_menu)) {
	  $menu_meta = json_decode($primary_menu,true);
	  
	  
	  $app_menu_static_pages = apply_filters("app_menu_static_pages_append_menu_items");
	  /*echo "<pre>"; print_r($app_menu_static_pages); echo "</pre>";*/
	  
	  foreach($menu_meta as $hmk=>$hmv)
	  {
		  $p_url = '#';
		  $menu_id_exp = explode('~',$hmv['id']);
		  $menu_type = $menu_id_exp[0];
		  $menu_slug = $menu_id_exp[1];
		  $active_class = '';
		  if($menu_type == 'static')
		  {
			 
			 
			  if(is_array($app_menu_static_pages) &&  array_key_exists($menu_slug, $app_menu_static_pages)){
					$menu_link =  $app_menu_static_pages[$menu_slug]['link']; 
			
				
					$p_url = $myHelpers->menu_lib->get_link_url($menu_slug , $menu_link);
			  }
			  
		  }
		  else if($menu_type == 'page')
		  {
			  
			  $page_slug = $myHelpers->global_lib->get_page_slug_by_id($menu_slug);
			  $p_url = $myHelpers->menu_lib->get_url('page='.$page_slug); 
		  }
		  else if($menu_type == 'custom_link')
		  {
			  $p_url = $menu_slug; 
		  }
	  ?>
			<li class="
			<?php 
			if(isset($hmv['children']) && !empty($hmv['children']))
			{
				echo 'has-children';
			}
			?> <?php echo $active_class; ?>"
			><a href="<?php echo $p_url  ; ?>"><?php echo mlx_get_lang($hmv['name']); ?></a>
				<?php 
				if(isset($hmv['children']) && !empty($hmv['children']))
				{
					echo '<ul class="dropdown arrow-top">';
					foreach($hmv['children'] as $hmvck=>$hmvcv)
					{
						
						$hm_ids = explode("~",$hmvcv['id']);
						$url_type = '';
						
						if(isset($hm_ids[0]) && $hm_ids[0] == 'custom_link')
						{
							if(isset($hm_ids[1]) && !empty($hm_ids[1]))
								$type_url = $hm_ids[1];
							else	continue;	
						}else if(isset($hm_ids[0]) && $hm_ids[0] == 'static')
						{
							
							if(isset($hm_ids[1]) && !empty($hm_ids[1]))
							{	
								$type_url = $hm_ids[1];
								$type_url = str_replace("-","_",$type_url );
								$type_url = $myHelpers->menu_lib->get_url($type_url);
							}else	continue;	
						}else if(isset($hm_ids[0]) && $hm_ids[0] == 'page')
						{
							if(isset($hm_ids[1]) && !empty($hm_ids[1]))
							{
								$page_slug = $myHelpers->global_lib->get_page_slug_by_id($hm_ids[1]);
								$type_url = $myHelpers->menu_lib->get_url('page='.$page_slug); 
							}
							else	continue;	
						}else if(isset($hm_ids[0]) && $hm_ids[0] == 'property_type')
						{
							
							if(isset($hm_ids[1]) && !empty($hm_ids[1]))
							{
								/*$type_url = $myHelpers->menu_lib->get_url('type='.strtolower($hmvcv['name']));*/
								
								$type_field = get_property_type_field($hm_ids[1]  , 'slug');
								$type_url = $myHelpers->menu_lib->get_url('type='.strtolower($type_field));
							}
							else	continue;	
						}
						else continue;
							
						?>
							<li ><a href="<?php echo $type_url; ?>"><?php echo mlx_get_lang($hmvcv['name']); ?></a></li>
						<?php
					}
					echo '</ul>';
				}
				?>
			</li>
	  <?php
	  }
	  
	  }else{
	  ?>
	  <!--	base_url('property');-->
	  <li <?php if($class == 'home' && $func == 'home') echo 'class="active"'; ?>>
		<a href="<?php echo $myHelpers->menu_lib->get_url('home'); ?>"><?php echo mlx_get_lang('Home'); ?></a>
	  </li>
	  <li <?php if($class == 'main' && $func == 'property-for-sale') echo 'class="active"'; ?>>
		<a href="<?php echo $myHelpers->menu_lib->get_url('property_for_sale');  ?>"><?php echo mlx_get_lang('Sale'); ?></a>
	  </li>
	  <li <?php if($class == 'main' && $func == 'property-for-rent') echo 'class="active"'; ?>>
		<a href="<?php echo $myHelpers->menu_lib->get_url('property_for_rent');  ?>"><?php echo mlx_get_lang('Rent'); ?></a>
	  </li>
	  <li class="has-children <?php if($class == 'main' && $func == 'property') echo 'active'; ?>">
		<a href="<?php echo $myHelpers->menu_lib->get_url('property'); ?>"><?php echo mlx_get_lang('Properties'); ?></a>
		<ul class="dropdown arrow-top">
		  <?php 
			if(isset($property_type_list) && $property_type_list->num_rows() > 0){ 
				foreach($property_type_list->result() as $prop_row){
					$type = $myHelpers->menu_lib->get_url('type='.strtolower($prop_row->title));
				 ?>
					<li><a href="<?php echo $type; ?>"><?php echo mlx_get_lang(ucfirst($prop_row->title)); ?></a></li>
			<?php } } ?>
		</ul>
	  </li>
	  <?php 
	  $enbale_our_agents = get_option('enbale_our_agents');
	  if(isset($enbale_our_agents) && $enbale_our_agents == 'Y'){ ?>
		  <li <?php if($class == 'main' && $func == 'agents') echo 'class="active"'; ?>>
			<a href="<?php echo $myHelpers->menu_lib->get_url('agents');  ?>"><?php echo mlx_get_lang('Our Agents'); ?></a>
		  </li>
	  <?php } ?>
	  <li <?php if($class == 'main' && $func == 'about-us') echo 'class="active"'; ?>>
		<a href="<?php echo $myHelpers->menu_lib->get_url('page=about-us'); ?>"><?php echo mlx_get_lang('About Us'); ?></a>
	  </li>
	  <li <?php if($class == 'main' && $func == 'contact') echo 'class="active"'; ?>>
		<a href="<?php echo $myHelpers->menu_lib->get_url('contact');  ?>"><?php echo mlx_get_lang('Contact Us'); ?></a>
	  </li>
	  <?php } ?>
	</ul>
  </nav>
  
</div>
	