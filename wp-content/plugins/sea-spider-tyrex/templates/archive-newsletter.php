<?php get_header(); ?>

<div class="cxontainer-fluid">
  <div class="row">
    <div class="col-sm-9 clearfix" data-mh="equal">
      <section id="content" role="main">
        <header class="header">
          <?php  $image = get_field('newsletter_header', 'option');
			if( !empty($image) ): ?>
          <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" class="img-responsive" />
          <?php endif; ?>
          <h1 class="entry-title">
            <?php 
                    if ( is_day() ) { 
						printf( __( 'Daily Archives: %s', 'blankslate' ), get_the_time( get_option( 'date_format' ) ) ); 
					} elseif ( is_month() ) { 
						printf( __( 'Monthly Archives: %s', 'blankslate' ), get_the_time( 'F Y' ) ); 
					} elseif ( is_year() ) { 
						printf( __( 'Yearly Archives: %s', 'blankslate' ), get_the_time( 'Y' ) ); 
					} else {
						if (is_archive('newsletter')) {echo "Newsletter ";}
						 _e( 'Archive', 'blankslate' ); }
                  ?>
          </h1>
        </header>
        <div class="row">
          <?php query_posts(array('post_type' => 'newsletter', 'posts_per_page'=>-1));
                if ( have_posts() ) { while ( have_posts() ) {the_post(); ?>
          <div class="col-md-3 col-sm-6">
          
          
          
          
                
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <header class="well well-newsletter"> <a href="<?php if(is_archive('newsletter')) { 
                                            $file = get_field('newsletter_pdf'); 
                                            echo $file['url'];
                                                } else { 
                                            the_permalink(); } ?>" title="<?php the_title_attribute(); ?>" rel="bookmark">
                        <?php if ( has_post_thumbnail() ) { echo get_the_post_thumbnail( $post->ID, 'sidebar', array( 'class' => 'thumbnail' ) ); } ?>
                        <div class="caption">
                            <?php if ( is_singular() ) { echo '<h1 class="entry-title">'; } else { echo '<h2 class="entry-title">'; } ?>
                            <div class="title">
                                <?php the_title(); ?>
                            </div>
                            <?php if ( is_singular() ) { echo '</h1>'; } else { echo '</h2>'; } ?>
                        </div>
                        </a>
                        <?php if ( !is_search() ) get_template_part( 'entry', 'meta' ); ?>
                    </header>
                    <?php //get_template_part( 'entry', ( is_archive() || is_search() ? 'summary' : 'content' ) ); ?>
                    <?php //if ( !is_search() ) get_template_part( 'entry-footer' ); ?>
                </article>





          </div>
          <?php } } ?>
        </div>
        <?php get_template_part( 'nav', 'below' ); ?>
      </section>
      <?php if ( !is_search() ) get_template_part( 'entry-footer' ); ?>
    </div>
    <?php get_sidebar(); ?>
  </div>
</div>
<?php get_footer(); ?>
