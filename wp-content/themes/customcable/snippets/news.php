<?php
if(is_page("News & Events")) { ?>
<div class="news-list">	
	<?php
        // page totals
        $first_page_total= 6; // total number of posts on first page
        $paginated_total = 6; // total number of posts on paginated pages
        $posts_to_skip = $paginated_total - $first_page_total;
       
        // pagination for custom page(s)
        if ( get_query_var('paged') ) { $paged = get_query_var('paged'); }
        elseif ( get_query_var('page') ) { $paged = get_query_var('page'); }
        else { $paged = 1; }
       
        // get the category ID
        $idObj1 = get_category_by_slug('updates');
        $current_category = $idObj1->term_id;
       
        // first page query args
        $args1 = array(
		'post_type' => array( 'post', 'article' ),
          'order'          => 'DESC',
          'post_status'    => 'publish',
         // 'cat'            => $current_category,
          'paged'          => $paged,
          'posts_per_page' => $first_page_total
        );
       
        if(is_paged()) {
                // paginated query args
                $offset = (($paged - 1) * $paginated_total)- $posts_to_skip;
                $args1['offset'] = $offset;
                $args1['posts_per_page'] = $paginated_total;
        }
       
        $the_query = new WP_Query( $args1 );
        $found_posts = $the_query->found_posts;
 
        //correct the max number of pages
        $pages = 1 + ceil(($found_posts - $first_page_total) / $paginated_total);
       
        // add the correct number of pages to the query
        $the_query->max_num_pages = $pages;
       
        // pagenavi before the the loop ?>
        <div class="col-xs-12 text-right"><?php wp_pagenavi(array('query' => $the_query)); ?></div>
 
        <?php // Start of the Loop
         while ( $the_query->have_posts() ) : $the_query->the_post(); ?>

					<div class="col-xs-12 entry">
						<div class="row">
							<div class="col-md-4">
								<? if ( has_post_thumbnail() ) { ?> 
									<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('medium'); ?></a>						
								<? } ?>
							</div>
							<div class="col-md-8">
								<h2><a href="<?php the_permalink(); ?>"><? the_title() ; ?></a></h2>
								<strong class="entry-date"><?php the_time( get_option( 'date_format' ) ); ?></strong>
								<? the_excerpt() ; ?>
								<a href="<?php the_permalink(); ?>">>> Read More</a>
							</div>
						</div>
					</div>

        <?php endwhile; // end of the loop. ?>
 
        <?php
        // pagenavi after the loop ?>
        <div class="col-xs-12 text-right"><?php wp_pagenavi(array('query' => $the_query)); ?></div>
</div>
<?php } ?>