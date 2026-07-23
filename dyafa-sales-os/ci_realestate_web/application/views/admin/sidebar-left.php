<?php 
	$user_type = $this->session->userdata('user_type');
	$enable_homepage_section = get_option('enable_homepage_section');
?>
<aside class="main-sidebar">
	<section class="sidebar">
		<ul class="sidebar-menu"> 
		<?php
		$CI = &get_instance();
		$menu_items =  $CI->config->item('sidebar_left');  
		
		ksort($menu_items);
		
		/*if(isset($_GET['az'])){
				echo "<pre>";print_r($menu_items); echo "</pre>"; exit;
			}*/
		//echo "<pre>";print_r($menu_items);echo "</pre>";
		foreach($menu_items as $k => $menu_item)
		{	
			
			
			/*echo $k;
			echo "<pre>";print_r($menu_item); echo "</pre>";*/
			
			if($menu_item['method'] == 'home_page' && $enable_homepage_section != 'Y')
			{
				continue;
			}
			/*echo $menu_item['class'];*/
			if(!$myHelpers->has_menu_access($menu_item['class'] , $user_type))
			{
				continue;
			}
			
			
			
			
			if(isset($menu_item['item'])) 
			{	$nav_items = $menu_item['item'];	
				$mi_class = "treeview ";
			}else {
				$mi_class = "";
			}
			
			/*echo $menu_item['method'] . " - " . $menu_item['class'];*/
			if($menu_item['method'] != '' && $func == $menu_item['method'] && $class == $menu_item['class'])
				$mi_class = " class='$mi_class active' ";
			else if( $menu_item['method'] == '' && $class == $menu_item['class'] ) 	
				$mi_class = " class='$mi_class active' ";
			else 			
				$mi_class = " class='$mi_class ' ";
			
			if($menu_item['link'] != '#') 			$link = base_url(explode("/","admin/".$menu_item['link']));
			else			$link = $menu_item['link'];
			
			if(!empty($menu_item['icon_class']))	$icon_text = "<i class='".$menu_item['icon_class']."'></i> ";
			else			$icon_text = "";
				
			if(!empty($menu_item['collapse_class']))	$collapse_text = "<i class='".$menu_item['collapse_class']."'></i> ";
			else			$collapse_text = "";
					
			?>
			<li <?php  echo $mi_class; ?> data-menu_id="<?php echo $k; ?>">
			  <a href="<?php echo $link; ?>"> 	<?php echo $icon_text; ?>
				<span><?php echo mlx_get_lang($menu_item['text']); ?></span> 	<?php echo $collapse_text; ?>   </a>
			<?php	
			//print_r($menu_item);
			if(isset($menu_item['item'])) {
				$nav_items = $menu_item['item'];	
				
			?>
		  <ul class="treeview-menu">
		  <?php 	
			foreach($nav_items as  $k_inner => $item)
			{	
				$mit_class = ""; 
				/*print_r($item);
				echo $item['class']."||".$item['method'];*/
				if(!$myHelpers->has_menu_access($item['class']."||".$item['method'] , $user_type))
				{
					continue;
				}
				
				/*/echo $class . ' == '. $item['class'] .'&& '. $func .' == '. $item['method'];
				echo $sub_class . ' == '. $item['class'] .'&& '. $func .' == '. $item['method'];*/
				
				if( $class == $item['class'] && $func == $item['method'])	$mit_class = " class='active' ";
				else				$mit_class = " class='' ";
				
				if(isset($sub_class)  &&  $sub_class == $item['class'] && $func == $item['method'])		$mit_class = " class='active' ";
				
				if(isset($sub_class)  &&  $sub_class == $item['class'] && isset($parent_class)  &&  $parent_class == $item['method'])	
					$mit_class = " class='active' ";
				
				if($item['link'] != '#')				$link = base_url(explode("/","admin/".$item['link']));
				else				$link = $item['link'];
				
				if(!empty($item['icon_class']))			$icon_text = "<i class='".$item['icon_class']."'></i> ";
				else				$icon_text = "";
					
				if(!empty($item['collapse_class']))		$collapse_text = "<i class='".$item['collapse_class']."'></i> ";
				else				$collapse_text = "";
				
				?>
				<li <?php  echo $mit_class; ?>>
				<a href="<?php echo $link; ?>">
				<?php echo $icon_text; ?>
				<span><?php echo mlx_get_lang($item['text']); ?></span>
				<?php echo $collapse_text; ?>
				</a></li>
				<?php	}
					echo "</ul>";
				?>	</li>		<?php
			}	
		}
	?>	
  </ul>
</section>
</aside>