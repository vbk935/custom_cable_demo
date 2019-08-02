<?php
	$post_object = get_sub_field('panel_selector'); 
	if( $post_object ): $post = $post_object; setup_postdata( $post ); 
	$anchor = $post->post_name;  //pick up anchor info
?>	
<a name="<? echo $anchor; ?>"></a>

<?php ///////////////////////////////////////// START PANEL ; ?>	
	<div class="panel panel-default">

		<?php $text_color = get_field('text_color'); ?>
		
		<?php $change_header = get_field('change_header'); ?>
		<?php
		switch ($change_header) {
		    case "default":
			   break;
		    case "company":
				$header_background_color = get_field('header_background_color');
			   break;
		    case "other":
				$other_color = get_field('other_color');
			   break;
		}
		?>
				
		
		
		
		
		
		
	
		<div class="panel-heading <?php echo $header_background_color; ?>" <? if($other_color != "") { ?>style="background:<?php echo $other_color; ?>"; <?php } ?>>
			<h2 class="panel-title" style="color:<?php echo $text_color; ?>;"><?php the_title(); ?></h2>
		</div>
<?php ///////////////////////////////////////// END PANEL HEADER ; ?>	

<?php ///////////////////////////////////////// PANEL layout ; ?>	
		<?php $layout = get_field('panel_layout'); ?>
		<?php switch ($layout) {
				case "1090":
				   $col1 = '<div class="col-md-2">';
				   $col1end = '</div>';
				   $col2 = '<div class="col-md-10">';
				   $col2end = '</div>';
				break;
				case "2080":
				   $col1 = '<div class="col-md-3">';
				   $col1end = '</div>';
				   $col2 = '<div class="col-md-9">';
				   $col2end = '</div>';
				break;
				case "3070":
				   $col1 = '<div class="col-md-4">';
				   $col1end = '</div>';
				   $col2 = '<div class="col-md-8">';
				   $col2end = '</div>';
				break;
				case "4060":
				   $col1 = '<div class="col-md-5">';
				   $col1end = '</div>';
				   $col2 = '<div class="col-md-7">';
				   $col2end = '</div>';
				break;
				case "5050":
				   $col1 = '<div class="col-md-6">';
				   $col1end = '</div>';
				   $col2 = '<div class="col-md-6">';
				   $col2end = '</div>';
				break;
				case "100top":
				   $col1 = '<div class="col-md-12">';
				   $col1end = '</div>';
				   $col2 = '<div class="col-md-12">';
				   $col2end = '</div>';
				break;
				case "100bottom":
				   $col1 = '<div class="col-md-12  col-md-push-12">';
				   $col1end = '</div>';
				   $col2 = '<div class="col-md-12  col-md-pull-12">';
				   $col2end = '</div>';
				break;																							
				default:
				   $col1 = '<div class="col-md-6">';
				   $col1end = '</div>';
				   $col2 = '<div class="col-md-6">';
				   $col2end = '</div>';				
		} ?>


	
		<div class="panel-body">
		
<?php ///////////////////////////////////////// COLUMN 1 ; ?>	
			<?php echo $col1; ?>
		
				<?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'large', array(  'title' => get_the_title(), 'class' => "img-responsive center-block" ) ); } ?>
			<?php 	
			    echo $col1end; 
 ///////////////////////////////////////// COLUMN 2 ; 			    
			    echo $col2; 
			?>			
				<?php the_content(); ?>
	
