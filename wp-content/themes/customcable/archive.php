<?php get_header(); ?>


<div class="cxontainer-fluid">
	<div class="row">
    	<div class="col-sm-9 clearfix" data-mh="equal">
        
        
			<section id="content" role="main">
              <header class="header">
              <img width="850" height="350" src="http://50.62.139.33/~tyrexmfg/tyr/wp-content/uploads/trx-company-header.png" class="attachment-post-thumbnail wp-post-image" alt="Jurassic Park 2365969b">	
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
						 _e( 'Archives', 'blankslate' ); }
                  ?>
                </h1>
              </header>
              <div class="row">
					  <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                       <? if (is_archive()) { echo '<div class="col-md-3">'; }?>
     
                            <? if ( is_post_type_archive('newsletter')) { 
                                get_template_part( 'entry-newsletter' ); 
                            } else {
                                get_template_part( 'entry' ); 
                            } ?>
                        <? if (is_archive()) { echo '</div>'; }?>

                      <?php endwhile; endif; ?>
              </div>
                      <?php get_template_part( 'nav', 'below' ); ?>
            </section>

	<?php if ( !is_search() ) get_template_part( 'entry-footer' ); ?>
    
            
      </div>  
			<?php get_sidebar(); ?>

	</div>
</div>
<?php get_footer(); ?>
