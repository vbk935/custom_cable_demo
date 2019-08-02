<footer class="entry-footer">
	<div class="row">
    	<div class="col-md-4">			
			<?php 
			$attachment_id = 32; // attachment ID
			$image_attributes = wp_get_attachment_image_src( $attachment_id, 'medium' ); // returns an array
			if( $image_attributes ) { ?> 
				<img src="<?php echo $image_attributes[0]; ?>" width="<?php echo $image_attributes[1]; ?>" height="<?php echo $image_attributes[2]; ?>" class="img-responsive logo-entry">
			<?php } ?>			
			
			
			
			<span class="entry-date">Published: <?php the_time( get_option( 'date_format' ) ); ?></span>
        </div>
        <div class="col-md-8">
			<span class="author vcard"><?php //the_author_posts_link(); ?> About TyRex</span>
			<p class="about">The technology companies that make up the TyRex Technology Family are connected by something beyond the industry itself – something that is truly special. From our uncompromising commitment to quality and customized solutions for the entire life cycle of your product to our lasting relationships forged by personal commitments to our business partners, TyRex is a company built to last.</p>


<?php /*?>            <div class="entry-social tofix">
                Social Media Sharing
            </div><?php */?>
		  
		</div>
    </div>
	<? if(!is_archive()) { ?>
		<div class="row taxonomy">
			<div class="col-xs-6">
				<span class="cat-links"><?php _e( 'Categories: ', 'blankslate' ); ?><?php the_category( ', ' ); ?></span>
			</div>
			<div class="col-xs-6">
				<span class="tag-links"><?php the_tags(); ?></span>
			</div>   
		</div>
	<? } ?>
</footer> 