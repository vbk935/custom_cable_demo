<?php
$display_amount = get_sub_field('display_amount'); 
    //$types = get_sub_field('types');
//$types = implode(', ', get_sub_field('types', 'option')); 
?>
    <ul class="">
        <?php
        $args = array( 
            'post_type' => 'post',
             'posts_per_page' => $display_amount, 
             'post_status' => 'publish',
        );
        $recent_posts = wp_get_recent_posts( $args );
        foreach( $recent_posts as $recent ){
            echo '<li><a href="' . get_permalink($recent["ID"]) . '" title="'.esc_attr($recent["post_title"]).'" >' .   $recent["post_title"].'</a> </li> ';
        } ?>
    </ul>