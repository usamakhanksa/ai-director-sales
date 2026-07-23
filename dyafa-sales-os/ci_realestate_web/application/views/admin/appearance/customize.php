<?php $this->load->view("default/header-top");?>
<?php $this->load->view("default/sidebar-left");?>


<style type="text/css">

.sidebars .widget {
    border: solid 1px #d2d6de;
}

.widget-collections .widget .box-header .box-tools { display:none; }
.widget-collections .widget .box-footer{ display:block!important;}

</style>
<?php 
echo script_tag("themes/$theme/plugins/jscolor/jscolor.js");
?>
<?php





	$sidebars = array();
	$sidebars [] = array("title" => "Property Sidebar" , "class" => "sidebar-property-details");
	$sidebars [] = array("title" => "Page Sidebar" , "class" => "sidebar-page-details");
	$sidebars [] = array("title" => "Footer Sidebar" , "class" => "sidebar-footer");
?>

<div class="content-wrapper">
	<section class="content-header">
	  <h1><?php echo mlx_get_lang('Widgets'); ?></h1>
	  <?php if(isset($_SESSION['msg']) && !empty($_SESSION['msg']))
			{
				echo $_SESSION['msg'];
				unset($_SESSION['msg']);
			}
	?> 
	</section>

	

	
	<section class="content">  
		<?php 
		$attributes = array('name' => 'add_form_post','class' => 'homepage_section_form');		 			
		echo form_open_multipart('appearance/customize',$attributes); ?>
		<div class="row ">
			<section class="connectedSortable">
			
			
			<div class="col-md-6  widget-collections">
								
				
			<?php 
			
			//$this->load->view("default/appearance/mortgage-calculator-widget");
			//$this->load->view("default/appearance/related-properties-widget");
			//$this->load->view("default/appearance/recent-properties-widget");
			
			/*echo "<pre>";
			print_r($customization);
			echo "</pre>";*/
			echo "<pre>";
			print_r($custom_styles);
			echo "</pre>";
			
			$custom_css = "";
			if(is_array($custom_styles))
			{
				$css_start = "{";
				$css_end = "}";	
				$css_selector = "";
				$css = "";
				foreach($custom_styles as  $custom_style){
					foreach($custom_style as  $key => $style){	
						/*print_r( $key);
						print_r( $style); continue;*/
						if($key == 'selector')
							$css_selector = $style;	
						else
						{	$value = $style;
							if(preg_match("/color/", $key))
							{/**	its a color box **/
								$value = "#". str_replace("#","",$value);
								
							}	
							$css .= $key . ":".$value . ";\n".PHP_EOL;
						}	
						
					}
				
				$custom_css .=  $css_selector .  $css_start .$css .  $css_end .PHP_EOL;
				}
			}
			
			echo $custom_css;
			
			$customization  = $customization['twenty20'];
			
			foreach($customization as $element){
				
				
				
			?>
			<div>
				<h4><?php echo $element['element_title'];?></h4>
				<?php
				$styles = $element['element_styles'];
				/*echo "<pre> ";
				print_r($styles);
				echo "</pre>";*/
				?>
				<input type="hidden" name="styles[<?php echo $element['element_name'];?>][selector]" 
						value="<?php echo $element['element_selector'];?>" 	>		
				<?php
				foreach($styles as $style){
					
					$value = $style['default'];
					if(isset($custom_styles[$element['element_name']][$style['style']]))
						$value = $custom_styles[$element['element_name']][$style['style']];
				?>	
				<div class="form-group">
				<label for="<?php echo $style['name'];?>"><?php echo $style['text'];?> <span class="required">*</span></label>	
				<input type="text" 
						name="styles[<?php echo $element['element_name'];?>][<?php echo $style['style'];?>]" 
						id="<?php echo $style['name'];?>" 
						value="#<?php echo $value;?>" 
						style="width:100%;" 
						class="jscolor" required="required">		
				</div>		
				<?php	
				}
				?>
				
			</div>
			<?php		
				
			}
			
			?>
					
			</div>
			
			
			
			<div class="col-md-6 sidebars">
					
					
			</div><!-- sidebars -->
<style type="text/css">
iframe .form-search { 
	background: #564789;
}
</style>
		
			<div class="col-md-12 ">
				<!--
				<iframe name="iframe1" id="iframe1" src="http://192.168.2.80/main_demo/ci_realestate/search/en/property-for-sale" 
				width="1360px" height="1000px;"></iframe>	 -->
				<?php
				$content = file_get_contents('http://192.168.2.80/main_demo/ci_realestate/search/en/property-for-sale');
				$content = str_replace('</title>','</title><base href="https://www.google.com/calendar/" />', $content);
				$content = str_replace('</head>','<link rel="stylesheet" href="http://192.168.2.80/main_demo/ci_realestate/themes/default/css/test.css" /></head>', $content);
				//echo $content;
				?>
					
			</div>
			</section>
			
<script>
$(function () {

		
	$("#iframe1").contents().find("form").attr("style","width:100%;height:100%");
	//console.log($("#preview").contents().html());
		

	var cssLink = document.createElement("link");
cssLink.href = "http://192.168.2.80/main_demo/ci_realestate/themes/default/css/test.css";//"style.css"; 
cssLink.rel = "stylesheet"; 
cssLink.type = "text/css"; 
frames['iframe1'].document.head.appendChild(cssLink);

	
	});


</script>			


		
		</div>
		
		<input type="submit" name="submit" class="btn btn-primary" value="Save Styles" >
		
		</form>
	</section>
</div>




