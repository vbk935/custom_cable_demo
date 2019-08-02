
<aside id="sidebar" role="complementary" class="sidebar col-sm-3" <?php /*?>data-mh="equal"<?php */?>>


<? /////////////////////////////////   Default Widget Area ; ?>    

	<?php if ( is_active_sidebar( 'primary-widget-area' ) ) : ?>
	<div id="primary" class="widget-area">
		<ul class="xoxo">
			<?php dynamic_sidebar( 'primary-widget-area' ); ?>
		</ul>
	</div>
	<?php endif; ?>


<?php /*?>	<h2>Downloads</h2>
	<div class="red-box"></div><?php */?>


		



<? /////////////////////////////////   Newsletter ; ?>    
<?   
?>

<?php /*?>	<?php
    $posts = get_posts(array(
        'numberposts' => 1,
        'post_type' => 'newsletter'
    ));


    
    if($posts){
        foreach($posts as $post){ 
            $attachment_pdf = get_field('newsletter_pdf');
        ?>
        <a href="<?php echo $attachment_pdf['url']; ?>" class="download" title="<?php echo $attachment_pdf['title']; ?>">
            <?php the_post_thumbnail('sidebar', array('class' => 'img-responsive')); ?>
            <div class="title">TyRex Newsletter</div>
        </a>
        <?	}
    
    }
    wp_reset_postdata();

    ?>
	<div class="download-small">
		<a href="<?php echo get_page_link(165); ?>">Subscribe</a>
		<span> &bull; </span>
		<a href="<?php echo get_post_type_archive_link( 'newsletter' ); ?>">Archive</a>
	</div><?php */?>
    
    
<? /////////////////////////////////   Other Downloads ; ?>    


<?php /*?>	<?php
    $posts = get_posts(array(
        'numberposts' 	=> -1,
        'post_type'		=> 'download'
    ));

    if($posts){ ?>
        <? foreach($posts as $post){ 
            $attachment_pdf = get_field('pdf');
        ?>
        <a href="<?php echo $attachment_pdf['url']; ?>" class="download" title="<?php echo $attachment_pdf['title']; ?>">
       	            <?php the_post_thumbnail('sidebar', array('class' => 'img-responsive')); ?>
                    <div class="title"><? the_title(); ?></div>                    
                </a>
          <? }}  else {?>
  			<p>No Downloads at the moment.</p>
    		<? } 
			    wp_reset_postdata();

		?><?php */?>
    
<? /////////////////////////////////   Quick links ; ?>    
   
    
  <?php /*?><h2>Quick Links</h2>
	<div class="red-box"></div>
    
 
	<?php
    $posts = get_posts(array(
        'numberposts' 	=> -1,
        'post_type'		=> 'quicklinks',
		'meta_key'		=> 'order_number',
		'orderby'		=> 'meta_value_num',
		'order'			=> 'DESC'
    ));
    
    if($posts){ ?>
    	<ul class="list-unstyled">
        <? foreach($posts as $post){ 
		$type = get_field('quicklink_type') ;
		if($type == "internal") {  ?>
            <li>
                <a href="<?php the_field('internal_link'); ?>" title="<? the_title(); ?>">
                    <? the_title(); ?>
                </a>
            </li>
        <? } else { ?>
            <li>
                <a href="<?php the_field('external_link'); ?>" title="<? the_title(); ?>" target="_blank">
                    <? the_title(); ?>
                </a>
            </li>        
        <? }} ?>
		</ul>
		<?	
    
    }  else {?>
  			<p>No Links at the moment.</p>
    		<? } 
		    wp_reset_postdata();

		
		?>

<? /////////////////////////////////   Recent Posts ; ?>    
	<h2>Recent News & Events</h2>
	<div class="red-box"></div>        
 
    	<ul class="list-unstyled">
			<?php
                $args = array( 'numberposts' => '5' );
                $recent_posts = wp_get_recent_posts( $args );
                foreach( $recent_posts as $recent ){
                    echo '<li><a href="' . get_permalink($recent["ID"]) . '" title="'.esc_attr($recent["post_title"]).'" >' .   $recent["post_title"].'</a> </li> ';
    		} ?>
		</ul>
<?php */?>

 
 
    

</aside>


