<?php if( have_rows('simple_sections') ): ?>
<div class="sections">
<?php while ( have_rows('simple_sections') ) : the_row();

	if( get_row_layout() == 'one_column_section' ): ?>
		<div class="columns"><?php the_sub_field('1_column_1'); ?></div>
	<?php
	elseif( get_row_layout() == 'two_column_section' ): 
		include (STYLESHEETPATH . '/snippets/flex-content/two-col.php');
	elseif( get_row_layout() == 'three_column_section' ): 
		include (STYLESHEETPATH . '/snippets/flex-content/three-col.php');
	elseif( get_row_layout() == 'four_column_section' ): 
		include (STYLESHEETPATH . '/snippets/flex-content/four-col.php');

	
	elseif( get_row_layout() == 'full_width_image' ): 
		include (STYLESHEETPATH . '/snippets/flex-content/full-width-image.php');

	elseif( get_row_layout() == 'header' ): ?> 
		<h2 class="section-h2"><?php the_sub_field('header_title'); ?></h2><?php
	
	elseif( get_row_layout() == 'image_gallery' ): 
		include (STYLESHEETPATH . '/snippets/flex-content/gallery.php');
	
	elseif( get_row_layout() == 'mini_slideshow' ): 
		include (STYLESHEETPATH . '/snippets/flex-content/mini-slideshow.php');

	elseif( get_row_layout() == 'image_grid' ): 
		include (STYLESHEETPATH . '/snippets/flex-content/image-grid.php');		
	
	elseif( get_row_layout() == 'table' ): 
		include (STYLESHEETPATH . '/snippets/flex-content/table.php');
	
	elseif( get_row_layout() == 'panel_row' ): 
		include (STYLESHEETPATH . '/snippets/flex-content/panel.php');

	elseif( get_row_layout() == 'embed_video' ): 
		include (STYLESHEETPATH . '/snippets/flex-content/videos.php');

	elseif( get_row_layout() == 'download_list' ): 
		include (STYLESHEETPATH . '/snippets/flex-content/downloads.php');

	elseif( get_row_layout() == 'company_table' ): 
		include (STYLESHEETPATH . '/snippets/flex-content/company-table.php');

//	elseif( get_row_layout() == 'download_buttons' ): 
//		include (STYLESHEETPATH . '/snippets/flex-content/download-buttons.php');


	endif;

endwhile;?>
</div>









<?php endif; ?>






