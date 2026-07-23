<?php 
global $settings;



$def_lang_code = $this->default_language;
/*print_r($settings);*/

/*if(!isset($settings['video_lang']) || (isset($settings['video_lang']) && $settings['video_lang'] != $def_lang_code))
	return false;*/
	
	/*print_r($settings['video_url']);*/
	
if(isset($settings['video_url']) && !empty($settings['video_url']))
{
	$video_urls = $settings['video_url'];
	$video_urls = array_filter($video_urls);
	if(count(array_filter($video_urls)) == count($video_urls)) 
	{
		
		if(!function_exists('getYoutubeIdFromUrl'))
		{
			function getYoutubeIdFromUrl($url) {    
				$parts = parse_url($url);
				if(($parts["host"]=="m.youtube.com" || $parts["host"]=="youtube.com" || $parts["host"]=="www.youtube.com" || $parts["host"]=="youtu.be" || $parts["host"]=="www.youtu.be") && !strstr($url,"/c/") && !strstr($url,"/channel/") && !strstr($url,"/user/")){
				if(isset($parts['query'])){
					parse_str($parts['query'], $qs);
					if(isset($qs['v'])){
						return $qs['v'];
					}else if(isset($qs['vi'])){
						return $qs['vi'];
					}
				}
				if(($parts["host"]=="youtu.be" || $parts["host"]=="www.youtu.be") && isset($parts['path'])){
					$path = explode('/', trim($parts['path'], '/'));
					return $path[count($path)-1];
				}
				}
				if(strlen($url)==11 && (!strstr($url, "http://") && !strstr($url, "https://") && !strstr($url, "www.") && !strstr($url, "youtube") && !strstr($url, "www.") && !strstr($url, "youtu.be"))) return $url;
				return false;
			}
		}
		if(!function_exists('validateFbVideoUrl'))
		{
			function validateFbVideoUrl($url){
				$parts = parse_url($url);
				if(($parts["host"]=="facebook.com" || $parts["host"]=="www.facebook.com" || $parts["host"]=="fb.me" || $parts["host"]=="fb.com") && !strstr($url,"/pg/")){
					return $url;
				}
				return false;
			}
		}
		if(!function_exists('getVimeoId'))
		{
			function getVimeoId($url){
				$parts = parse_url($url);
				
				if($parts['host'] == 'www.vimeo.com' || $parts['host'] == 'vimeo.com' || $parts['host'] == 'player.vimeo.com'){
					$vidid=substr($parts['path'], 1);
					return $vidid;
				}
				return false;
			}
		}
		if(!function_exists('getvidinfo'))
		{
			function getvidinfo($url){
				$getYT=getYoutubeIdFromUrl($url);
				if($getYT){
					$result["type"]="yt";
					$result["string"]=$getYT;
					$result["img"]="https://img.youtube.com/vi/".$getYT."/mqdefault.jpg";
					return($result);
				}
				else{
					$getFB=validateFbVideoUrl($url);
					if($getFB){
						$result["type"]="fb";
						$result["string"]=$getFB;
						$result["img"]="example.com/your-image-here.jpg";// I DIDN'T FIND A WAY TO GET FB VIDEO THUMBNAIL
						return($result);
					}
					else{
						
						$vimeoid=getVimeoId($url);
						
						if($vimeoid){
							$result["type"]="vim";
							$result["string"]=$vimeoid;
							return($result);
						}
						
					}
				}
				return false;
			}
		}
		if(!function_exists('echovideo'))
		{
			function echovideo($url){
				if($url){
					
					$vidinfo=getvidinfo($url);
					
					if($vidinfo){
						if($vidinfo["type"]=="yt"){
							return '<iframe width="100%" height="100%" src="https://www.youtube-nocookie.com/embed/'.$vidinfo["string"].'" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
						}elseif($vidinfo["type"]=="fb"){
							return '<iframe src="https://www.facebook.com/plugins/video.php?href='.$vidinfo["string"].'" width="100%" height="100%" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true" allowFullScreen="true"></iframe>';
						}elseif($vidinfo["type"]=="vim"){
							return '<iframe src="https://player.vimeo.com/video/'.$vidinfo["string"].'" width="100%" height="100%" frameborder="0" allow="autoplay; fullscreen" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
						}
						else{
							$array = explode('.', $url);
							$extension = end($array);
							
							return '<video width="100%" height="100%" controls>
										<source src="'.$url.'" type="video/'.$extension.'">
									</video>';
						}
					}
					else
					{ 
						$array = explode('.', $url);
						$extension = end($array);
						return '<video width="100%" height="100%" controls>
						<source src="'.$url.'" type="video/'.$extension.'">
						</video>';
					}
				}
			}	
		}
?>
<div class="site-section site-section-sm ">
<style>

.video-player-container {
    border: 1px solid #ddd;
    margin-bottom: 30px;
    background-color: #f4f4f4;
    padding: 10px 10px 10px 10px;
	-webkit-transition: .3s all ease-in-out;
    -o-transition: .3s all ease-in-out;
    transition: .3s all ease-in-out;
	
	height:550px;
}
.video-player-container:hover {
	background-color: #ddd;
}
.video-player-container iframe{
	display: block;
	height: 100% !important;        
    width: 100% !important;
}

@media only screen and (max-width:768px){
    
	.video-player-container {
		height:350px;
		padding:10px;
	}
	
}
@media only screen and (max-width:482px){
    
	.video-player-container {
		height:250px;
	}
	
}
</style>


  <div class="container">
	
	<div class="row justify-content-center mb-5">
	  <div class="col-md-10 text-center">
		<div class="site-section-title">
			<?php 
			if(isset($settings['heading']) && $settings['heading'] != ''){?>
			<h2> <?php echo mlx_get_lang($settings['heading']); ?></h2>
			<?php } ?>
			<?php if(isset($settings['sub_heading']) && $settings['sub_heading'] != ''){?>
			<p class="subheading"><?php echo mlx_get_lang($settings['sub_heading']); ?></p>
			<?php } ?>
		</div>
	  </div>
	</div>
	
	
	<div class="row justify-content-center mb-5">
		<?php foreach($video_urls as $video_url) { ?>
			<div class="col-md-12">
				<div class="video-player-container">
					<?php echo echovideo($video_url); ?>
				</div>
			</div>
		<?php } ?>
		<!--
		<iframe src="http://www.facebook.com/video/embed?video_id=10153231379946729" frameborder="0" width="100%" height="550">
		-->
	</div>
	
	</div>
</div>
<?php }} ?>