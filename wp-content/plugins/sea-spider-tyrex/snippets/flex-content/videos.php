<?php $cols = get_sub_field('video_columns'); ?>
<?php if( have_rows('video_group') ): ?>
<div class="row">
	<div class="video-gallery">
	<?php while ( have_rows('video_group') ) : the_row(); ?>
		<?php if($cols == "one") { ?><div class="col-md-6 col-md-offset-3"><?php } ?>
		<?php if($cols == "two") { ?><div class="col-md-6"><?php } ?>
		<?php if($cols == "three") { ?><div class="col-md-4"><?php } ?>
			<div class="embed-container">
				<?php the_sub_field('video'); ?>
			</div>
		</div>
	<?php endwhile; ?>
	</div>
</div>	
<?php endif; ?>