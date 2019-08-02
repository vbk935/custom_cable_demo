<?php
if(is_page(22)) {
// WP_Query arguments
$args = array (
	'post_type'              => 'company',
	'order'                  => 'ASC',
	'orderby'                => 'title',
	'posts_per_page'		=> 6
);

// The Query
$query = new WP_Query( $args );

// The Loop
if ( $query->have_posts() ) {
	while ( $query->have_posts() ) {
		$query->the_post(); ?>
		
		<?php $anchor = get_the_title(); ?>
		<? $anchor = preg_replace('/[^A-Za-z0-9]/', "_", $anchor); ?>
		<? $anchor = strtolower($anchor); ?>
		<a name="<? echo $anchor; ?>"></a>
		
		
		
		<div class="panel panel-default <?php the_field('company_code') ?>">
			<div class="panel-heading">
				<h2>
					<?php the_title(); ?>
				</h2>
			</div>
			<div class="panel-body">
				<div class="col-md-4">
					<?php if ( has_post_thumbnail() ) { ?> 
						<a href="<?php the_field('company_url'); ?>" title="<?php the_title(); ?>" target="_blank" ><?php the_post_thumbnail(); ?> </a>
					<?php } ?>
				</div>
				<div class="col-md-8">
						<h3><?php the_field('company_slogan'); ?></h3>
						<?php the_content(); ?>
						<a href="<?php the_field('company_url'); ?> target="_blank" " class="btn read-more" style="background: <?php $color ?>">Learn More <i class="fa fa-play"></i></a>
				</div>
			</div>
		</div>
<? 	}
} else {
	// no posts found
}

// Restore original Post Data
wp_reset_postdata();
}
?>