<?php 
$html ='<html>
<body>
    <div>Property Deaitls</div>
    <p>Property Image</p>
	<img src="'.base_url().'uploads/'.$result->property_images.'" />
	<br/>
	<h3>'.$result->title.'</h3>
</body>
</html>';
?>