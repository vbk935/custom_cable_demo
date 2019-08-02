<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> test>
  <div class="media"> <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark" class="pull-left">
    <?php if ( has_post_thumbnail() ) { echo get_the_post_thumbnail( $post->ID, 'medium', array( 'class' => 'media-object col-md-2' ) ); } ?>
    </a>
    <div class="media-body">
      <?php if ( is_singular() ) { echo '<h1 class="entry-title">'; } else { echo '<h2 class="media-heading">'; } ?>
      <?php the_title(); ?>
      <?php if ( is_singular() ) { echo '</h1>'; } else { echo '</h2>'; } ?>
      <?php edit_post_link(); ?>
      <?php if ( !is_search() ) get_template_part( 'entry', 'meta' ); ?>
      <?php get_template_part( 'entry', ( is_archive() || is_search() ? 'summary' : 'content' ) ); ?>
    </div>
  </div>
</article>
