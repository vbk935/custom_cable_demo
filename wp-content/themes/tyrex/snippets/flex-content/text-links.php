<?php if( have_rows('text_link_group') ): ?>
	<ul>
	<?php while ( have_rows('text_link_group') ) : the_row(); ?>

       <?php $text = get_sub_field('text'); ?>
        <?php $link = get_sub_field('link'); ?>
	   <li><a href="<?php echo $link; ?>"><?php echo $text; ?></a></li>

    <?php endwhile; ?>
    </ul>
<?php endif; ?>