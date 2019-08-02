<div class="berocket_filter_groups">
    <table>
        <tr>
            <th><?php _e('Custom CSS class', 'BeRocket_AJAX_domain'); ?></th>
            <td>
                <input type="text" name="<?php echo $post_name; ?>[custom_class]" value="<?php echo br_get_value_from_array($filters, 'custom_class'); ?>">
                <small><?php _e('use white space for multiple classes', 'BeRocket_AJAX_domain');?></small>
            </td>
        </tr>
        <?php do_action('berocket_aapf_filters_group_settings', $filters, $post_name, $post); ?>
    </table>
    <h3><?php _e('Filters In Group', 'BeRocket_AJAX_domain'); ?></h3>
    <?php
    $query = new WP_Query(array('post_type' => 'br_product_filter', 'nopaging' => true));
    if ( $query->have_posts() ) {
        echo '<select class="berocket_filter_list">';
        while ( $query->have_posts() ) {
            $query->the_post();
            echo '<option data-name="' . get_the_title() . '" value="' . get_the_id() . '">' . get_the_title() . ' (ID:' . get_the_id() . ')</option>';
        }
        echo '</select>';
        echo ' <a class="button berocket_add_filter_to_group" href="#add_filter">' . __('Add filter', 'BeRocket_AJAX_domain') . '</a>';
        echo ' <a href="' . admin_url('edit.php?post_type=br_product_filter') . '">' . __('Manage filters', 'BeRocket_AJAX_domain') . '</a>';
        wp_reset_postdata();
    }
    ?>
    <ul class="berocket_filter_added_list" data-name="<?php echo $post_name; ?>[filters][]" data-url="<?php echo admin_url('post.php');?>">
    <?php 
    if( isset($filters['filters']) && is_array($filters['filters']) ) {
        foreach($filters['filters'] as $filter) {
            $filter_id = $filter;
            $filter_post = get_post($filter_id);
            if( ! empty($filter_post) ) {
                echo '<li class="berocket_filter_added_' . $filter_id . '"><fa class="fa fa-bars"></fa>
                    <input type="hidden" name="'.$post_name.'[filters][]" value="' . $filter_id . '">
                    ' . $filter_post->post_title . ' <small>ID:' . $filter_id . '</small>
                    <i class="fa fa-times"></i>
                    <a class="berocket_edit_filter fas fa-pencil-alt" target="_blank" href="' . get_edit_post_link($filter_id) . '"></a>
                    <div class="berocket_hidden_clickable_options">
                        ' . __('Width', 'BeRocket_AJAX_domain') . '<input type="text" name="'.$post_name.'[filters_data][' . $filter_id . '][width]" value="' . br_get_value_from_array($filters, array('filters_data', $filter_id, 'width')) . '" placeholder="100%">
                    </div>
                </li>';
            }
        }
    }
    ?>
    </ul>
</div>
<script>
    jQuery(document).on('click', '.berocket_add_filter_to_group', function(event) {
        event.preventDefault();
        if( ! jQuery('.berocket_filter_added_'+jQuery('.berocket_filter_list').val()).length ) {
            var html = '<li class="berocket_filter_added_'+jQuery('.berocket_filter_list').val()+'"><i class="fa fa-bars"></i> ';
            html += '<input type="hidden" name="'+jQuery('.berocket_filter_added_list').data('name')+'" value="'+jQuery('.berocket_filter_list').val()+'">';
            html += jQuery('.berocket_filter_list').find(':selected').data('name');
            html += ' <small>ID:'+jQuery('.berocket_filter_list').val()+'</small>';
            html += '<i class="fa fa-times"></i>';
            html += ' <a class="berocket_edit_filter fas fa-pencil-alt" target="_blank" href="'+jQuery('.berocket_filter_added_list').data('url')+'?post='+jQuery('.berocket_filter_list').val()+'&action=edit"></a>';
            html += '<div class="berocket_hidden_clickable_options">';
            html += '<?php _e('Width', 'BeRocket_AJAX_domain'); ?><input type="text" name="<?php echo $post_name; ?>[filters_data]['+jQuery('.berocket_filter_list').val()+'][width]" placeholder="100%" value="">';
            html += '</div>';
            html += '</li>';
            jQuery('.berocket_filter_added_list').append(jQuery(html));
        } else {
            jQuery('.berocket_filter_added_'+jQuery('.berocket_filter_list').val()).css('background-color', '#ee3333').clearQueue().animate({backgroundColor:'#eeeeee'}, 1000);
        }
    });
    jQuery(document).on('click', '.berocket_filter_added_list .fa-times', function(event) {
        jQuery(this).parents('li').first().remove();
    });
    jQuery(document).ready(function() {
        if(typeof(jQuery( ".berocket_filter_added_list" ).sortable) == 'function') {
            jQuery( ".berocket_filter_added_list" ).sortable({axis:"y", handle:".fa-bars", placeholder: "berocket_sortable_space"});
        }
    });
</script>
<style>
.button.berocket_add_filter_to_group {
    margin-right: 8px;
    margin-left: 5px;
}
.berocket_filter_added_list li {
    font-size: 2em;
    border: 1px solid #2c3b48;
    padding: 0;
    line-height: 40px;
    height: 40px;
    border-right-width: 3px;
    background-color: rgb(238, 238, 238);
}
.berocket_filter_added_list li .fa-bars {
    margin-right: 0.5em;
    cursor: move;
    background-color: #2c3b48;
    line-height: 41px;
    padding: 0 5px;
    color: white;
    font-size: 16px;
    position: relative;
    top: -3px;
}
.berocket_filter_added_list small {
    font-size: 0.5em;
    vertical-align: middle;
}
.berocket_filter_added_list li .fa-times {
    margin-left: 0.5em;
    margin-right: 0.5em;
    cursor: pointer;
    float: right;
    line-height: 40px;
    font-size: 16px;
    position: relative;
    top: 1px;
}
.berocket_filter_added_list .berocket_edit_filter {
    vertical-align: middle;
    font-size: 0.6em;
    float: right;
    line-height: 40px;
    display: inline-block;
    color: #2c3b48;
    margin-left: 0.5em;
    margin-right: 0.5em;
}
.berocket_filter_added_list li .fa-times:hover,
.berocket_filter_added_list .berocket_edit_filter:hover {
    color: black;
}
.berocket_filter_added_list .berocket_sortable_space {
    border: 2px dashed #aaa;
    background: white;
    font-size: 2em;
    height: 1.1em;
    box-sizing: content-box;
    padding: 5px;
}
.berocket_filter_groups {
    margin-top: 20px;
}
.berocket_filter_added_list .berocket_hidden_clickable_options {
    font-size: 12px;
    float: right;
    margin-right: 10px;
    display: none;
}
.berocket_hidden_clickable_options input{
    width: 100px;
}
.berocket_filter_added_list.berocket_hidden_clickable_enabled .berocket_hidden_clickable_options {
    display: inline-block;
}
@media screen and (max-width: 600px) {
    .berocket_filter_added_list small,
    .berocket_filter_added_list .berocket_edit_filter {
        display: none;
    }
    .berocket_filter_added_list li {
        position: relative;
    }
    .berocket_filter_added_list li .fa-times {
        position: absolute;
        top: 1px;
        right: 0;
        background-color: rgb(238, 238, 238);
        margin: 0;
        padding: 0 10px;
        line-height: 38px;
    }
}
</style>
