<?php
/*
 * Template Name: Custom Cable Configuration
 */
?>

<?php get_header('custom'); ?>

<div class="col-sm-12 clearfix main-column" data-mh="equal">
    <section id="content" role="main">
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <header class="header">

                        <?php include(WP_CONTENT_DIR . '/plugins/sea-spider-tyrex/snippets/article-header.php'); ?>


                        <h1 class="entry-title"><?php the_title(); ?></h1>

                        <?php edit_post_link(); ?>
                    </header>
                    <section class="entry-content">
                        <?php
                        if (is_page('Contact Us')) {
                            include (STYLESHEETPATH . '/snippets/address-panel.php');
                            include (STYLESHEETPATH . '/snippets/map.php');
                            ?>
                            <h2 class="sub-head" style="color:#2f2c7a">Send Us An Email</h2>
                            <?php
                            echo do_shortcode('[formidable id=7]');
                        } else {
                            ?>
                            <?php include(WP_CONTENT_DIR . '/plugins/sea-spider-tyrex/snippets/article-subheader.php'); ?>
                            <?php the_content(); ?>
                        <? } ?>
                        <?php include(WP_CONTENT_DIR . '/plugins/sea-spider-tyrex/snippets/flex-content.php'); ?>
                        <?php include (STYLESHEETPATH . '/snippets/link-list.php'); ?>
                        <?php include (STYLESHEETPATH . '/snippets/content-panels.php'); ?>
                        <?php include (STYLESHEETPATH . '/snippets/news.php'); ?>
                        <?php include (STYLESHEETPATH . '/snippets/press-releases.php'); ?>

                        <div class="entry-links"><?php wp_link_pages(); ?></div>
                    </section>
                </article>
                <?php
            endwhile;
        endif;
        ?>
    </section>
</div>
<?php get_footer(); ?>
