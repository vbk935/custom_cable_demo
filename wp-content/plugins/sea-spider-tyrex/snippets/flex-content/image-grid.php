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
<style>
.responsive-container {
    position: relative;
    width: 100%;
    border-radius:5px;
    border: solid 1px #eee;
    margin-bottom:10px;
}

.dummy {
    padding-top: 100%; /* forces 1:1 aspect ratio */
}

.img-container {
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
	  padding: 5px;
    text-align:center; /* Align center inline elements */
    font: 0/0 a;
}

.img-container:before {
    content: ' ';
    display: inline-block;
    vertical-align: middle;
    height: 100%;
}

.img-container img {
    vertical-align: middle;
	border-radius: 3px;
    display: inline-block;
}
.outside-text {margin-bottom: 30px;}
</style>
<div class="row">
	<?php while ( have_rows('grid_repeater') ) : the_row(); ?>
		<div class="col-md-3 ">
		<div class="responsive-container">
		<div class="dummy"></div>
		<div class="img-container">
			<?php $image = get_sub_field('grid_image'); ?>
			<?php $link = get_sub_field('grid_link'); ?>
			<?php $url = get_sub_field('grid_url'); ?>

		
			<?php $anchor = get_sub_field('grid_anchor'); ?>
			<?php $pdf = get_sub_field('grid_pdf'); ?>
			<?php $text = get_sub_field('grid_text'); ?>
			<?php $label = get_sub_field('label'); ?>
		
		
		<?php if($link == "none") { ?> 
			<a href="#" class="thumbz" style="cursor:default;">
				<img src="<?php echo $image['url']; ?>" alt="<?php $image['alt']; ?>" class="img-responsive" />
				<?php if($text != "") { ?><div><?php the_sub_field('grid_text'); ?></div><?php } else { echo $url;} ?>
			</a>		
		
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
			<a href="<?php echo $perma; ?>#<?php echo $slug; ?>" class="thumbz">
				<img src="<?php echo $image['url']; ?>" alt="<?php $image['alt']; ?>" class="img-responsive" />
				<?php /*?><?php if($text != "") { ?><div><?php the_sub_field('grid_text'); ?></div><?php } else {echo $anchor_title; } ?><?php */?>
                <?php if( get_sub_field('grid_text') ) { $text = get_sub_field('grid_text'); } else { $text = $anchor_title; } ?>
			</a>		
		
		<?php } elseif($link == "URL") { ?> 
			<a href="<?php echo $url; ?>" class="thumbz" target="_blank">
				<img src="<?php echo $image['url']; ?>" alt="<?php $image['alt']; ?>" class="img-responsive" />
				<?php /*?><?php if($text != "") { ?><div><?php the_sub_field('grid_text'); ?></div><?php } else {echo $url;} ?><?php */?>
	            <?php if( get_sub_field('grid_text') ) { $text = get_sub_field('grid_text'); } else { $text = $url; } ?>
		</a>		
		
		<?php } elseif($link == "pdf") { ?> 
			<a href="<?php echo $pdf['url']; ?>" class="thumbz">
				<img src="<?php echo $image['url']; ?>" alt="<?php $image['alt']; ?>" class="img-responsive" />
				<?php /*?><?php if($text != "") { ?><div><?php the_sub_field('grid_text'); ?></div><?php } else { echo $pdf["title"];} ?><?php */?>
	            <?php if( get_sub_field('grid_text') ) { $text = get_sub_field('grid_text'); } else { $text = $pdf["title"]; } ?>
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
			<a href="<?php echo $perma; ?>" class="thumbz">
				<img src="<?php echo $image['url']; ?>" alt="<?php $image['alt']; ?>" class="img-responsive" />
				<?php if($label != "") { ?>
					<?php if($text != "") { ?>
                        <div>
                            <?php the_sub_field('grid_text'); ?>
                        </div>
                    <?php } else { ?>
                        <div>
                            <?php echo $anchor_title; ?>
                        </div>
                    <?php } ?>
				<?php } ?>
		        <?php if( get_sub_field('grid_text') ) { $text = get_sub_field('grid_text'); } else { $text = $anchor_title; } ?>
		</a>				
		
		<?php } ?>						
		

		</div>
		</div>

        
        <?php if($label != "") { ?>
        	<div class="center-block text-center outside-text"><?php echo $text; ?></div>
        <?php } ?>

</div>
			<?php endwhile; ?>
</div>
<?php endif; ?>
<div class="clear"></div>