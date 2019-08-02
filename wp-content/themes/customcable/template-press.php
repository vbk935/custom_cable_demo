<?php
/**
 * Template Name: Press Releases
 *
 */
get_header(); ?>
<div class="col-sm-9 clearfix main-column" data-mh="equal">
	<section id="content" role="main">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<header class="header">
				<?php include(WP_CONTENT_DIR . '/plugins/sea-spider-tyrex/snippets/article-header.php'); ?>
				<div class="breadcrumb"><?php get_breadcrumb(); ?></div>
				<h1 class="entry-title"><?php the_title(); ?></h1>
			</header>
			<section class="entry-content">
				<?php include(WP_CONTENT_DIR . '/plugins/sea-spider-tyrex/snippets/article-subheader.php'); ?>			
				<?php include(WP_CONTENT_DIR . '/plugins/sea-spider-tyrex/snippets/contact-page-team.php'); ?>
				<?php the_content(); ?>
				<?php include (WP_CONTENT_DIR . '/plugins/sea-spider-tyrex/snippets/flex-content.php'); ?>
				<?php include(WP_CONTENT_DIR . '/plugins/sea-spider-tyrex/snippets/press-releases.php'); ?>
				<div class="entry-links"><?php wp_link_pages(); ?></div>
			</section>
		</article>
		<?php 
			endwhile; 
			endif; 
		?>
	</section>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>