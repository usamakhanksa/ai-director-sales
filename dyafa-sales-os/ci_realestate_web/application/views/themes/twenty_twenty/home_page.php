<?php

$enable_homepage_section = get_option('enable_homepage_section');
if ($enable_homepage_section == 'Y' && isset($homepage_section) && count($homepage_section) > 0) {
	$sections = $homepage_section;

	echo '<div class="homepage-section-block">';
	global $settings;
	
	/*echo "<pre>"; print_r($homepage_section); echo "</pre>";*/
	
	
	
	
	foreach ($sections as $section_id => $section_settings) {

		if ($section_settings['is_enable'] == 'Y') {
			$section_settings['themes'] = $theme;
			$section_settings['section_id'] = $section_id;
			$settings = $section_settings;
			$section_key = $section_settings['section_key'];
			
			if(isset($homepage_contents[$section_key]['section_path'])){
				
				$this->load->view($homepage_contents[$section_key]['section_path']);	
				
			}else{
				
				if (isset($section_settings['section_type']) && $section_settings['section_type'] == 'dynamic') {
					
					if(cms_file_exists("$theme/block/".$section_settings['section_type']."_".$section_settings['section_key']))
						$this->load->view("$theme/block/".$section_settings['section_type']."_".$section_settings['section_key']);
				}	
				else {
					if(cms_file_exists("$theme/block/$section_key"))
						$this->load->view("$theme/block/$section_key");	
				}	
			}
				

		}
	}
	echo '</div>';
} else {
	$this->load->view("$theme/home_page_default");
}
