<?php /*?><?php if( have_rows('grid_repeater') ): ?>
	<?php while ( have_rows('grid_repeater') ) : the_row(); ?>
		<?php the_sub_field('grid_image'); ?>
		<?php the_sub_field('grid_link'); ?>
		<?php the_sub_field('grid_url'); ?>
		<?php the_sub_field('grid_page'); ?>
		<?php the_sub_field('grid_anchor'); ?>
		<?php the_sub_field('grid_pdf'); ?>
		<?php the_sub_field('grid_text'); ?>
    <?php endwhile; ?>
<?php endif; ?>
<?php */?>

<?php if( have_rows('grid_repeater') ): ?>
<div class="pics">
	<?php while ( have_rows('grid_repeater') ) : the_row(); ?>
	<div class="col-md-3">

		<?php $image = get_sub_field('grid_image'); ?>
		<?php $link = get_sub_field('grid_link'); ?>
		<?php $url = get_sub_field('grid_url'); ?>

		
		<?php $anchor = get_sub_field('grid_anchor'); ?>
		<?php $pdf = get_sub_field('grid_pdf'); ?>
		<?php $text = get_sub_field('grid_text'); ?>
		
		
		<?php if($link == "none") { ?> 
			<div class="thumbnail">
				<img src="<?php echo $image['url']; ?>" alt="<?php $image['alt']; ?>" class="img-responsive" />
				<?php if($text != "") { ?><div><?php the_sub_field('grid_text'); ?></div><?php } else { echo $url;} ?>
			</div>		
		
		<?php } elseif($link == "anchor") { ?> 
				<?php 
					$post_object = get_sub_field('grid_page'); 
					$post = $post_object;
					setup_postdata( $post );
					$perma = get_permalink();
					wp_reset_postdata();
				?>
				<?php 
					$post_object = get_sub_field('grid_anchor'); 
					$post = $post_object;
					setup_postdata( $post );
					$slug = $post->post_name;
					$anchor_title = get_the_title();
					wp_reset_postdata();
				?>				
			<a href="<?php echo $perma; ?>#<?php echo $slug; ?>" class="thumbnail">
				<img src="<?php echo $image['url']; ?>" alt="<?php $image['alt']; ?>" class="img-responsive" />
				<?php if($text != "") { ?><div><?php the_sub_field('grid_text'); ?></div><?php } else {echo $anchor_title; } ?>
			</a>		
		
		<?php } elseif($link == "URL") { ?> 
			<a href="<?php echo $url; ?>" class="thumbnail">
				<img src="<?php echo $image['url']; ?>" alt="<?php $image['alt']; ?>" class="img-responsive" />
				<?php if($text != "") { ?><div><?php the_sub_field('grid_text'); ?></div><?php } else {echo $url;} ?>
			</a>		
		
		<?php } elseif($link == "pdf") { ?> 
			<a href="<?php echo $pdf; ?>" class="thumbnail">
				<img src="<?php echo $image['url']; ?>" alt="<?php $image['alt']; ?>" class="img-responsive" />
				<?php if($text != "") { ?><div><?php the_sub_field('grid_text'); ?></div><?php } else { echo $pdf["title"];} ?>
			</a>		
		
		<?php } elseif($link == "page") { ?> 
				<?php 
					$post_object = get_sub_field('grid_page'); 
					$post = $post_object;
					setup_postdata( $post );
					$perma = get_permalink();
					$anchor_title = get_the_title();
					wp_reset_postdata();
				?>			
			<a href="<?php echo $perma; ?>" class="thumbnail">
				<img src="<?php echo $image['url']; ?>" alt="<?php $image['alt']; ?>" class="img-responsive" />
				<?php if($text != "") { ?><div><?php the_sub_field('grid_text'); ?></div><?php } else {echo $anchor_title; } ?>
			</a>				
		
		<?php } ?>						
		

	</div>
	<?php endwhile; ?>
</div>
<?php endif; ?>
<div class="clear"></div>