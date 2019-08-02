<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	
    <header class="well well-newsletter">
    
        	<a href="<?php if(is_archive('newsletter')) { 
							$file = get_field('newsletter_pdf'); 
							echo $file['url'];
								} else { 
							the_permalink(); } ?>" title="<?php the_title_attribute(); ?>" rel="bookmark">
                            
                <?php if ( has_post_thumbnail() ) { echo get_the_post_thumbnail( $post->ID, 'full', array( 'class' => 'thumbnail' ) ); } ?>
                
				<div class="caption">
					<?php if ( is_singular() ) { echo '<h1 class="entry-title">'; } else { echo '<h2 class="entry-title">'; } ?>
                        <div class="title"><?php the_title(); ?></div>
                    <?php if ( is_singular() ) { echo '</h1>'; } else { echo '</h2>'; } ?>
                 </div>
            </a>
            
        <?php edit_post_link(); ?>
        <?php if ( !is_search() ) get_template_part( 'entry', 'meta' ); ?>
	</header>
	<?php //get_template_part( 'entry', ( is_archive() || is_search() ? 'summary' : 'content' ) ); ?>
	<?php //if ( !is_search() ) get_template_part( 'entry-footer' ); ?>
</article>
