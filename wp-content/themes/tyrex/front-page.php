<?php get_header(); ?>

<?php include(WP_CONTENT_DIR . '/plugins/sea-spider-tyrex/snippets/slideshow-front.php'); ?>


<div class="title">
	<h2 class="home"><?php the_field('tagline', 'option'); ?></h2>
	<?php echo wp_get_attachment_image( 700, 'full', 0, array(  'class' => 'img-responsive h2-art' ) ); ?>
</div>
	
	
<div class="lead-ins">
	<a class="col-sm-4 lead-in" href="<?php the_field('box_1_link'); ?>" title="<?php the_field('box_1_title'); ?>">
		<?php $image = get_field('box_1_photo');
	  if( !empty($image) ): ?>
		 <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" width="<?php echo $image['width']; ?>" height="<?php echo $image['height']; ?>" class="img-responsive center-block" />
	  <?php endif; ?>            
		<h3><?php the_field('box_1_title'); ?></h3>
		<p><?php the_field('box_1_text'); ?></p>
	</a>
	<a class="col-sm-4 lead-in" href="<?php the_field('box_2_link'); ?>" title="<?php the_field('box_2_title'); ?>">
		<?php $image = get_field('box_2_photo');
	  if( !empty($image) ): ?>
		 <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" width="<?php echo $image['width']; ?>" height="<?php echo $image['height']; ?>" class="img-responsive center-block" />
	  <?php endif; ?> 
		<h3><?php the_field('box_2_title'); ?></h3>
		<p><?php the_field('box_2_text'); ?></p>
	</a>
	<a class="col-sm-4 lead-in" href="<?php the_field('box_3_link'); ?>" title="<?php the_field('box_3_title'); ?>">
		<?php $image = get_field('box_3_photo');
	  if( !empty($image) ): ?>
		 <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" width="<?php echo $image['width']; ?>" height="<?php echo $image['height']; ?>" class="img-responsive center-block" />
	  <?php endif; ?> 
		<h3><?php the_field('box_3_title'); ?></h3>
		<p><?php the_field('box_3_text'); ?></p>
	</a>
</div>		


<div class="clear"></div>
<div class="shaped-buttons">
	<h2>Industries Served</h2>
	<ul class="nav nav-tabs " role="navigation">
		<li class="shaped-button">
			<a href="<?php echo get_permalink( 1095 ); ?>" data-container="body" data-toggle="popover" data-placement="bottom" data-content="CATV (Cable TV)" class="popover">
				<?php echo wp_get_attachment_image( 734, 'medium', 0, array(  'class' => 'img-responsive' ) ); ?>
			</a>
		</li>
		<li class="shaped-button blue">
			<a href="<?php echo get_permalink( 1096 ); ?>" data-container="body" data-toggle="popover" data-placement="bottom" data-content="OSP (Outside Plant)" class="popover">
				<?php echo wp_get_attachment_image( 741, 'medium', 0, array(  'class' => 'img-responsive' ) ); ?>
			</a>
		</li>
		<li class="shaped-button">
			<a href="<?php echo get_permalink( 1097 ); ?>"  data-container="body" data-toggle="popover" data-placement="bottom" data-content="Data Center" class="popover">
				<?php echo wp_get_attachment_image( 736, 'medium', 0, array(  'class' => 'img-responsive' ) ); ?>
			</a>
		</li>
		<li class="shaped-button blue">
			<a href="<?php echo get_permalink( 1098 ); ?>" data-container="body" data-toggle="popover" data-placement="bottom" data-content="Test & Measurement" class="popover">
				<?php echo wp_get_attachment_image( 745, 'medium', 0, array(  'class' => 'img-responsive' ) ); ?>
			</a>
		</li>
		<li class="shaped-button">
			<a href="<?php echo get_permalink( 1099 ); ?>" data-container="body" data-toggle="popover" data-placement="bottom" data-content="Telecommunications" class="popover">
				<?php echo wp_get_attachment_image( 746, 'medium', 0, array(  'class' => 'img-responsive' ) ); ?>
			</a>
		</li>
		<li class="shaped-button blue">
			<a href="<?php echo get_permalink( 1100 ); ?>" data-container="body" data-toggle="popover" data-placement="bottom" data-content="Industrial & Military" class="popover">
				<?php echo wp_get_attachment_image( 739, 'medium', 0, array(  'class' => 'img-responsive' ) ); ?>
			</a>
		</li>
	</ul>
</div>

<br /><br />
<?php include(WP_CONTENT_DIR . '/plugins/sea-spider-tyrex/snippets/flex-content.php'); ?>

<?php get_footer(); ?>

