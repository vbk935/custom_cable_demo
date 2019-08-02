<?php $add_team = get_field('add_team', 'option'); ?>
<?php if($add_team == 1 ) { ?>
	<?php $show_team = get_field('show_team', 'option'); ?>
	<?php $page_to_add_team = get_field('page_to_add_team', 'option'); ?>
	<?php if($show_team == 1 ) { ?>
		<?php 
		   $args3= array(
			'post_type' =>  'team' ,
			'order' => 'ASC',
			'orderby' =>  'meta_value_num' ,
			'meta_key' =>  'order1' ,
		   );
		?>
		
		<div class="team-box clearfix">
			<h2>Our Team</h2>
			<?php the_field('team_intro') ?>
			<?php // Start of the Loop
			$the_query = new WP_Query( $args3);
			while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
			<div class="col-md-2">
				<?php //the_field('order1');?>
				<div class="img-rounded image">
					<?php
						$thumb_id = get_post_thumbnail_id(get_the_ID());
						$alt = get_post_meta($thumb_id, '_wp_attachment_image_alt', true);
						if ( has_post_thumbnail() ) { the_post_thumbnail( 'large', array( 'alt' => $alt, 'title' => get_the_title(), 'class' => "" ) ); } 
					?>
				</div>
				<div class="team-name"> <strong>
					<?php the_title(); ?>
					</strong> </div>
				<div class="team-title">
					<?php the_field('title');?>
				</div>
				<?php if( get_field('department') ): ?>
				<div class="team-dept">
					<?php the_field('department');?>
				</div>
				<?php endif; ?>
				<a class="team-email" href="mailto:<?php the_field('email');?>">E-mail</a> <br />
				<a class="team-phone" href="tel:<?php the_field('phone_number');?>">
				<?php the_field('phone_number');?>
				</a> 
			</div>
			<?php endwhile; // end of the loop. ?>
		</div>
	<?php  } ?>
<?php } ?>
<?php wp_reset_postdata(); ?>
