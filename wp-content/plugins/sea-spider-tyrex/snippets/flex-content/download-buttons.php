<?php if( have_rows('downloads_list') ): ?>
	<div class="row">
	<?php while ( have_rows('downloads_list') ) : the_row(); ?>

       <?php  $title = get_sub_field('text_replace'); ?>
        <?php $file = get_sub_field('download'); ?>
		  <?php //var_dump($file); ?>
	   <div class="col-md-2"><a href="<?php echo $file['url']; ?>" class="btn read-more"><?php echo $title; ?></a></div>

    <?php endwhile; ?>
    </div>
<?php endif; ?>