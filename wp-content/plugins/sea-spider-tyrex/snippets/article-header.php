<?php if( get_field('header_slideshow') ) { ?>
<?php $n = 0; ?>
<?php $n2 = 0; ?>
<div class="slideshow-featured">
	<div id="slideshow-01" class="carousel slide carousel-fade" data-ride="carousel"> 
		<ol class="carousel-indicators">
			<? while ( have_rows('header_slideshow') ) : the_row(); ?>
				<li data-target="#slideshow-01" data-slide-to="<? echo $n; ?>" <? if($n == 0) { ?>class="active"<? } ?>></li>
				<? $n++; ?>
			<?php endwhile; ?>
		</ol>
		<div class="carousel-inner">
			<? while ( have_rows('header_slideshow') ) : the_row(); ?>
			<div class="item <? if($n2 == 0) { ?> active<? } ?>">
				<?php $image = get_sub_field('slide_image');
                    if( !empty($image) ): ?>
				<img src="<?php echo $image['url']; ?>" width="<?php echo $image['width']; ?>" height="<?php echo $image['height']; ?>" alt="<?php echo $image['alt']; ?>" class="img-responsive"/>
				<?php endif; ?>
			</div>
			<? $n2++; ?>
			<?php endwhile; ?>
		</div>
	</div>
</div>
<?php } else { 
	if ( has_post_thumbnail() ) { the_post_thumbnail(); 
	}
} ?>

<?php if( get_field( 'secondary_menu' ) ) : ?>
	<div class="secondary-menu <?php  the_field( 'secondary_type' ); ?>">
		<?php the_field( 'secondary_menu' ); ?>
	</div>
<?php endif; ?>