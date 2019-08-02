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



		<?php $anchor = get_sub_field('section_title'); ?>
		<?php $anchor = preg_replace('/[^A-Za-z0-9]/', "_", $anchor); ?>
		<?php $anchor = str_replace("-", "_", $anchor); ?>
		<?php $anchor = str_replace("___", "_", $anchor); ?>
		<?php $anchor = str_replace("__", "_", $anchor); ?>
		<?php $anchor = str_replace("_", "-", $anchor); ?>
		<?php $anchor = str_replace("---", "-", $anchor); ?>
		<?php $anchor = str_replace("--", "-", $anchor); ?>
		<?php $anchor = strtolower($anchor); ?>
	<a name="<? echo $anchor; ?>"></a>
	<?php if( get_sub_field('section_width') ) { 
		$columns = get_sub_field('section_width');
		switch ($columns) {
			//twenty : 20/80
			//fifty : 50/50
			//full : 100
			case "five":
			   $col1 = '<div class="col-md-1">';
			   $col1end = '</div>';
			   $col2 = '<div class="col-md-11">';
			   $col2end = '</div>';
			break;
			case "ten":
			   $col1 = '<div class="col-md-2">';
			   $col1end = '</div>';
			   $col2 = '<div class="col-md-10">';
			   $col2end = '</div>';
			break;
			case "fifteen":
			   $col1 = '<div class="col-md-3">';
			   $col1end = '</div>';
			   $col2 = '<div class="col-md-9">';
			   $col2end = '</div>';
			break;							
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
	<? if( get_sub_field('text_color') ) { $color = ' style="color:'. get_sub_field('text_color').'"' ;} ?>
	<? if( get_sub_field('header_color') ) { $back = ' style="background:'. get_sub_field('header_color').'"' ;} ?>
	<div class="panel-heading"<? echo $back;?>>
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
	<?php $linkloc = get_sub_field('section_link_choice'); ?>

	<?php if($linkloc == '' or $linkloc == 'None') { ?>	
		<img src="<?php echo $thumb; ?>" alt="<?php echo $alt; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>"  class="img-responsive"/>
	<?php  } else {  ?>			
			<?php
			$linkloc = get_sub_field('section_link_choice');
			if( $linkloc == 'Internal'){ ?>
				<a href="<?php echo get_sub_field('section_internal'); ?>" title="<?php echo $title; ?>" target="_blank" > <img src="<?php echo $thumb; ?>" alt="<?php echo $alt; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>"  class="img-responsive"/> </a>
			<? 
			}	
			if( $linkloc == 'External'){ ?>
				<a href="<?php echo get_sub_field('section_link'); ?>" title="<?php echo $title; ?>" target="_blank" > <img src="<?php echo $thumb; ?>" alt="<?php echo $alt; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>"  class="img-responsive"/> </a>
			<?php } ?>		
	
	<?php } ?>
	
	
	
	
		
		<?	
			echo $col1end; 
			echo $col2; 
		?>
		<?php if( get_sub_field('section_subhead') ) { ?>
			<h3><? the_sub_field('section_subhead'); ?></h3>
		<? } 
		if( get_sub_field('section_text') ) { 
			the_sub_field('section_text'); 
		} ?>
		
		
		

		
				
		
		<div class="row" style="margin-top:20px;">

			<? if( get_sub_field('add_request_a_quote_button') ) { ?>
			<div class="col-md-4">
				<a href="<?php echo get_permalink( 369 ); ?>" class="btn read-more">Request Quote <i class="fa fa-play"></i></a>
			</div>
			<?php } ?>


			<?php
			if( $linkloc == 'Internal'){ ?>
				<div class="col-md-4">
					<a href="<?php echo get_sub_field('section_internal'); ?>" class="btn read-more"><?php echo  get_sub_field('section_internal_link'); ?> <i class="fa fa-play"></i></a>
				</div>
			<? 
			}	
			if( $linkloc == 'External'){ ?>
				<div class="col-md-4">
					<a href="<?php echo get_sub_field('section_link'); ?>" target="_blank" class="btn read-more">Learn More <i class="fa fa-play"></i></a> 
				</div>
			<?php } ?>
			
			

			
			
			<?php if (is_page(379)){ ?>

					<? if( get_sub_field('section_downloads') ) { 
						$posts = get_sub_field('section_downloads');
						
						if( $posts ): ?>
						    <ul>
						    <?php foreach( $posts as $post): // variable must be called $post (IMPORTANT) ?>
							   <?php setup_postdata($post); ?>
							   <li>
								  <a href="<?php echo wp_get_attachment_url( $p->ID ); ?>"><?php the_title(); ?></a>
							   </li>
						    <?php endforeach; ?>
						    </ul>
						    <?php wp_reset_postdata(); // IMPORTANT - reset the $post object so the rest of the page works correctly ?>
						<?php 
							endif;
							}
						?>	



			<?php } else { ?>


				<? if( get_sub_field('section_downloads') ) { 
					$posts = get_sub_field('section_downloads');
					
					if( $posts ): ?>
					    <?php foreach( $posts as $post): // variable must be called $post (IMPORTANT) ?>
						   <?php setup_postdata($post); ?>
						   <div class="col-md-4">
							  <a href="<?php echo wp_get_attachment_url( $p->ID ); ?>" class="btn read-more">View Spec Sheet <i class="fa fa-play"></i></a>
						   </div>
					    <?php endforeach; ?>
					    <?php wp_reset_postdata(); // IMPORTANT - reset the $post object so the rest of the page works correctly ?>
					<?php 
						endif;
						}
				?>	

			
			<?php } ?>
			
			
			
<? if( get_sub_field('case_study') ) { 
	// CASE STUDIES
	if(is_page(360)) {
		$field = get_sub_field_object('case_study');
		$value = get_sub_field('case_study');
		$label = $field['choices'][ $value ]; ?>
		<?php $label = preg_replace('/[^A-Za-z0-9]/', "_", $label); ?>
		<?php $label = str_replace("-", "_", $label); ?>
		<?php $label = str_replace("___", "_", $label); ?>
		<?php $label = str_replace("__", "_", $label); ?>
		<?php $label = str_replace("_", "-", $label); ?>
		<?php $label = str_replace("---", "-", $label); ?>
		<?php $label = str_replace("--", "-", $label); ?>
		<?php $label = strtolower($label); ?>

		<div class="col-md-4">
			<a href="<?php echo get_permalink( 380 ); ?>#<?php echo $label; ?>" class="btn read-more">View Case Study <i class="fa fa-play"></i></a>
		</div>
<?php 
	}
} 
?>			
			
			

			
			
			
			
			
			
			
			
		</div> <?php // END OF ROW; ?>
		
		
		
		
			
        <?php echo $col1end; ?>
	</div>
</div>
<? endwhile; ?>
<? endif; ?>
