<?php if( have_rows('sidebar') ): ?>
<div class="pull-<?php echo get_field('which_side'); ?> <?php echo get_field('image_width'); ?> article-sidebar">
	<div class="row">
	<div class="">
	<?php while ( have_rows('sidebar') ) : the_row(); ?>
		<?php if( get_row_layout() == 'image' ): ?>
			<div class="item thumbnail">
				<?php $image = get_sub_field('image_file');
				if( !empty($image) ): ?>
					<?php $link = get_sub_field('link'); ?>
					<?php if($link != "") { ?><a href="<?php echo $link; ?>"><?php } ?>
						<img src="<?php echo $image['sizes']['medium']; ?>" alt="<?php echo $image['alt']; ?>"  width="<?php echo $image['sizes']['medium-width']; ?>" height="<?php echo $image['sizes']['medium-height']; ?>"/>
					<?php if($link != "") { ?></a><?php } ?>
					<div><?php the_sub_field('extra_text'); ?></div>
				<?php endif; ?>
			</div>
			<div style="clear:both"></div>

		<?php elseif( get_row_layout() == 'text_link' ): ?>

			<div class="item-link">
				<?php 
					$link = get_sub_field('link'); 
					$text = get_sub_field('extra_text');
				if( !empty($text) ): ?>
					<?php if($link != "") { ?><a href="<?php echo $link; ?>"><?php } ?>
							<?php echo $text; ?>
					<?php if($link != "") { ?></a><?php } ?>
				<?php endif; ?>
			</div>
			<div style="clear:both"></div>

		<?php elseif( get_row_layout() == 'file' ): ?>
		
			<div class="item thumbnail">
				<?php 
					$image = get_sub_field('image');
					$file = get_sub_field('file'); 
					$text = get_sub_field('extra_text');
				if( !empty($file) ): ?>
					<?php if($link != "") { ?><a href="<?php echo $file; ?>"><?php } ?>
						<?php if($image != "") { ?>
							<img src="<?php echo $image['sizes']['medium']; ?>" alt="<?php echo $image['alt']; ?>"  width="<?php echo $image['sizes']['medium-width']; ?>" height="<?php echo $image['sizes']['medium-height']; ?>"/>
						<?php } else { ?>
							<?php echo $text; ?>
						<?php } ?>
					<?php if($link != "") { ?></a><?php } ?>
						<?php if($image == "") { ?><?php echo $text; ?><?php } ?>
				<?php endif; ?>
			</div>
			<div style="clear:both"></div>		
		
		<?php endif; ?>
	<?php endwhile; ?>
	</div>
	</div>
</div>
<?php endif; ?>