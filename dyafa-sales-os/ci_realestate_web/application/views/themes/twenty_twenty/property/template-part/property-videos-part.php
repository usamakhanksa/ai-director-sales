<?php 




if (isset($single_property->video_urls) && !empty($single_property->video_urls)) {
						$video_url_array = explode(',', $single_property->video_urls);
?>


<?php
function getYoutubeIdFromUrl($url)
{
	$parts = parse_url($url);
	if (($parts["host"] == "m.youtube.com" || $parts["host"] == "youtube.com" || $parts["host"] == "www.youtube.com" || $parts["host"] == "youtu.be" || $parts["host"] == "www.youtu.be") && !strstr($url, "/c/") && !strstr($url, "/channel/") && !strstr($url, "/user/")) {
		if (isset($parts['query'])) {
			parse_str($parts['query'], $qs);
			if (isset($qs['v'])) {
				return $qs['v'];
			} else if (isset($qs['vi'])) {
				return $qs['vi'];
			}
		}
		if (($parts["host"] == "youtu.be" || $parts["host"] == "www.youtu.be") && isset($parts['path'])) {
			$path = explode('/', trim($parts['path'], '/'));
			return $path[count($path) - 1];
		}
	}
	if (strlen($url) == 11 && (!strstr($url, "http://") && !strstr($url, "https://") && !strstr($url, "www.") && !strstr($url, "youtube") && !strstr($url, "www.") && !strstr($url, "youtu.be"))) return $url;
	return false;
}

function validateFbVideoUrl($url)
{
	$parts = parse_url($url);
	if (($parts["host"] == "facebook.com" || $parts["host"] == "www.facebook.com" || $parts["host"] == "fb.me" || $parts["host"] == "fb.com") && !strstr($url, "/pg/")) {
		return $url;
	}
	return false;
}

function getVimeoId($url)
{
	$parts = parse_url($url);

	if ($parts['host'] == 'www.vimeo.com' || $parts['host'] == 'vimeo.com' || $parts['host'] == 'player.vimeo.com') {
		$vidid = substr($parts['path'], 1);
		return $vidid;
	}
	return false;
}

function getvidinfo($url)
{
	$getYT = getYoutubeIdFromUrl($url);
	if ($getYT) {
		$result["type"] = "yt";
		$result["string"] = $getYT;
		$result["img"] = "https://img.youtube.com/vi/" . $getYT . "/mqdefault.jpg";
		return ($result);
	} else {
		$getFB = validateFbVideoUrl($url);
		if ($getFB) {
			$result["type"] = "fb";
			$result["string"] = $getFB;
			$result["img"] = "example.com/your-image-here.jpg"; // I DIDN'T FIND A WAY TO GET FB VIDEO THUMBNAIL
			return ($result);
		} else {

			$vimeoid = getVimeoId($url);

			if ($vimeoid) {
				$result["type"] = "vim";
				$result["string"] = $vimeoid;
				return ($result);
			}
		}
	}
	return false;
}
function echovideo($url)
{
	if ($url) {

		$vidinfo = getvidinfo($url);

		if ($vidinfo) {
			if ($vidinfo["type"] == "yt") {
				
				/*
				youtube-nocookie.com/embed/  not working solution
				youtube.com/watch?v=   working solutions
				<iframe width="420" height="345" src="https://www.youtube.com/embed/tgbNymZ7vqY">
</iframe>
				allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
				*/
?><div>
			


			<iframe width="100%" height="350" src="https://www.youtube-nocookie.com/embed/<?php echo $vidinfo["string"]; ?>?rel=0&controls=1&modestbranding=1&showinfo=0" frameborder="0" 
					 allowfullscreen allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"></iframe>
				</div>
			<?php
			} elseif ($vidinfo["type"] == "fb") {
			?>
				<div>
					<iframe src="https://www.facebook.com/plugins/video.php?href=<?php echo $vidinfo["string"]; ?>&show_text=0&width=100%&height=350" width="100%" height="350" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true" allowFullScreen="true"></iframe>
				</div>
			<?php
			} elseif ($vidinfo["type"] == "vim") {
			?><div><iframe src="https://player.vimeo.com/<?php echo $vidinfo["string"]; ?>" width="100%" height="350" frameborder="0" allow="autoplay; fullscreen" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div>
			<?php
			} else {
				$array = explode('.', $url);
				$extension = end($array);
			?>
				<video width="100%" height="350" controls>
					<source src="<?php echo $url; ?>" type="video/<?php echo $extension; ?>">
				</video>
			<?php
			}
		} else {
			$array = explode('.', $url);
			$extension = end($array);
			?>
			<video width="100%" height="350" controls>
				<source src="<?php echo $url; ?>" type="video/<?php echo $extension; ?>">
			</video>
<?php
		}
	}
}

?>
<h2 class="h4 text-black no-gutters mt-5  text-left d-print-none"><?php echo mlx_get_lang('Videos'); ?></h2>
<div class="row d-print-none">
	<div class="col-md-12">
		<div class="video_url_container owl-carousel">
			<?php if (!empty($video_url_array)) {
				foreach ($video_url_array as $k => $v) {
			?>
					<div class="single_video_url">
						<?php echo echovideo($v); ?>
					</div>
			<?php }
			} ?>
		</div>
	</div>
</div>

<script>

</script>
<?php } ?>