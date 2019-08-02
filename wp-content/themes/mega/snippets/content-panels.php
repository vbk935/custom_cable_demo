<?php if (is_page(17)) { ?>
<?php if( have_rows('sections_repeater') ): ?>
<? while ( have_rows('sections_repeater') ) : the_row(); ?>

	<?php if( get_sub_field('section_type') ) { 
		$company = get_sub_field('section_type');
		switch ($company) {
			case "arl":
			   $color = '<div class="col-md-4">';
			   $logo = '</div>';
			   $subhead = '<div class="col-md-8">';
			   $url = '</div>';
			break;
			case "fifty":
			   $col1 = '<div class="col-md-6">';
			   $col1end = '</div>';
			   $col2 = '<div class="col-md-6">';
			   $col2end = '</div>';
			break;			
			default:
			   $col1 = '<div class="col-xs-12">';
			   $col1end = '</div>';
			   $col2 = '<div class="col-md-12">';
			   $col2end = '</div>';
			}
		}
 	?>



		<? $anchor = get_sub_field('section_title'); ?>
		<? $anchor = preg_replace('/[^A-Za-z0-9]/', "_", $anchor); ?>
		<? $anchor = strtolower($anchor); ?>
	<a name="<? echo $anchor; ?>"></a>
	<?php if( get_sub_field('section_width') ) { 
		$columns = get_sub_field('section_width');
		switch ($columns) {
			//twenty : 20/80
			//fifty : 50/50
			//full : 100
			case "twenty":
			   $col1 = '<div class="col-md-4">';
			   $col1end = '</div>';
			   $col2 = '<div class="col-md-8">';
			   $col2end = '</div>';
			break;
			case "fifty":
			   $col1 = '<div class="col-md-6">';
			   $col1end = '</div>';
			   $col2 = '<div class="col-md-6">';
			   $col2end = '</div>';
			break;			
			default:
			   $col1 = '<div class="col-xs-12">';
			   $col1end = '</div>';
			   $col2 = '<div class="col-md-12">';
			   $col2end = '</div>';
			}
		}
 	?>

<div class="panel panel-default">
	<? if( get_sub_field('section_title') ) { ?>
	<? if( get_sub_field('section_color') ) { $color = ' style="color:'. get_sub_field('section_color').'"' ;} ?>
	<div class="panel-heading">
		<h2<? echo $color;?>>
			<? the_sub_field('section_title'); ?>
		</h2>
	</div>
	<? } ?>
	<div class="panel-body">
		<?php 
                        echo $col1; 
                        
                        $image = get_sub_field('section_art'); 
                        $url = $image['url'];
                        $title = $image['title'];
                        $alt = $image['alt'];
                
                        // thumbnail
                        $size = 'medium';
                        $thumb = $image['sizes'][ $size ];
                        $width = $image['sizes'][ $size . '-width' ];
                        $height = $image['sizes'][ $size . '-height' ];
                        ?>
		<a href="<?php echo $url; ?>" title="<?php echo $title; ?>" target="_blank" > <img src="<?php echo $thumb; ?>" alt="<?php echo $alt; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>"  class="img-responsive"/> </a>
		<?	
                        echo $col1end; 
                        echo $col2; 
                        ?>
		<?php if( get_sub_field('section_subhead') ) { ?>
		<h3>
			<? the_sub_field('section_subhead'); ?>
		</h3>
		<? } 
		if( get_sub_field('section_text') ) { 
			the_sub_field('section_text'); 
		}
		if( get_sub_field('section_link') ) { ?>
			<a href="<?php if( get_sub_field('section_link') ){ ?><? } ?>" target="_blank" class="btn read-more" style="background: $color">Learn More <i class="fa fa-play"></i></a> 
		<? }		
         echo $col1end; ?>
	</div>
</div>
<? endwhile; ?>
<? endif; ?>
<? } ?>			 