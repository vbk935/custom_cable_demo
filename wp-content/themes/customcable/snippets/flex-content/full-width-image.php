<?php 
$image = get_sub_field('full_image');
if( !empty($image) ): 
	$url = $image['url'];
	$title = $image['title'];
	$alt = $image['alt'];
	$width = $image[ 'width' ];
	$height = $image[ 'height' ];
?>
<img src="<?php echo $url; ?>" alt="<?php echo $title; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" class="img-responsive center-block full-width-image"/>
<?php endif; ?>