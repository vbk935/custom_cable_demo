<?php
$sidebar = get_field('sidebar_groups', 'option'); 
	if( have_rows('sidebar_groups', 'option') ):
		while ( have_rows('sidebar_groups', 'option') ) : the_row();
     

			if( get_row_layout() == 'download' ):
				$post_object = get_sub_field('download');
				setup_postdata( $post ); 
					if( $post_object ):
						$post = $post_object; setup_postdata( $post ); ?>
							<div class="sidebar-download">
							<?php $attachment_pdf = get_field('pdf');?>
							<a href="<?php echo $attachment_pdf['url']; ?>" class="download">
								<?php the_post_thumbnail('sidebar', array('class' => 'img-responsive center-block')); ?>
								<div class="title"><?php the_title(); ?></div>
							</a>
							</div>
				<?php wp_reset_postdata(); 
					endif;
					
			 
			 elseif( get_row_layout() == 'header' ):
				$header = get_sub_field('header'); ?>
				<h2><?php echo $header; ?></h2>
				<div class="red-box"></div>				
				<?php 



			 elseif( get_row_layout() == 'news_and_events' ):
				$display_amount = get_sub_field('display_amount'); 
				$types = get_sub_field('types'); ?>
				<?php if( in_array( 'articles', get_sub_field('types') ) ) { $articles = "articles"; } ?>
				<?php if( in_array( 'post', get_sub_field('types') ) ) { $posts = "posts"; } ?>
				
				<?php if ($articles != "" AND $posts != "")  { $post_types = "post,articles"; } ?> 
				<?php if ($articles != "articles" AND $posts != "")  { $post_types = "articles"; } ?> 
				<?php if ($articles != "" AND $posts != "post")  { $post_types = "post"; } ?> 
					<ul class="">
						<?php
						$args = array( 'post_type' => array($post_types), 'posts_per_page' => $display_amount );
						$recent_posts = wp_get_recent_posts( $args );
						foreach( $recent_posts as $recent ){
							echo '<li><a href="' . get_permalink($recent["ID"]) . '" title="'.esc_attr($recent["post_title"]).'" >' .   $recent["post_title"].'</a> </li> ';
						} ?>
					</ul>				
				

				
<?php
			 elseif( get_row_layout() == 'newsletter' ):
				$post_object = get_sub_field('newsletter');
				setup_postdata( $post ); 
					if( $post_object ):
						$post = $post_object; setup_postdata( $post ); ?>
						<?php $attachment_pdf = get_field('newsletter_pdf');?>
							<a href="<?php echo $attachment_pdf['url']; ?>" class="download">
								<?php the_post_thumbnail('sidebar', array('class' => 'img-responsive center-block')); ?>
								<div class="title"><?php the_title(); ?></div>
							</a>
							<div class="download-small">
								<a href="<?php echo get_page_link(165); ?>">Subscribe</a>
								<span> &bull; </span>
								<a href="<?php echo get_post_type_archive_link( 'newsletter' ); ?>">Archive</a>
							</div>
				<?php wp_reset_postdata(); 
					endif;

					
					
			 elseif( get_row_layout() == 'image_link' ): 
			 	$image = get_sub_field('image'); 
				if( !empty($image) ): ?>
					<div class="sidebar-item">
					<a href="<?php the_sub_field('url'); ?>" <?php if(get_sub_field('external_link') == 1) { ?> target="_blank" <?php } ?>>
						<img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" class="img-responsive center-block"/>
						<?php the_sub_field('text'); ?>
					</a>
					</div>
				<?php endif; ?>
				
				
				
				
				
			 <?php elseif( get_row_layout() == 'text_links' ): ?>
						<ul>
						<?php while ( have_rows('text_link_group') ) : the_row(); ?>
					
						  <?php $text = get_sub_field('text'); ?>
						   <?php $link = get_sub_field('link'); ?>
						   <li><a href="<?php echo $link; ?>"><?php echo $text; ?></a></li>
					
					    <?php endwhile; ?>
					    </ul>
				
				
						
						
							
				
		<?php
		endif;
		endwhile;
	endif; ?> 