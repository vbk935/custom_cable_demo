<?php if( have_rows('linklist_repeater') ) : ?>
	<ul>
	<?php while ( have_rows('linklist_repeater') ) : the_row(); ?>
		<?php $file = get_sub_field('linklist_file'); ?>
		<?php if( $file == 'File'){ ?>
			<li><a href="<?php echo $file['url']; ?>"><?php the_sub_field('linklist_title'); ?></a></li>
		<?php } else { ?>
			<li><a href="<?php the_sub_field('linklist_page'); ?>"><?php the_sub_field('linklist_title'); ?></a></li>
		<?php }?>
	<?php endwhile; ?>
</ul>
<?php endif; ?>