<?php /////////////////////////////////////////  LINKS ; ?>	
			<?php $add_links = get_field('add_links'); ?>
            <?php $link_type = get_field('link_type'); ?>	
            <?php $postslug = $post->post_name; ?>
        <?php if($add_links == 1) { ?>				
            <?php $make_buttons = get_field('make_buttons'); ?>
            <?php if($make_buttons != 1) {	?>			
                <ul>
                <?php if( have_rows('link_list') ): while ( have_rows('link_list') ) : the_row(); ?>
            
                    <?php if( get_sub_field('link_type') == "external") {  ?>
                        <li><a href="<?php the_sub_field('link_url'); ?>" target="_blank"><?php the_sub_field('link_text'); ?></a></li>
                    <?php } ?>
            
                    <?php if(get_sub_field('link_type') == "internal") { ?>
                        <li><a href="<?php the_sub_field('internal_page'); ?>"><?php the_sub_field('link_text'); ?></a></li>
                    <?php } ?>					
                    
                    
                    <?php if(get_sub_field('link_type') == "anchor") { ?>
                        <?php $anchor = get_sub_field('internal_anchor'); ?>
                        <li><a href="<?php the_sub_field('internal_page'); ?>#<?php echo the_slug_by_id($anchor); ?>"><?php the_sub_field('link_text'); ?></a></li>
                    <?php } ?>						
                    
                <?php endwhile; endif; ?>	
                </ul>
            <?php } else { ?>	
                <div class="row">
                <?php if( have_rows('link_list') ): while ( have_rows('link_list') ) : the_row(); ?>
                    <div class="col-md-3">
                    <?php if( get_sub_field('link_type') == "external") {  ?>
                        <a href="<?php the_sub_field('link_url'); ?>" target="_blank" class="btn read-more"><?php the_sub_field('link_text'); ?></a>
                    <?php } ?>
            
                    <?php if(get_sub_field('link_type') == "internal") { ?>
                        <a href="<?php the_sub_field('internal_page'); ?>" class="btn read-more"><?php the_sub_field('link_text'); ?></a>
                    <?php } ?>					
                    
                    
                    <?php if(get_sub_field('link_type') == "anchor") { ?>
                        <?php $anchor = get_sub_field('internal_anchor'); ?>
                        <a href="<?php the_sub_field('internal_page'); ?>#<?php echo the_slug_by_id($anchor); ?>"class="btn read-more"><?php the_sub_field('link_text'); ?></a>
                        
                    <?php } ?>						
                    </div>
                <?php endwhile; endif; ?>	
                </div>
            <?php } ?>
        <?php } ?>
<?php ///////////////////////////////////////// END LINKS ; ?>	



			

<?php ///////////////////////////////////////// DOWNLOADS ; ?>	
		<?php $add_downloads = get_field('add_downloads'); ?>
        <?php $download_type = get_field('download_type')?>	
        <?php if($add_downloads == 1) { ?>				
                
                <?php if ($download_type == "text") { ?>
                    <?php $add_downloads = get_field('add_downloads'); ?>
                        <ul>			
                    <?php if( have_rows('downloads_repeater') ): while ( have_rows('downloads_repeater') ) : the_row(); ?>
                        <?php $file = get_sub_field('download'); ?>
                        <?php $name = get_sub_field('download_name'); ?>
                        <li>
                            <a href="<?php echo $file["url"];?>">
                                <?php if($name == "") { echo  $file["title"];} else { echo $name; } ?> 
                            </a>
                        </li>				
                    <?php endwhile; endif; ?>
                    </ul>
                <?php } ?>	
        
        <?php ///////////////////////////////////////// DOWNLOADS IF SET TO BUTTONS ; ?>
                <?php if ($download_type == "button") { ?>
                    <?php $add_downloads = get_field('add_downloads'); ?>
                    <?php if( have_rows('downloads_repeater') ): while ( have_rows('downloads_repeater') ) : the_row(); ?>
                        <div style="float:left; margin:10px 20px 10px 0">
                        <?php $file = get_sub_field('download'); ?>
                        <?php $name = get_sub_field('download_name'); ?>
                            <a href="<?php echo $file["url"];?>" class="btn read-more">
                                <?php if($name == "") { echo  $file["title"];} else { echo $name; } ?>  
                            </a>
                        </div>
                    <?php endwhile; endif; ?>
                <?php } ?>
        <?php } ?>        
<?php ///////////////////////////////////////// END DOWNLOADS; ?>
		
			
<?php ///////////////////////////////////////// END COLUMN 2 ; ?>							
<?php echo $col2end; ?>
			
				<?php edit_post_link(); ?>
			
			
<?php ///////////////////////////////////////// VIDEOS; ?>				
			<?php $cols = get_sub_field('video_columns'); ?>
			<?php if( have_rows('video_group') ): ?>
			<div class="row">
				<div class="video-gallery">
				<?php while ( have_rows('video_group') ) : the_row(); ?>
					<?php if($cols == "one") { echo '<div class="col-xs-12">';} ?>
					<?php if($cols == "two") { echo'<div class="col-md-6">'; } ?>
					<?php if($cols == "three") { ?><div class="col-md-4"><?php } ?>
						<div class="embed-container">
							<?php the_sub_field('video'); ?>
						</div>
					</div>
				<?php endwhile; ?>
				</div>
			</div>	
			<?php endif; ?>			
<?php ///////////////////////////////////////// VIDEOS; ?>				
		

		</div><?php // END PANEL BODY; ?>		
<?php wp_reset_postdata(); ?>
</div>
<?php endif; ?>