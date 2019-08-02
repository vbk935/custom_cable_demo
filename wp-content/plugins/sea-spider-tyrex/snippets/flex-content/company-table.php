<div class="table-responsive">
	<table class="table table-striped table-bordered directory">
		<tbody>		
<?php $posts = get_posts(array(
	'post_type'		=> 'company',
	'posts_per_page'	=> -1,
	'orderby'			=> 'title',
	'order'			=> 'ASC',
));
if($posts) foreach($posts as $post) { ?>
<tr class="<?php the_field('code'); ?>-section">
	<td align="center" valign="middle">
		<a title="<?php the_field('full_name'); ?>" href="<?php the_field('website_main'); ?>" target="_blank">

			<?php $image = get_field('logo_wide');
                    if( !empty($image) ): ?>
				<img src="<?php echo $image['url']; ?>" width="<?php echo $image['width']; ?>" height="<?php echo $image['height']; ?>" alt="<?php echo $image['alt']; ?>" class="company-image <?php the_field('code'); ?>-logo"/>
				<?php endif; ?>
		</a>
	</td>
	<td align="center" valign="middle">
		<a title="<?php the_field('full_name'); ?>" href="<?php the_field('website_main'); ?>" target="_blank">
			<?php the_field('short_description'); ?>
		</a>
	</td>
</tr>
<?php } ?>	
		
		
		
		</tbody>
	</table>
</div>