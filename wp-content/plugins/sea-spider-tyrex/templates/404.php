<?php get_header(); ?>

<div class="container-fluid">
	<div class="row">
		<div class="col-sm-9 clearfix" data-mh="equal">
			<section id="content" role="main">
				<article id="post-0" class="post not-found">
					<header class="header">
						<?php  $image = get_field('404_featured_image', 'option');
						if( !empty($image) ): ?>
						<img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" class="img-responsive" style="padding-left: 0;margin-left: -10px;"/>
						<?php endif; ?>
						<h1 class="entry-title">
							<?php  the_field('title_404', 'option');?>
						</h1>
					</header>
					<section class="entry-content">
						<?php  the_field('404_text', 'option');?>
						<?php get_search_form(); ?>
					</section>
				</article>
			</section>
		</div>
		<?php include(WP_CONTENT_DIR . '/plugins/sea-spider-tyrex/templates/sidebar.php'); ?>
	</div>
</div>
<?php get_footer(); ?>
