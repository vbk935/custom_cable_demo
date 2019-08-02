<?php if( get_field('about', 'option') ){ ?>
<footer class="entry-footer">
	<div class="row">
		<div class="col-md-4">
			<?php  $image = get_field('logo', 'option');
			if( !empty($image) ): ?>
				<img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" width="<?php echo $image['width']; ?>" height="<?php echo $image['height']; ?>" class="img-responsive" />
			<?php endif; ?>
		</div>
		<div class="col-md-8">
			<span class="author vcard">
			<?php //the_author_posts_link(); ?>
			About <?php the_field('short_name', 'option'); ?></span>
			<div class="about"><?php the_field('about', 'option'); ?></div>
		</div>
	</div>
</footer>
<?php } ?>