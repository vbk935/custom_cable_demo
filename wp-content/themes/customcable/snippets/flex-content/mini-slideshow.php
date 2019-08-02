<?php $i = 0 ?>
<?php $n = 0 ?>
<?php $images = get_sub_field('slideshow_group');
if( $images ): ?>
<?php $rand = rand(1,99); ?>
<div id="carousel-mini-<?php echo $rand; ?>" class="carousel slide" data-ride="carousel">
<?php /*?>	<ol class="carousel-indicators">
		<?php while ( have_rows('slideshow_group') ) : the_row(); ?>
			<li data-target="#carousel-mini-<?php echo $rand; ?>" data-slide-to="<?php echo $i ?>" <?php if( $i == 0 ) { ?> class="active" <?php } ?>></li>
			<?php $i++ ;?>
		<?php endwhile; ?>
	</ol>
	<?php */?>
	<!-- Wrapper for slides -->
	<div class="carousel-inner" role="listbox">
		<?php while ( have_rows('slideshow_group') ) : the_row(); ?>
		<?php $image = get_sub_field('slideshow_image'); ?>
		<div class="item<?php if( $n == 0 ) { ?> active <?php } ?>"> <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" class="img-responsive">
			<div class="carousel-caption"><?php echo $image['caption']; ?></div>
		</div>
		<?php $n++ ;?>
		<?php endwhile; ?>
	</div>
	
	<!-- Controls --> 
	<a class="left carousel-control" href="#carousel-mini-<?php echo $rand; ?>" role="button" data-slide="prev"> <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> <span class="sr-only">Previous</span> </a> 
	<a class="right carousel-control" href="#carousel-mini-<?php echo $rand; ?>" role="button" data-slide="next"> <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span> <span class="sr-only">Next</span> </a> 
</div>
<?php endif; ?>
