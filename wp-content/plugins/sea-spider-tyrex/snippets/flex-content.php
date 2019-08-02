<?php if( get_field('clear') ) { ?>
	<div class="clear"></div>
<?php } ?>
<?php if( have_rows('simple_sections') ): ?>
<div class="sections">
<?php while ( have_rows('simple_sections') ) : the_row();







	if( get_row_layout() == 'one_column_section' ): ?>
		<div class="columns"><?php the_sub_field('1_column_1'); ?></div>
	<?php
	elseif( get_row_layout() == 'two_column_section' ): 
		include (WP_CONTENT_DIR . '/plugins/sea-spider-tyrex/snippets/flex-content/two-col.php');
	elseif( get_row_layout() == 'three_column_section' ): 
		include (WP_CONTENT_DIR . '/plugins/sea-spider-tyrex/snippets/flex-content/three-col.php');
	elseif( get_row_layout() == 'four_column_section' ): 
		include (WP_CONTENT_DIR . '/plugins/sea-spider-tyrex/snippets/flex-content/four-col.php');

	
	elseif( get_row_layout() == 'full_width_image' ): 
		include (WP_CONTENT_DIR . '/plugins/sea-spider-tyrex/snippets/flex-content/full-width-image.php');

	elseif( get_row_layout() == 'header' ): ?> 
		<h2 class="section-h2"><?php the_sub_field('header_title'); ?></h2><?php
	
	elseif( get_row_layout() == 'image_gallery' ): 
		include (WP_CONTENT_DIR . '/plugins/sea-spider-tyrex/snippets/flex-content/gallery.php');
	
	elseif( get_row_layout() == 'mini_slideshow' ): 
		include (WP_CONTENT_DIR . '/plugins/sea-spider-tyrex/snippets/flex-content/mini-slideshow.php');

	elseif( get_row_layout() == 'image_grid' ): 
		include (WP_CONTENT_DIR . '/plugins/sea-spider-tyrex/snippets/flex-content/image-grid.php');		
	
	elseif( get_row_layout() == 'table' ): 
		include (WP_CONTENT_DIR . '/plugins/sea-spider-tyrex/snippets/flex-content/table.php');
	
	elseif( get_row_layout() == 'panel_row' ): 
		include (WP_CONTENT_DIR . '/plugins/sea-spider-tyrex/snippets/flex-content/panel.php');

	elseif( get_row_layout() == 'embed_video' ): 
		include (WP_CONTENT_DIR . '/plugins/sea-spider-tyrex/snippets/flex-content/videos.php');

	elseif( get_row_layout() == 'download_list' ): 
		include (WP_CONTENT_DIR . '/plugins/sea-spider-tyrex/snippets/flex-content/downloads.php');

	elseif( get_row_layout() == 'company_table' ): 
		include (WP_CONTENT_DIR . '/plugins/sea-spider-tyrex/snippets/flex-content/company-table.php');

	elseif( get_row_layout() == 'download_buttons' ): 
		include (WP_CONTENT_DIR . '/plugins/sea-spider-tyrex/snippets/flex-content/download-buttons.php');


	endif;

endwhile;?>
</div>









<?php endif; ?>






