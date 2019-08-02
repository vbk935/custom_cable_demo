<?php
global $porto_settings, $porto_layout;

$default_layout = porto_meta_default_layout();
$wrapper        = porto_get_wrapper_type();
?>
		<?php get_sidebar(); ?>

		<?php if ( porto_get_meta_value( 'footer', true ) ) : ?>

			<?php

			$cols = 0;
			for ( $i = 1; $i <= 4; $i++ ) {
				if ( is_active_sidebar( 'content-bottom-' . $i ) ) {
					$cols++;
				}
			}

			if ( is_404() ) {
				$cols = 0;
			}

			if ( $cols ) :
				?>
				<?php if ( 'boxed' == $wrapper || 'fullwidth' == $porto_layout || 'left-sidebar' == $porto_layout || 'right-sidebar' == $porto_layout ) : ?>
					<div class="container sidebar content-bottom-wrapper">
					<?php
				else :
					if ( 'fullwidth' == $default_layout || 'left-sidebar' == $default_layout || 'right-sidebar' == $default_layout ) :
						?>
					<div class="container sidebar content-bottom-wrapper">
					<?php else : ?>
					<div class="container-fluid sidebar content-bottom-wrapper">
						<?php
					endif;
				endif;
				?>

				<div class="row">

					<?php
					$col_class = array();
					switch ( $cols ) {
						case 1:
							$col_class[1] = 'col-md-12';
							break;
						case 2:
							$col_class[1] = 'col-md-12';
							$col_class[2] = 'col-md-12';
							break;
						case 3:
							$col_class[1] = 'col-lg-4';
							$col_class[2] = 'col-lg-4';
							$col_class[3] = 'col-lg-4';
							break;
						case 4:
							$col_class[1] = 'col-lg-3';
							$col_class[2] = 'col-lg-3';
							$col_class[3] = 'col-lg-3';
							$col_class[4] = 'col-lg-3';
							break;
					}
					?>
						<?php
						$cols = 1;
						for ( $i = 1; $i <= 4; $i++ ) {
							if ( is_active_sidebar( 'content-bottom-' . $i ) ) {
								?>
								<div class="<?php echo esc_attr( $col_class[ $cols++ ] ); ?>">
									<?php dynamic_sidebar( 'content-bottom-' . $i ); ?>
								</div>
								<?php
							}
						}
						?>

					</div>
				</div>
			<?php endif; ?>

			</div><!-- end main -->

			<?php
			do_action( 'porto_after_main' );
			$footer_view = porto_get_meta_value( 'footer_view' );
			?>

			<div class="footer-wrapper<?php echo 'wide' == $porto_settings['footer-wrapper'] ? ' wide' : ''; ?> <?php echo esc_attr( $footer_view ); ?>">

				<?php if ( porto_get_wrapper_type() != 'boxed' && 'boxed' == $porto_settings['footer-wrapper'] ) : ?>
				<div id="footer-boxed">
				<?php endif; ?>

				<?php if ( is_active_sidebar( 'footer-top' ) && ! $footer_view ) : ?>
					<div class="footer-top">
						<div class="container">
							<?php dynamic_sidebar( 'footer-top' ); ?>
						</div>
					</div>
				<?php endif; ?>

				<?php
					get_template_part( 'footer/footer' );
				?>

				<?php if ( porto_get_wrapper_type() != 'boxed' && 'boxed' == $porto_settings['footer-wrapper'] ) : ?>
				</div>
				<?php endif; ?>

			</div>

		<?php else : ?>

			</div><!-- end main -->

			<?php
			do_action( 'porto_after_main' );
		endif;
		?>

	</div><!-- end wrapper -->
	<?php do_action( 'porto_after_wrapper' ); ?>

<?php

if ( isset( $porto_settings['mobile-panel-type'] ) && 'side' === $porto_settings['mobile-panel-type'] ) {
	// navigation panel
	get_template_part( 'panel' );
}

?>

<!--[if lt IE 9]>
<script src="<?php echo esc_url( PORTO_JS ); ?>/libs/html5shiv.min.js"></script>
<script src="<?php echo esc_url( PORTO_JS ); ?>/libs/respond.min.js"></script>
<![endif]-->

<?php wp_footer(); ?>

<?php
// js code (Theme Settings/General)
if ( isset( $porto_settings['js-code'] ) && $porto_settings['js-code'] ) {
	?>
	<script>
		<?php echo porto_filter_output( $porto_settings['js-code'] ); ?>
	</script>
<?php } ?>
<?php if ( isset( $porto_settings['page-share-pos'] ) && $porto_settings['page-share-pos'] ) : ?>
	<div class="page-share position-<?php echo esc_attr( $porto_settings['page-share-pos'] ); ?>">
		<?php get_template_part( 'share' ); ?>
	</div>
<?php endif; ?>
</body>
</html>
