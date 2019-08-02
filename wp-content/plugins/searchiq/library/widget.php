<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Searchiq_Widget extends WP_Widget {

	function __construct() {
		$options = array( 'classname' => 'searchiq_widget', 'description' => __( 'Searchiq Advanced search widget' ) );
		parent::__construct( 'searchiq_widget', __( 'Searchiq Advanced search widget' ), $options );
	}

	function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		$post_types = $instance['post_types'];
		$showButton = (int)$instance['show_button'];
		
		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;

		$form = '<form role="search" method="get" id="searchiqSearch" action="' . esc_url( home_url( '/' ) ) . '" >
			<div><label class="widget-heading" for="s">' . __( 'Search for:' ) . '</label>
			<input type="text" value="' . get_search_query() . '" name="s" id="siq_s" />
			<input type="hidden" name="post_types" value="'.$post_types.'" id="siq_post_types" />';
			if($showButton){
				$form .= '<input type="submit" id="searchiqSubmit" value="'. esc_attr__( 'Search' ) .'" />';
			}
			$form .='</div>
			</form>';

		echo $form;

		echo $after_widget;
	}

	function form( $instance ) {
		
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'post_types' => array(), 'show_button' => 1 ) );
		$title = $instance['title'];
		$showButton = $instance['show_button'];
		@$post_types = explode(',',$instance['post_types']);
		$buttonChecked = ($showButton == 1) ? ' checked="checked" ' : '';
		
?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><b><?php _e( 'Title:' ); ?></b>
				<input class="regular-text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'post_types' ); ?>"><b><?php _e( 'Allowed post types for search:' ); ?></b></label>
<?php
		$postTypes = array_merge( get_post_types( array( 'exclude_from_search' => '0' ) ), 
					 get_post_types( array( 'exclude_from_search' => false ) ) 
					);
		if($postTypes != ""){		
			$i = 0;
			foreach($postTypes as $k => $v){
				@$checked   = (in_array($k, $post_types)) ? 'checked="checked"' : '';
				echo "<br/><input ".$checked." type='checkbox' name='".$this->get_field_name( 'post_types' )."[]' id='".$this->get_field_name( 'post_types' )."_".$i."' value='".$k."'><label class='postType' for='".$this->get_field_name( 'post_types' )."_".$i."'>".$k."</label>";
				$i++;
			}
		}
					
?>
	
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'show_button' ); ?>"><b><?php _e( 'Show Search Button:' ); ?></b>
				<input type="hidden" name="<?php echo $this->get_field_name( 'show_button' ); ?>" value="0" />
				<input <?php echo $buttonChecked; ?> type="checkbox" name="<?php echo $this->get_field_name( 'show_button' ); ?>" value="1" />
			</label>
		</p>
<?php
	}

	function update( $new_instance, $old_instance ) {
		
		$instance = $old_instance;
		$new_instance = wp_parse_args( (array) $new_instance, array( 'title' => '', 'post_types' => array() ) );
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['post_types'] = implode(',', $new_instance['post_types'] );
		$instance['show_button'] = (int)$new_instance['show_button'];
		
		return $instance;
	}

}
