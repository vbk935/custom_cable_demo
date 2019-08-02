<?php if( have_rows('downloads_list') ): ?>
	<ul>
	<?php while ( have_rows('downloads_list') ) : the_row(); ?>

       <?php $title = get_sub_field('text_replace'); ?>
        <?php $file = get_sub_field('download'); ?>
	   <li><a href="<?php echo $file['url']; ?>"><?php echo $title; ?></a></li>

    <?php endwhile; ?>
    </ul>
<?php endif; ?>